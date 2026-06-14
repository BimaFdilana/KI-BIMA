<?php

namespace App\Exports\BarangKI;

use App\Models\Barang\BarangKI;
use App\Services\Barang\ConvertSatuanService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Str;

class BarangKIExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;
    protected $convertService;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->convertService = app(ConvertSatuanService::class);
    }

    /**
     * Ambil data dari query yang sama seperti di DataTable
     */
    public function collection()
    {
        $query = BarangKI::withTrashed()
            ->with(['barang', 'barang.subcategory.category', 'barang.brand', 'barang.type', 'satuan'])
            ->selectRaw('
                MIN(id_barcode) as id_barcode,
                barang_id,
                expired_time,
                SUM(quantity) as quantity,
                SUM(sold_quantity) as sold_quantity,
                MAX(price_buy) as price_buy,
                MAX(price_sell) as price_sell,
                MIN(discount_start) as discount_start,
                MIN(discount_end) as discount_end,
                MAX(discount_amount) as discount_amount,
                MAX(discount_percentage) as discount_percentage
            ')
            ->groupBy('barang_id', 'expired_time');

        $this->applyFilters($query);

        return $query->get();
    }

    /**
     * Terapkan filter yang sama seperti di DataTable
     */
    private function applyFilters($query)
    {
        // Filter diskon
        if (isset($this->filters['discount'])) {
            $discount = $this->filters['discount'];
            if ($discount === 'ongoing') {
                $query->where('discount_start', '<=', now())->where('discount_end', '>=', now());
            } elseif ($discount === 'coming') {
                $query->where('discount_start', '>', now());
            } elseif ($discount === 'expired_discount') {
                $query->where('discount_end', '<', now())->whereNotNull('discount_start');
            } elseif ($discount === 'no_discount') {
                $query->whereNull('discount_amount')->whereNull('discount_percentage');
            }
        }

        // Filter status (deleted/active)
        if (isset($this->filters['status'])) {
            $status = $this->filters['status'];
            if ($status === 'deleted') {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }
        }

        // Filter expired
        if (isset($this->filters['expired'])) {
            $expired = $this->filters['expired'];
            switch ($expired) {
                case 'no_expiry':
                    $query->whereNull('expired_time');
                    break;
                case 'fresh':
                    $query->whereHas('barang', fn($q) => $q->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.early_expiry_days DAY)'));
                    break;
                case 'early_expiry':
                    $query->whereHas('barang', fn($q) => $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.early_expiry_days DAY)')
                        ->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)'));
                    break;
                case 'mid_expiry':
                    $query->whereHas('barang', fn($q) => $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)')
                        ->whereRaw('DATE(expired_time) > DATE(NOW() + INTERVAL barang.late_expiry_days DAY)'));
                    break;
                case 'late_expiry':
                    $query->whereHas('barang', fn($q) => $q->whereRaw('DATE(expired_time) <= DATE(NOW() + INTERVAL barang.late_expiry_days DAY)')
                        ->whereRaw('DATE(expired_time) > DATE(NOW())'));
                    break;
                case 'expired':
                    $query->whereRaw('DATE(expired_time) < DATE(NOW())');
                    break;
            }
        }

        // Filter stok
        if (isset($this->filters['stock'])) {
            $stock = $this->filters['stock'];
            if ($stock === 'available') {
                $query->havingRaw('SUM(quantity) > SUM(sold_quantity)');
            } elseif ($stock === 'out_of_stock') {
                $query->havingRaw('SUM(quantity) <= SUM(sold_quantity)');
            } elseif ($stock === 'low_stock') {
                $query->havingRaw('(SUM(quantity) - SUM(sold_quantity)) <= 10 AND (SUM(quantity) - SUM(sold_quantity)) > 0');
            }
        }

        // Filter subkategori
        if (isset($this->filters['subcategory'])) {
            $query->whereHas('barang', fn($q) => $q->where('subcategory_id', $this->filters['subcategory']));
        }

        // Filter harga
        if (isset($this->filters['price_min'])) {
            $query->havingRaw('MAX(price_sell) >= ?', [$this->filters['price_min']]);
        }
        if (isset($this->filters['price_max'])) {
            $query->havingRaw('MAX(price_sell) <= ?', [$this->filters['price_max']]);
        }

        return $query;
    }

    /**
     * Header kolom
     */
    public function headings(): array
    {
        return [
            'ID PRODUCT',
            'Nama Barang',
            'Kategori',
            'Sub Kategori',
            'Brand',
            'Type',
            'Satuan',
            'Stok',
            'Harga Beli',
            'Harga Jual',
            'Diskon',
            'Expired',
            'Status Barang',
            'Status Diskon',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * Mapping data per row
     */
    /**
     * Mapping data per row
     */
    public function map($barangKi): array
    {
        // Hitung stok tersedia
        $sisaStok = $barangKi->quantity - $barangKi->sold_quantity;
        $convertToSmall = $this->convertService->convertBarangKeTerkecilDatatables([$barangKi]);
        $formattedStock = $this->convertService->convertStock($barangKi, $convertToSmall['total']);
        $stokDisplay = is_string($formattedStock) ? $formattedStock : implode(', ', $formattedStock['formatted'] ?? ['0']);

        // Status diskon
        $diskonStatus = 'Tidak Ada';
        if ($barangKi->discount_amount || $barangKi->discount_percentage) {
            $now = now();
            if ($barangKi->discount_start && $barangKi->discount_end) {
                if ($barangKi->discount_start <= $now && $now <= $barangKi->discount_end) {
                    $diskonStatus = 'Berlangsung';
                } elseif ($barangKi->discount_start > $now) {
                    $diskonStatus = 'Akan Datang';
                } else {
                    $diskonStatus = 'Kadaluarsa';
                }
            } else {
                $diskonStatus = 'Data Tidak Lengkap';
            }
        }

        // Detail diskon
        $diskonDetail = 'Tidak Ada';
        if ($barangKi->discount_amount) {
            $diskonDetail = 'Rp' . number_format($barangKi->discount_amount, 0, ',', '.');
        } elseif ($barangKi->discount_percentage) {
            $diskonDetail = $barangKi->discount_percentage . '%';
        }

        // Status barang
        $statusBarang = $barangKi->deleted_at ? 'Dihapus' : 'Aktif';

        // ✅ Perbaiki: Cek null untuk expired_time, created_at, updated_at
        $expiredDate = $barangKi->expired_time
            ? \Carbon\Carbon::parse($barangKi->expired_time)->format('Y-m-d')
            : 'Tidak Ada';

        $createdAt = $barangKi->created_at
            ? \Carbon\Carbon::parse($barangKi->created_at)->format('Y-m-d H:i:s')
            : 'Tidak Ada';

        $updatedAt = $barangKi->updated_at
            ? \Carbon\Carbon::parse($barangKi->updated_at)->format('Y-m-d H:i:s')
            : 'Tidak Ada';

        return [
            $barangKi->id_barcode,
            $barangKi->barang->name ?? 'N/A',
            $barangKi->barang->subcategory?->category->name ?? 'No Category',
            $barangKi->barang->subcategory?->name ?? 'No Subcategory',
            $barangKi->barang->brand?->name ?? 'No Brand',
            $barangKi->barang->type?->name ?? 'No Type',
            $barangKi->satuan?->name ?? 'No Satuan',
            $stokDisplay,
            'Rp' . number_format($barangKi->price_buy ?? 0, 0, ',', '.'),
            'Rp' . number_format($barangKi->price_sell ?? 0, 0, ',', '.'),
            $diskonDetail,
            $expiredDate,
            $statusBarang,
            $diskonStatus,
            $createdAt,
            $updatedAt,
        ];
    }

    /**
     * Styling worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4F46E5'], // Indigo
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            "A1:P{$lastRow}" => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // ID PRODUCT
            'B' => 25,  // Nama Barang
            'C' => 18,  // Kategori
            'D' => 18,  // Sub Kategori
            'E' => 15,  // Brand
            'F' => 15,  // Type
            'G' => 12,  // Satuan
            'H' => 20,  // Stok
            'I' => 15,  // Harga Beli
            'J' => 15,  // Harga Jual
            'K' => 15,  // Diskon
            'L' => 15,  // Expired
            'M' => 12,  // Status Barang
            'N' => 15,  // Status Diskon
            'O' => 18,  // Created At
            'P' => 18,  // Updated At
        ];
    }

    /**
     * Nama sheet
     */
    public function title(): string
    {
        return 'Data Barang KI';
    }
}
