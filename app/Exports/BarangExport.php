<?php

namespace App\Exports;

use App\Models\Barang\BarangModel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Events\AfterSheet;

class BarangExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    WithCustomCsvSettings,
    ShouldAutoSize,
    WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = BarangModel::query()
            ->with([
                'subcategory:id,name',
                'brand:id,name',
                'type:id,name'
            ])
            ->select([
                'id',
                'sku',
                'subcategory_id',
                'brand_id',
                'type_id',
                'name',
                'description',
                'early_expiry_days',
                'mid_expiry_days',
                'late_expiry_days',
                'status',
                'created_at',
                'updated_at'
            ]);

        // Apply filters if provided
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['subcategory_id'])) {
            $query->where('subcategory_id', $this->filters['subcategory_id']);
        }

        if (!empty($this->filters['brand_id'])) {
            $query->where('brand_id', $this->filters['brand_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Subcategory',
            'Brand',
            'Type',
            'Name',
            'Description',
            'Early Expiry Days',
            'Mid Expiry Days',
            'Late Expiry Days',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->id,
            $barang->sku,
            $barang->subcategory->name ?? '',
            $barang->brand->name ?? '',
            $barang->type->name ?? '',
            $barang->name,
            $barang->description,
            $barang->early_expiry_days,
            $barang->mid_expiry_days,
            $barang->late_expiry_days,
            $barang->status,
            $barang->created_at->format('Y-m-d H:i:s'),
            $barang->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 records at a time
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_character' => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Add formatting if needed
                $event->sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => 'E2E8F0']
                    ]
                ]);
            }
        ];
    }
}
