<?php

namespace App\Imports\BarangKI;

use App\Models\Barang\BarangKI;
use App\Models\Barang\BarangModel;
use App\Models\Barang\SatuanItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

/**
 * Import Barang KI dari format Excel user:
 *
 * Kolom (header di baris 1):
 *   KODE_BARANG | KODE_BARCODE | NAMA | KATEGORI | SUB_KATEGORI | BRAND |
 *   TIPE_BARANG | SATUAN_1 | ISI_1 | SATUAN_2 | ISI_2 | SATUAN_3 | ISI_3 |
 *   HPP | HARGA_JUAL | PEMBELI | EARLY_EXPIRED | MID_EXPIRED | LAST_EXPIRED
 */
class BarangKiImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected array $errors       = [];
    protected int   $successCount = 0;
    protected int   $skippedCount = 0;
    protected ?string $importId;

    /** Cache SKU → BarangModel untuk menghindari query berulang */
    protected array $barangCache = [];

    /** Cache nama/cut_name satuan (uppercase) → SatuanItem */
    protected array $satuanCache = [];

    public function __construct(?string $importId = null)
    {
        $this->importId = $importId;
    }

    /* ------------------------------------------------------------------ */
    /*  MAIN PROCESSING                                                     */
    /* ------------------------------------------------------------------ */

    public function collection(Collection $collection): void
    {
        $totalRows = $collection->count();

        Log::info('BarangKiImport: mulai memproses', [
            'import_id'  => $this->importId,
            'total_rows' => $totalRows,
        ]);

        // Preload lookup tables ke memori
        $this->preloadCache();

        $processedRows = 0;

        foreach ($collection as $index => $row) {
            $rowNumber = $index + 2; // baris 1 = header, data mulai baris 2

            try {
                if ($this->isRowEmpty($row)) {
                    $this->skippedCount++;
                    continue;
                }

                $validatedData = $this->validateRow($row->toArray(), $rowNumber);

                if ($validatedData === null) {
                    $processedRows++;
                    continue;
                }

                // Cek duplikasi barcode
                if (BarangKI::where('id_barcode', $validatedData['id_barcode'])->exists()) {
                    $this->addError($rowNumber, 'KODE_BARCODE sudah ada: ' . $validatedData['id_barcode']);
                    $processedRows++;
                    continue;
                }

                // Update expiry threshold di barang master (opsional)
                $this->updateBarangExpiry($validatedData);

                // Buat data barang KI (hilangkan field private _*)
                $kiData = array_filter(
                    $validatedData,
                    fn($key) => !str_starts_with($key, '_'),
                    ARRAY_FILTER_USE_KEY
                );

                BarangKI::create($kiData);
                $this->successCount++;

                Log::info('BarangKiImport: baris berhasil', [
                    'import_id'  => $this->importId,
                    'row'        => $rowNumber,
                    'id_barcode' => $validatedData['id_barcode'],
                ]);

            } catch (\Exception $e) {
                $this->addError($rowNumber, 'Exception: ' . $e->getMessage());
                Log::error('BarangKiImport: exception baris', [
                    'import_id' => $this->importId,
                    'row'       => $rowNumber,
                    'message'   => $e->getMessage(),
                    'trace'     => $e->getTraceAsString(),
                ]);
            }

            $processedRows++;

            if ($this->importId && $totalRows > 0) {
                $progress = ($processedRows / $totalRows) * 100;
                cache()->put("import_barang_ki_progress_{$this->importId}", round($progress), 3600);
            }
        }

        Log::info('BarangKiImport: selesai', [
            'import_id'     => $this->importId,
            'success_count' => $this->successCount,
            'skipped_count' => $this->skippedCount,
            'error_count'   => count($this->errors),
            'errors'        => $this->errors,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  VALIDATION & MAPPING                                                */
    /* ------------------------------------------------------------------ */

    protected function validateRow(array $row, int $rowNumber): ?array
    {
        // ── Baca kolom dari format Excel user ──────────────────────────
        $kodeBarang  = strtoupper(trim($row['kode_barang']  ?? ''));
        $kodeBarcode = trim($row['kode_barcode'] ?? '');
        $satuan1Key  = strtoupper(trim($row['satuan_1']     ?? ''));
        $isi1        = $row['isi_1']        ?? null;
        $hpp         = $row['hpp']          ?? null;
        $hargaJual   = $row['harga_jual']   ?? null;
        $earlyExp    = $row['early_expired'] ?? null;
        $midExp      = $row['mid_expired']   ?? null;
        $lastExp     = $row['last_expired']  ?? null;

        // ── Lookup KODE_BARANG → barang_id ─────────────────────────────
        $barang = $this->barangCache[$kodeBarang] ?? null;
        if (!$barang) {
            $this->addError($rowNumber, "KODE_BARANG '{$kodeBarang}' tidak ditemukan. "
                . "Pastikan SKU/kode barang sudah terdaftar di sistem.");
            return null;
        }

        // ── Lookup SATUAN_1 → satuan_id ────────────────────────────────
        $satuan = $this->satuanCache[$satuan1Key] ?? null;
        if (!$satuan) {
            $this->addError($rowNumber, "SATUAN_1 '{$satuan1Key}' tidak ditemukan. "
                . "Gunakan nama satuan atau kode singkat yang terdaftar (misal: PCS, KG, LTR).");
            return null;
        }

        // ── Validasi field wajib ───────────────────────────────────────
        $data = [
            'barang_id'    => $barang->id,
            'id_barcode'   => $kodeBarcode,
            'satuan_id'    => $satuan->id,
            'quantity'     => $isi1,
            'sold_quantity' => 0,
            'price_buy'    => $hpp,
            'price_sell'   => $hargaJual,
            'status'       => 'active',
            // Field private untuk update barang master (tidak disimpan ke barang_ki)
            '_barang_id'     => $barang->id,
            '_early_expired' => is_numeric($earlyExp) ? (int) $earlyExp : null,
            '_mid_expired'   => is_numeric($midExp)   ? (int) $midExp   : null,
            '_last_expired'  => is_numeric($lastExp)  ? (int) $lastExp  : null,
        ];

        $validator = Validator::make($data, [
            'id_barcode'   => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0',
            'sold_quantity' => 'numeric|min:0',
            'price_buy'    => 'required|numeric|min:0',
            'price_sell'   => 'required|numeric|min:0',
            'status'       => 'in:active,inactive',
        ], [
            'id_barcode.required'   => 'KODE_BARCODE wajib diisi',
            'quantity.required'     => 'ISI_1 (jumlah stok) wajib diisi',
            'quantity.numeric'      => 'ISI_1 harus berupa angka',
            'price_buy.required'    => 'HPP wajib diisi',
            'price_buy.numeric'     => 'HPP harus berupa angka',
            'price_sell.required'   => 'HARGA_JUAL wajib diisi',
            'price_sell.numeric'    => 'HARGA_JUAL harus berupa angka',
        ]);

        if ($validator->fails()) {
            $this->addError($rowNumber, implode(', ', $validator->errors()->all()));
            return null;
        }

        return $data;
    }

    /* ------------------------------------------------------------------ */
    /*  HELPERS                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Preload semua barang dan satuan ke cache memori untuk efisiensi.
     */
    protected function preloadCache(): void
    {
        BarangModel::select('id', 'sku', 'early_expiry_days', 'mid_expiry_days', 'late_expiry_days')
            ->get()
            ->each(function (BarangModel $b) {
                $this->barangCache[strtoupper(trim($b->sku))] = $b;
            });

        SatuanItem::select('id', 'name', 'cut_name')
            ->get()
            ->each(function (SatuanItem $s) {
                $this->satuanCache[strtoupper(trim($s->name))] = $s;
                if ($s->cut_name) {
                    $this->satuanCache[strtoupper(trim($s->cut_name))] = $s;
                }
            });

        Log::info('BarangKiImport: cache preloaded', [
            'barang_count' => count($this->barangCache),
            'satuan_count' => count($this->satuanCache),
        ]);
    }

    /**
     * Update expiry thresholds barang master dari kolom EARLY/MID/LAST_EXPIRED.
     */
    protected function updateBarangExpiry(array $data): void
    {
        $barangId = $data['_barang_id'] ?? null;
        if (!$barangId) return;

        $updates = array_filter([
            'early_expiry_days' => $data['_early_expired'] ?? null,
            'mid_expiry_days'   => $data['_mid_expired']   ?? null,
            'late_expiry_days'  => $data['_last_expired']  ?? null,
        ], fn($v) => $v !== null);

        if (!empty($updates)) {
            BarangModel::where('id', $barangId)->update($updates);
        }
    }

    /**
     * Baris dianggap kosong jika KODE_BARANG dan KODE_BARCODE keduanya kosong.
     */
    protected function isRowEmpty(Collection $row): bool
    {
        return empty(trim($row['kode_barang']  ?? ''))
            && empty(trim($row['kode_barcode'] ?? ''));
    }

    protected function addError(int $row, string $message): void
    {
        $this->errors[] = "Baris {$row}: {$message}";

        Log::warning('BarangKiImport: error baris', [
            'import_id' => $this->importId,
            'row'       => $row,
            'message'   => $message,
        ]);

        if ($this->importId) {
            cache()->put("import_barang_ki_errors_{$this->importId}", $this->errors, 3600);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  ACCESSORS                                                           */
    /* ------------------------------------------------------------------ */

    public function getErrors(): array    { return $this->errors; }
    public function getSuccessCount(): int { return $this->successCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }

    public function batchSize(): int  { return 100; }
    public function chunkSize(): int  { return 100; }

    /**
     * Header ada di baris 1 (sesuai format Excel user).
     */
    public function headingRow(): int { return 1; }
}
