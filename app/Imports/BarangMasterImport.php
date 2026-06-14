<?php

namespace App\Imports;

use App\Models\Barang\BarangKI;
use App\Models\Barang\BarangModel;
use App\Models\Barang\Brand;
use App\Models\Barang\Category;
use App\Models\Barang\SatuanConversion;
use App\Models\Barang\SatuanItem;
use App\Models\Barang\Subcategory;
use App\Models\Barang\TypeItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import Barang Master dari format Excel:
 *
 * Kolom (header baris 1):
 *   KODE_BARANG | KODE_BARCODE | NAMA | KATEGORI | SUB_KATEGORI | BRAND |
 *   TIPE_BARANG | SATUAN_1 | ISI_1 | SATUAN_2 | ISI_2 | SATUAN_3 | ISI_3 |
 *   HPP | HARGA_JUAL | PEMBELI | EARLY_EXPIRED | MID_EXPIRED | LAST_EXPIRED
 */
class BarangMasterImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected array  $errors       = [];
    protected int    $successCount = 0;
    protected int    $skippedCount = 0;
    protected int    $updatedCount = 0;

    /** Cache lookup: key(uppercase) → model instance */
    protected array $categoryCache    = [];
    protected array $subcategoryCache = [];
    protected array $brandCache       = [];
    protected array $typeCache        = [];
    protected array $satuanCache      = [];

    /* ------------------------------------------------------------------ */
    /*  MAIN PROCESSING                                                     */
    /* ------------------------------------------------------------------ */

    public function collection(Collection $collection): void
    {
        Log::info('BarangMasterImport: mulai', ['total_rows' => $collection->count()]);

        $this->preloadCache();

        foreach ($collection as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $arr = $row->toArray();

                if ($this->isRowEmpty($arr)) {
                    $this->skippedCount++;
                    continue;
                }

                $this->processRow($arr, $rowNumber);

            } catch (\Throwable $e) {
                $this->addError($rowNumber, 'Exception: ' . $e->getMessage());
                Log::error('BarangMasterImport: exception', [
                    'row'     => $rowNumber,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info('BarangMasterImport: selesai', [
            'success' => $this->successCount,
            'updated' => $this->updatedCount,
            'skipped' => $this->skippedCount,
            'errors'  => count($this->errors),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  ROW PROCESSING                                                      */
    /* ------------------------------------------------------------------ */

    protected function processRow(array $row, int $rowNumber): void
    {
        // ── Ambil nilai kolom ────────────────────────────────────────────
        $sku         = strtoupper(trim((string)($row['kode_barang']  ?? '')));
        $barcode     = trim((string)($row['kode_barcode'] ?? ''));
        $nama        = trim($row['nama']          ?? '');
        $kategori    = ucwords(strtolower(trim($row['kategori']    ?? '')));
        $subKategori = ucwords(strtolower(trim($row['sub_kategori'] ?? '')));
        $brandName   = trim($row['brand']         ?? '');
        $tipeName    = trim($row['tipe_barang']   ?? '');
        $satuan1     = strtoupper(trim($row['satuan_1'] ?? ''));
        $isi1        = is_numeric($row['isi_1'] ?? null) ? (float)$row['isi_1'] : null;
        $satuan2     = strtoupper(trim($row['satuan_2'] ?? ''));
        $isi2        = is_numeric($row['isi_2'] ?? null) ? (float)$row['isi_2'] : null;
        $satuan3     = strtoupper(trim($row['satuan_3'] ?? ''));
        $isi3        = is_numeric($row['isi_3'] ?? null) ? (float)$row['isi_3'] : null;
        $earlyExp    = is_numeric($row['early_expired'] ?? null) ? (int)$row['early_expired'] : null;
        $midExp      = is_numeric($row['mid_expired']   ?? null) ? (int)$row['mid_expired']   : null;
        $lastExp     = is_numeric($row['last_expired']  ?? null) ? (int)$row['last_expired']  : null;

        // ── Parse HPP & HARGA_JUAL (hapus titik sebagai pemisah ribuan) ──
        $hppRaw      = trim((string)($row['hpp']        ?? ''));
        $jualRaw     = trim((string)($row['harga_jual'] ?? ''));
        $hpp         = $this->parseCurrency($hppRaw);
        $hargaJual   = $this->parseCurrency($jualRaw);

        // ── Validasi wajib ───────────────────────────────────────────────
        if (empty($sku)) {
            $this->addError($rowNumber, 'KODE_BARANG tidak boleh kosong');
            return;
        }
        if (empty($nama)) {
            $this->addError($rowNumber, 'NAMA tidak boleh kosong');
            return;
        }

        // ── Lookup / auto-create Brand ───────────────────────────────────
        $brand = null;
        if (!empty($brandName)) {
            $brandKey = strtoupper($brandName);
            if (!isset($this->brandCache[$brandKey])) {
                $this->brandCache[$brandKey] = Brand::firstOrCreate(
                    ['name' => $brandName],
                    ['description' => '']
                );
            }
            $brand = $this->brandCache[$brandKey];
        } else {
            $this->addError($rowNumber, "BRAND kosong pada baris {$rowNumber}. BRAND wajib diisi.");
            return;
        }

        // ── Lookup TypeItem ──────────────────────────────────────────────
        $type = null;
        if (!empty($tipeName)) {
            $typeKey = strtoupper($tipeName);
            if (isset($this->typeCache[$typeKey])) {
                $type = $this->typeCache[$typeKey];
            } else {
                $this->addError($rowNumber, "TIPE_BARANG '{$tipeName}' tidak ditemukan. Pastikan tipe barang sudah terdaftar: Harian, Mingguan, Bulanan, dll.");
                return;
            }
        }

        // ── Lookup Subcategory (via Kategori + SubKategori) ──────────────
        $subcategory = null;
        if (!empty($subKategori)) {
            $subKey = strtoupper($subKategori);
            if (isset($this->subcategoryCache[$subKey])) {
                $subcategory = $this->subcategoryCache[$subKey];
            } else {
                // Coba cari kategori dulu
                $catKey  = strtoupper($kategori);
                $category = $this->categoryCache[$catKey] ?? null;

                if (!$category && !empty($kategori)) {
                    $category = Category::firstOrCreate(
                        ['name' => $kategori],
                        ['description' => '']
                    );
                    $this->categoryCache[$catKey] = $category;
                }

                if ($category) {
                    $subcategory = Subcategory::firstOrCreate(
                        ['name' => $subKategori, 'category_id' => $category->id],
                        ['margin' => 0]
                    );
                    $this->subcategoryCache[$subKey] = $subcategory;
                } else {
                    $this->addError($rowNumber, "SUB_KATEGORI '{$subKategori}' tidak dapat dibuat karena KATEGORI kosong.");
                    return;
                }
            }
        }

        // ── Lookup SATUAN_1 (wajib ada di satuan_items) ──────────────────
        $satuanObj1 = null;
        if (!empty($satuan1)) {
            $satuanObj1 = $this->satuanCache[$satuan1] ?? null;
            if (!$satuanObj1) {
                $this->addError($rowNumber, "SATUAN_1 '{$satuan1}' tidak ditemukan di tabel satuan_items. Tambahkan terlebih dahulu.");
                return;
            }
        }

        // ── Cek apakah SKU sudah ada (update) ────────────────────────────
        $existing = BarangModel::withTrashed()->where('sku', $sku)->first();

        DB::beginTransaction();
        try {
            if ($existing) {
                // UPDATE data barang yang sudah ada
                $existing->update(array_filter([
                    'name'             => $nama,
                    'brand_id'         => $brand ? $brand->id : $existing->brand_id,
                    'type_id'          => $type  ? $type->id  : $existing->type_id,
                    'subcategory_id'   => $subcategory ? $subcategory->id : $existing->subcategory_id,
                    'early_expiry_days'=> $earlyExp,
                    'mid_expiry_days'  => $midExp,
                    'late_expiry_days' => $lastExp,
                ], fn ($v) => $v !== null));

                $barang = $existing;
                $this->updatedCount++;

                Log::info('BarangMasterImport: update barang', ['sku' => $sku, 'row' => $rowNumber]);
            } else {
                // CREATE barang baru
                $barang = BarangModel::create([
                    'sku'              => $sku,
                    'name'             => $nama,
                    'brand_id'         => $brand?->id,
                    'type_id'          => $type?->id,
                    'subcategory_id'   => $subcategory?->id,
                    'description'      => '-',
                    'early_expiry_days'=> $earlyExp,
                    'mid_expiry_days'  => $midExp,
                    'late_expiry_days' => $lastExp,
                    'status'           => 'active',
                ]);

                $this->successCount++;
                Log::info('BarangMasterImport: created barang', ['sku' => $sku, 'row' => $rowNumber]);
            }

            // ── Simpan konversi satuan ───────────────────────────────────
            $this->saveConversions($barang, [
                ['satuan_key' => $satuan1, 'isi' => $isi1, 'obj' => $satuanObj1],
                ['satuan_key' => $satuan2, 'isi' => $isi2, 'obj' => $this->satuanCache[$satuan2] ?? null],
                ['satuan_key' => $satuan3, 'isi' => $isi3, 'obj' => $this->satuanCache[$satuan3] ?? null],
            ], $rowNumber);

            // ── Simpan / update BarangKI (harga referensi) ───────────────
            if ($satuanObj1 && ($hpp !== null || $hargaJual !== null || !empty($barcode))) {
                $this->saveBarangKI($barang, $satuanObj1, $barcode, $hpp, $hargaJual, $rowNumber);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  BARANG KI (harga referensi)                                         */
    /* ------------------------------------------------------------------ */

    /**
     * Simpan atau update record BarangKI sebagai data harga referensi.
     * Dicocokkan berdasarkan barang_id + satuan_id (atau id_barcode jika ada).
     * quantity = 0, status = 'waiting' (belum ada stok fisik).
     */
    protected function saveBarangKI(
        BarangModel $barang,
        SatuanItem  $satuan,
        string      $barcode,
        ?float      $hpp,
        ?float      $hargaJual,
        int         $rowNumber
    ): void {
        try {
            // Cari record yang cocok: barang_id + satuan_id
            $ki = BarangKI::withTrashed()
                ->where('barang_id', $barang->id)
                ->where('satuan_id', $satuan->id)
                ->first();

            $hppVal  = $hpp      ?? 0;
            $jualVal = $hargaJual ?? $hppVal;

            if ($ki) {
                // Update harga & barcode saja, jangan ubah quantity/status
                $updateData = [];
                if (!empty($barcode))  $updateData['id_barcode']  = $barcode;
                if ($hpp !== null)     $updateData['price_buy']   = $hppVal;
                if ($hargaJual !== null) $updateData['price_sell'] = $jualVal;

                if (!empty($updateData)) {
                    $ki->update($updateData);
                }
            } else {
                BarangKI::create([
                    'barang_id'   => $barang->id,
                    'satuan_id'   => $satuan->id,
                    'id_barcode'  => !empty($barcode) ? $barcode : null,
                    'quantity'    => 0,
                    'sold_quantity' => 0,
                    'price_buy'   => $hppVal,
                    'price_sell'  => $jualVal,
                    'price_up'    => $jualVal,
                    'status'      => 'waiting',
                ]);
            }

            Log::info('BarangMasterImport: barang_ki saved', [
                'barang_id' => $barang->id,
                'sku'       => $barang->sku,
                'barcode'   => $barcode,
                'hpp'       => $hppVal,
                'harga_jual'=> $jualVal,
                'row'       => $rowNumber,
            ]);

        } catch (\Throwable $e) {
            // Jangan gagalkan seluruh baris, cukup log warning
            Log::warning('BarangMasterImport: gagal simpan barang_ki', [
                'barang_id' => $barang->id,
                'row'       => $rowNumber,
                'message'   => $e->getMessage(),
            ]);
            $this->addError($rowNumber, "Peringatan: data harga/barcode tidak tersimpan – {$e->getMessage()}");
        }
    }

    /* ------------------------------------------------------------------ */
    /*  SATUAN CONVERSION                                                   */
    /* ------------------------------------------------------------------ */

    /**
     * Simpan konversi satuan ke tabel satuan_conversions.
     * Konversi disimpan dari satuan terbesar ke satuan sebelumnya.
     * Contoh: Slop→Kotak (isi_1=10 means 1 Slop = 10 Kotak).
     */
    protected function saveConversions(BarangModel $barang, array $satuans, int $rowNumber): void
    {
        // Hapus konversi lama untuk barang ini sebelum buat baru
        SatuanConversion::where('barang_id', $barang->id)->delete();

        // Filter yang terisi dengan satuan yang valid
        $valid = array_filter($satuans, fn($s) => !empty($s['satuan_key']) && $s['obj'] !== null && $s['isi'] !== null);
        $valid = array_values($valid);

        // Buat konversi berpasangan: satuan[i] → satuan[i+1] dengan faktor isi[i+1]
        for ($i = 0; $i < count($valid) - 1; $i++) {
            $from   = $valid[$i]['obj'];
            $next   = $valid[$i + 1];
            $toObj  = $next['obj'];
            $factor = $next['isi'];

            if (!$from || !$toObj || !$factor) continue;

            // Forward: from → to
            SatuanConversion::firstOrCreate(
                [
                    'barang_id'      => $barang->id,
                    'from_satuan_id' => $from->id,
                    'to_satuan_id'   => $toObj->id,
                ],
                ['conversion_factor' => $factor]
            );

            // Reverse: to → from (1/factor)
            SatuanConversion::firstOrCreate(
                [
                    'barang_id'      => $barang->id,
                    'from_satuan_id' => $toObj->id,
                    'to_satuan_id'   => $from->id,
                ],
                ['conversion_factor' => round(1 / $factor, 6)]
            );
        }
    }

    /* ------------------------------------------------------------------ */
    /*  CACHE PRELOAD                                                       */
    /* ------------------------------------------------------------------ */

    protected function preloadCache(): void
    {
        // Categories
        Category::all()->each(function ($c) {
            $this->categoryCache[strtoupper(trim($c->name))] = $c;
        });

        // Subcategories
        Subcategory::all()->each(function ($s) {
            $this->subcategoryCache[strtoupper(trim($s->name))] = $s;
        });

        // Brands
        Brand::all()->each(function ($b) {
            $this->brandCache[strtoupper(trim($b->name))] = $b;
        });

        // Type Items
        TypeItem::all()->each(function ($t) {
            $this->typeCache[strtoupper(trim($t->name))] = $t;
        });

        // Satuan Items (by name & cut_name)
        SatuanItem::all()->each(function ($s) {
            $this->satuanCache[strtoupper(trim($s->name))] = $s;
            if ($s->cut_name) {
                $this->satuanCache[strtoupper(trim($s->cut_name))] = $s;
            }
        });

        Log::info('BarangMasterImport: cache preloaded', [
            'categories'    => count($this->categoryCache),
            'subcategories' => count($this->subcategoryCache),
            'brands'        => count($this->brandCache),
            'types'         => count($this->typeCache),
            'satuans'       => count($this->satuanCache),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  HELPERS                                                             */
    /* ------------------------------------------------------------------ */

    protected function isRowEmpty(array $row): bool
    {
        return empty(trim((string)($row['kode_barang'] ?? '')))
            && empty(trim($row['nama'] ?? ''));
    }

    /**
     * Parse nilai mata uang: "36.500" → 36500.0, "39000" → 39000.0
     * Menangani format ribuan dengan titik (ID) atau koma (EN).
     */
    protected function parseCurrency(string $value): ?float
    {
        $value = trim($value);
        if ($value === '' || $value === '-') return null;

        // Hapus karakter non-numerik kecuali titik, koma, dan minus
        $cleaned = preg_replace('/[^\d.,\-]/', '', $value);

        // Deteksi format: jika ada titik sebagai pemisah ribuan (contoh: 36.500)
        // tanda: titik diikuti tepat 3 digit dan tidak ada koma sesudah titik
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $cleaned)) {
            // Format Indonesia: 36.500 = 36500
            $cleaned = str_replace('.', '', $cleaned);
        } elseif (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
            // Format: 1.234,56 → 1234.56
            $cleaned = str_replace(['.', ','], ['', '.'], $cleaned);
        } else {
            // Format biasa atau desimal dengan koma: 36500 atau 36500,50
            $cleaned = str_replace(',', '.', $cleaned);
        }

        return is_numeric($cleaned) ? (float)$cleaned : null;
    }

    protected function addError(int $row, string $message): void
    {
        $this->errors[] = "Baris {$row}: {$message}";
        Log::warning('BarangMasterImport: error', ['row' => $row, 'message' => $message]);
    }

    /* ------------------------------------------------------------------ */
    /*  ACCESSORS                                                           */
    /* ------------------------------------------------------------------ */

    public function getErrors(): array     { return $this->errors; }
    public function getSuccessCount(): int { return $this->successCount; }
    public function getUpdatedCount(): int { return $this->updatedCount; }
    public function getSkippedCount(): int { return $this->skippedCount; }

    public function batchSize(): int  { return 100; }
    public function chunkSize(): int  { return 100; }
    public function headingRow(): int { return 1; }
}
