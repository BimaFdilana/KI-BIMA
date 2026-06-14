<?php

namespace App\Exports;

use App\Models\Barang\Brand;
use App\Models\Barang\SatuanItem;
use App\Models\Barang\Subcategory;
use App\Models\Barang\TypeItem;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BarangMasterTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Import' => new BarangMasterMainSheet(),
            'Referensi'       => new BarangMasterRefSheet(),
        ];
    }
}

/* ======================================================================
   Sheet 1 – Template utama
   ====================================================================== */
class BarangMasterMainSheet implements WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string { return 'Template Import'; }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // KODE_BARANG
            'B' => 20, // KODE_BARCODE
            'C' => 30, // NAMA
            'D' => 20, // KATEGORI
            'E' => 22, // SUB_KATEGORI
            'F' => 18, // BRAND
            'G' => 15, // TIPE_BARANG
            'H' => 12, // SATUAN_1
            'I' => 10, // ISI_1
            'J' => 12, // SATUAN_2
            'K' => 10, // ISI_2
            'L' => 12, // SATUAN_3
            'M' => 10, // ISI_3
            'N' => 15, // HPP
            'O' => 15, // HARGA_JUAL
            'P' => 20, // PEMBELI
            'Q' => 16, // EARLY_EXPIRED
            'R' => 16, // MID_EXPIRED
            'S' => 16, // LAST_EXPIRED
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastCol = 'S';

                // ── Header baris 1 ─────────────────────────────────────
                $headers = [
                    'A1' => 'KODE_BARANG',
                    'B1' => 'KODE_BARCODE',
                    'C1' => 'NAMA',
                    'D1' => 'KATEGORI',
                    'E1' => 'SUB_KATEGORI',
                    'F1' => 'BRAND',
                    'G1' => 'TIPE_BARANG',
                    'H1' => 'SATUAN_1',
                    'I1' => 'ISI_1',
                    'J1' => 'SATUAN_2',
                    'K1' => 'ISI_2',
                    'L1' => 'SATUAN_3',
                    'M1' => 'ISI_3',
                    'N1' => 'HPP',
                    'O1' => 'HARGA_JUAL',
                    'P1' => 'PEMBELI',
                    'Q1' => 'EARLY_EXPIRED',
                    'R1' => 'MID_EXPIRED',
                    'S1' => 'LAST_EXPIRED',
                ];

                foreach ($headers as $cell => $value) {
                    $sheet->setCellValue($cell, $value);
                }

                // Style header – biru gelap
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F75B6']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Tandai kolom WAJIB dengan kuning
                foreach (['A', 'C', 'F', 'G', 'H', 'I'] as $col) {
                    $sheet->getStyle("{$col}1")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF2CC']],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF7F4F00']],
                    ]);
                }

                // ── Baris contoh (baris 2) – merah/pink ────────────────
                $examples = [
                    'A2' => '20020',
                    'B2' => '8999909192034',
                    'C2' => '234 FILTER',
                    'D2' => 'Produk Tembakau',
                    'E2' => 'Zat Adiktif',
                    'F2' => 'Dji Sam Soe',
                    'G2' => 'Harian',
                    'H2' => 'Slop',
                    'I2' => 10,
                    'J2' => 'Kotak',
                    'K2' => 10,
                    'L2' => '',
                    'M2' => '',
                    'N2' => 36500,
                    'O2' => 39000,
                    'P2' => 'Kedai Indonesia',
                    'Q2' => '',
                    'R2' => '',
                    'S2' => '',
                ];
                foreach ($examples as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFC7CE']],
                    'font'    => ['color' => ['argb' => 'FF9C0006'], 'italic' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ── Keterangan di kolom U ───────────────────────────────
                $sheet->setCellValue('U1', '* Kolom kuning = WAJIB DIISI');
                $sheet->setCellValue('U2', '(baris merah = contoh, HAPUS sebelum import)');
                $sheet->setCellValue('U3', 'TIPE_BARANG: Harian / Mingguan / Bulanan');
                $sheet->setCellValue('U4', 'SATUAN: lihat sheet Referensi');
                $sheet->setCellValue('U5', 'ISI = qty per satuan besar, misal 1 Slop = 10 Kotak → ISI_1=10');
                $sheet->setCellValue('U6', 'EARLY/MID/LAST_EXPIRED = jumlah hari (angka, boleh kosong)');
                $sheet->setCellValue('U7', 'KODE_BARANG = SKU (boleh angka/huruf, unik per barang)');
                $sheet->getColumnDimension('U')->setWidth(55);
                $sheet->getStyle('U1:U7')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['argb' => 'FF555555']],
                    'alignment' => ['wrapText' => true],
                ]);
                $sheet->getStyle('U1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF7F4F00']],
                ]);

                // ── Freeze row 1 ────────────────────────────────────────
                $sheet->freezePane('A2');
            },
        ];
    }
}

/* ======================================================================
   Sheet 2 – Referensi: Brand, SubKategori, Satuan, Tipe
   ====================================================================== */
class BarangMasterRefSheet implements WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string { return 'Referensi'; }

    public function columnWidths(): array
    {
        return [
            'A' => 25, 'B' => 5,
            'C' => 25, 'D' => 5,
            'E' => 18, 'F' => 18, 'G' => 5,
            'H' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $headerStyle = [
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F75B6']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ];

                // ── Judul kolom ─────────────────────────────────────────
                $sheet->setCellValue('A1', 'BRAND');
                $sheet->setCellValue('C1', 'SUB_KATEGORI');
                $sheet->setCellValue('E1', 'SATUAN (nama)');
                $sheet->setCellValue('F1', 'SATUAN (kode/cut)');
                $sheet->setCellValue('H1', 'TIPE_BARANG');

                $sheet->getStyle('A1')->applyFromArray($headerStyle);
                $sheet->getStyle('C1')->applyFromArray($headerStyle);
                $sheet->getStyle('E1:F1')->applyFromArray($headerStyle);
                $sheet->getStyle('H1')->applyFromArray($headerStyle);

                // ── Data Brand ──────────────────────────────────────────
                try {
                    $brands = Brand::orderBy('name')->pluck('name')->values();
                } catch (\Exception $e) {
                    $brands = collect();
                }

                // ── Data Subcategory ────────────────────────────────────
                try {
                    $subs = Subcategory::orderBy('name')->pluck('name')->values();
                } catch (\Exception $e) {
                    $subs = collect();
                }

                // ── Data Satuan ─────────────────────────────────────────
                try {
                    $satuans = SatuanItem::orderBy('name')->get(['name', 'cut_name']);
                } catch (\Exception $e) {
                    $satuans = collect();
                }

                // ── Data TypeItem ───────────────────────────────────────
                try {
                    $types = TypeItem::orderBy('name')->pluck('name')->values();
                } catch (\Exception $e) {
                    $types = collect();
                }

                $maxRows = max($brands->count(), $subs->count(), $satuans->count(), $types->count(), 1);

                for ($i = 0; $i < $maxRows; $i++) {
                    $row   = $i + 2;
                    $isEven = ($i % 2 === 0);

                    // Brand
                    if ($b = $brands->get($i)) {
                        $sheet->setCellValue("A{$row}", $b);
                        if ($isEven) $sheet->getStyle("A{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']]]);
                    }

                    // Subcategory
                    if ($s = $subs->get($i)) {
                        $sheet->setCellValue("C{$row}", $s);
                        if ($isEven) $sheet->getStyle("C{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']]]);
                    }

                    // Satuan
                    if ($sat = $satuans->get($i)) {
                        $sheet->setCellValue("E{$row}", $sat->name);
                        $sheet->setCellValue("F{$row}", $sat->cut_name ?? $sat->name);
                        if ($isEven) {
                            $sheet->getStyle("E{$row}:F{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']]]);
                        }
                    }

                    // Type
                    if ($t = $types->get($i)) {
                        $sheet->setCellValue("H{$row}", $t);
                        if ($isEven) $sheet->getStyle("H{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']]]);
                    }
                }

                // Pesan jika kosong
                if ($brands->isEmpty()) {
                    $sheet->setCellValue('A2', 'Belum ada brand');
                }
                if ($subs->isEmpty()) {
                    $sheet->setCellValue('C2', 'Belum ada sub kategori');
                }
                if ($satuans->isEmpty()) {
                    $sheet->setCellValue('E2', 'Belum ada satuan');
                }
                if ($types->isEmpty()) {
                    $sheet->setCellValue('H2', 'Belum ada tipe barang');
                }
            },
        ];
    }
}
