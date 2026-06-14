<?php

namespace App\Exports;

use App\Models\Toko\BarangToko;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BarangTokoExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = BarangToko::with([
            'toko',
            'barangKI.barang.subcategory.category',
            'barangKI.barang.brand',
            'barangKI.barang.type',
            'barangKI.satuan'
        ])
            ->selectRaw('
            barang_toko.id,
            barang_toko.barangki_id,
            barang_toko.toko_id,
            barang_toko.quantity,
            barang_toko.sold,
            barang_toko.price_sell,
            barang_toko.price_buy,
            barang_toko.price_percentage,
            barang_toko.created_at,
            barang_toko.updated_at
        ')
            ->leftJoin('barang_ki', 'barang_toko.barangki_id', '=', 'barang_ki.id')
            ->leftJoin('barang', 'barang_ki.barang_id', '=', 'barang.id')
            ->leftJoin('brands', 'barang.brand_id', '=', 'brands.id')
            ->leftJoin('type_barang', 'barang.type_id', '=', 'type_barang.id')
            ->leftJoin('satuan_items', 'barang_ki.satuan_id', '=', 'satuan_items.id')
            ->leftJoin('sub_categories', 'barang.subcategory_id', '=', 'sub_categories.id')
            ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->leftJoin('toko', 'barang_toko.toko_id', '=', 'toko.id');

        $this->applyFilters($query);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Toko',
            'Owner Toko',
            'Nama Barang',
            'Barcode',
            'SKU',
            'Kategori',
            'Sub Kategori',
            'Brand',
            'Type',
            'Satuan',
            'Quantity',
            'Sold',
            'Harga Beli',
            'Harga Jual',
            'Persentase Markup',
            'Total Harga Jual',
            'Profit Per Unit',
            'Total Profit',
            'Tanggal Expired',
            'Status Expired',
            'Hari Ke Expired',
            'Created At',
            'Updated At'
        ];
    }

    public function map($barangToko): array
    {
        $hargaJual = $barangToko->price_sell;
        $persentase = $barangToko->price_percentage ?? 0;

        if ($persentase > 0) {
            $hargaJualTotal = $hargaJual + ($hargaJual * ($persentase / 100));
        } else {
            $hargaJualTotal = $hargaJual;
        }

        $profitPerUnit = $hargaJualTotal - $barangToko->price_buy;
        $totalProfit = $profitPerUnit * $barangToko->sold;

        // Status expired
        $expiredTime = $barangToko->barangKI->expired_time ?? null;
        $expiredStatus = 'No Expiry';
        $daysToExpiry = null;

        if ($expiredTime) {
            $expiredDate = \Carbon\Carbon::parse($expiredTime);
            $now = \Carbon\Carbon::now();

            if ($expiredDate->isPast()) {
                $expiredStatus = 'Expired';
                $daysToExpiry = $now->diffInDays($expiredDate) . ' days ago';
            } else {
                $daysToExpiry = $now->diffInDays($expiredDate) . ' days';

                $earlyDays = $barangToko->barangKI->barang->early_expiry_days ?? 30;
                $midDays = $barangToko->barangKI->barang->mid_expiry_days ?? 15;
                $lateDays = $barangToko->barangKI->barang->late_expiry_days ?? 7;

                $remainingDays = $now->diffInDays($expiredDate);

                if ($remainingDays > $earlyDays) {
                    $expiredStatus = 'Fresh';
                } elseif ($remainingDays <= $earlyDays && $remainingDays > $midDays) {
                    $expiredStatus = 'Early Expiry';
                } elseif ($remainingDays <= $midDays && $remainingDays > $lateDays) {
                    $expiredStatus = 'Mid Expiry';
                } elseif ($remainingDays <= $lateDays) {
                    $expiredStatus = 'Late Expiry';
                }
            }
        }

        return [
            $barangToko->id,
            $barangToko->toko->name ?? 'N/A',
            $barangToko->toko->owner->name ?? 'N/A',
            $barangToko->barangKI->barang->name ?? 'N/A',
            $barangToko->barangKI->id_barcode ?? 'N/A',
            $barangToko->barangKI->barang->sku ?? 'N/A',
            $barangToko->barangKI->barang->subcategory->category->name ?? 'No Category',
            $barangToko->barangKI->barang->subcategory->name ?? 'No Subcategory',
            $barangToko->barangKI->barang->brand->name ?? 'No Brand',
            $barangToko->barangKI->barang->type->name ?? 'No Type',
            $barangToko->barangKI->satuan->name ?? 'No Satuan',
            $barangToko->quantity,
            $barangToko->sold,
            number_format($barangToko->price_buy, 0, ',', '.'),
            number_format($hargaJual, 0, ',', '.'),
            $persentase . '%',
            number_format($hargaJualTotal, 0, ',', '.'),
            number_format($profitPerUnit, 0, ',', '.'),
            number_format($totalProfit, 0, ',', '.'),
            $expiredTime ? \Carbon\Carbon::parse($expiredTime)->format('Y-m-d H:i:s') : 'No Expiry',
            $expiredStatus,
            $daysToExpiry,
            $barangToko->created_at->format('Y-m-d H:i:s'),
            $barangToko->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4F46E5'], // Indigo
                ],
            ],
            // Auto-fit all columns
            'A:X' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 20,  // Nama Toko
            'C' => 20,  // Owner Toko
            'D' => 25,  // Nama Barang
            'E' => 15,  // Barcode
            'F' => 15,  // SKU
            'G' => 15,  // Kategori
            'H' => 15,  // Sub Kategori
            'I' => 15,  // Brand
            'J' => 15,  // Type
            'K' => 12,  // Satuan
            'L' => 12,  // Quantity
            'M' => 12,  // Sold
            'N' => 15,  // Harga Beli
            'O' => 15,  // Harga Jual
            'P' => 15,  // Persentase
            'Q' => 15,  // Total Harga Jual
            'R' => 15,  // Profit Per Unit
            'S' => 15,  // Total Profit
            'T' => 20,  // Tanggal Expired
            'U' => 15,  // Status Expired
            'V' => 15,  // Hari Ke Expired
            'W' => 20,  // Created At
            'X' => 20,  // Updated At
        ];
    }

    public function title(): string
    {
        return 'Barang Toko Export';
    }

    private function applyFilters($query)
    {
        // Apply the same filters as in DataTable
        $query->when($this->filters['stock_status'] ?? null, function ($query, $stockStatus) {
            switch ($stockStatus) {
                case 'available':
                    $query->where('barang_toko.quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('barang_toko.quantity', '>', 0)
                        ->where('barang_toko.quantity', '<=', 10);
                    break;
                case 'out_of_stock':
                    $query->where('barang_toko.quantity', '=', 0);
                    break;
                case 'has_sales':
                    $query->where('barang_toko.sold', '>', 0);
                    break;
                case 'no_sales':
                    $query->where('barang_toko.sold', '=', 0);
                    break;
            }
        });

        $query->when($this->filters['expired'] ?? null, function ($query, $expired) {
            switch ($expired) {
                case 'no_expiry':
                    $query->whereNull('barang_ki.expired_time');
                    break;
                case 'early_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.early_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)');
                    break;
                case 'mid_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.mid_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW() + INTERVAL barang.late_expiry_days DAY)');
                    break;
                case 'late_expiry':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW() + INTERVAL barang.late_expiry_days DAY)')
                        ->whereRaw('DATE(barang_ki.expired_time) > DATE(NOW())');
                    break;
                case 'expired':
                    $query->whereRaw('DATE(barang_ki.expired_time) <= DATE(NOW())');
                    break;
            }
        });

        $query->when($this->filters['category_id'] ?? null, function ($query, $categoryId) {
            $query->where('categories.id', $categoryId);
        });

        $query->when($this->filters['subcategory_id'] ?? null, function ($query, $subcategoryId) {
            $query->where('sub_categories.id', $subcategoryId);
        });

        $query->when($this->filters['brand_id'] ?? null, function ($query, $brandId) {
            $query->where('brands.id', $brandId);
        });

        $query->when($this->filters['type_id'] ?? null, function ($query, $typeId) {
            $query->where('type_barang.id', $typeId);
        });

        $query->when($this->filters['toko_id'] ?? null, function ($query, $tokoId) {
            if (is_array($tokoId)) {
                $query->whereIn('barang_toko.toko_id', $tokoId);
            } else {
                $query->where('barang_toko.toko_id', $tokoId);
            }
        });

        $query->when($this->filters['price_min'] ?? null, function ($query, $priceMin) {
            $query->where('barang_toko.price_sell', '>=', $priceMin);
        });

        $query->when($this->filters['price_max'] ?? null, function ($query, $priceMax) {
            $query->where('barang_toko.price_sell', '<=', $priceMax);
        });

        $query->when($this->filters['created_from'] ?? null, function ($query, $createdFrom) {
            $query->whereDate('barang_toko.created_at', '>=', $createdFrom);
        });

        $query->when($this->filters['created_to'] ?? null, function ($query, $createdTo) {
            $query->whereDate('barang_toko.created_at', '<=', $createdTo);
        });

        // Default filter: only show items with stock or sales history
        if (!isset($this->filters['show_all'])) {
            $query->where(function ($q) {
                $q->where('barang_toko.quantity', '>', 0)
                    ->orWhere('barang_toko.sold', '>', 0);
            });
        }

        return $query;
    }
}
