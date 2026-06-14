<?php

namespace App\Exports\BarangKI;

use App\Models\Barang\BarangModel;
use App\Models\Barang\SatuanItem;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class BarangKiTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Import' => new BarangKiMainTemplateSheet(),
            'Referensi'       => new BarangKiRefSheet(),
        ];
    }
}

/* ======================================================================
   Sheet 1 – Template utama (format sesuai Excel user)
   ====================================================================== */
class BarangKiMainTemplateSheet implements WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string { return 'Template Import'; }

    public function columnWidths(): array
    {
        return [
            'A' => 18, // KODE_BARANG
            'B' => 20, // KODE_BARCODE
            'C' => 25, // NAMA
            'D' => 15, // KATEGORI
            'E' => 18, // SUB_KATEGORI
            'F' => 15, // BRAND
            'G' => 15, // TIPE_BARANG
            'H' => 12, // SATUAN_1
            'I' => 10, // ISI_1
            'J' => 12, // SATUAN_2
            'K' => 10, // ISI_2
            'L' => 12, // SATUAN_3
            'M' => 10, // ISI_3
            'N' => 15, // HPP
            'O' => 15, // HARGA_JUAL
            'P' => 15, // PEMBELI
            'Q' => 16, // EARLY_EXPIRED
            'R' => 16, // MID_EXPIRED
            'S' => 16, // LAST_EXPIRED
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'S';

                // ── Judul ──────────────────────────────────────────────
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT BARANG KI');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F75B6']],
                ]);

                // ── Petunjuk ───────────────────────────────────────────
                $sheet->setCellValue('A2',
                    'Petunjuk: Isi data mulai baris 3. '
                    . 'KODE_BARANG = SKU barang di sistem. '
                    . 'SATUAN_1 = nama/kode satuan (lihat sheet Referensi). '
                    . 'EARLY/MID/LAST_EXPIRED = jumlah hari (angka).'
                );
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF7F7F7F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(30);

                // ── Header baris ke-3 (dipakai importer pada headingRow=3) ──
                // CATATAN: headingRow() di importer = 1 artinya baris pertama.
                // Agar cocok, kita letakkan header di baris 1.
                // Kita geser: judul di baris 1, HEADER di baris 2, data mulai baris 3.
                // Namun karena importer headingRow=1 maka header HARUS di baris 1.
                // Solusi: Sheet ini hanya sebagai panduan visual; data aktual harus
                // disalin ke file baru dengan header di baris 1.
                //
                // ATAU – letakkan header di baris 1 dan hapus baris judul di atas.
                // Kita pilih pendekatan PRAKTIS: header di baris 1, tanpa judul di atas
                // (pengguna export template lalu langsung isi data dari baris 2).
                //
                // Reset: kita re-design agar header benar-benar di baris 1 ──────

                // Hapus baris 1 & 2 yang tadi kita isi, dan mulai fresh
                $sheet->setCellValue('A1', 'KODE_BARANG');
                $sheet->setCellValue('B1', 'KODE_BARCODE');
                $sheet->setCellValue('C1', 'NAMA');
                $sheet->setCellValue('D1', 'KATEGORI');
                $sheet->setCellValue('E1', 'SUB_KATEGORI');
                $sheet->setCellValue('F1', 'BRAND');
                $sheet->setCellValue('G1', 'TIPE_BARANG');
                $sheet->setCellValue('H1', 'SATUAN_1');
                $sheet->setCellValue('I1', 'ISI_1');
                $sheet->setCellValue('J1', 'SATUAN_2');
                $sheet->setCellValue('K1', 'ISI_2');
                $sheet->setCellValue('L1', 'SATUAN_3');
                $sheet->setCellValue('M1', 'ISI_3');
                $sheet->setCellValue('N1', 'HPP');
                $sheet->setCellValue('O1', 'HARGA_JUAL');
                $sheet->setCellValue('P1', 'PEMBELI');
                $sheet->setCellValue('Q1', 'EARLY_EXPIRED');
                $sheet->setCellValue('R1', 'MID_EXPIRED');
                $sheet->setCellValue('S1', 'LAST_EXPIRED');

                // Style header baris 1
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F75B6']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Tandai kolom wajib dengan warna berbeda (kuning)
                foreach (['A', 'B', 'H', 'I', 'N', 'O'] as $col) {
                    $sheet->getStyle("{$col}1")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF2CC']],
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                    ]);
                }

                // Baris contoh (baris 2) – merah/pink
                $example = [
                    'A2' => 'BRG001',       // KODE_BARANG (SKU)
                    'B2' => 'BRG001-001',   // KODE_BARCODE
                    'C2' => 'Contoh Barang',// NAMA (info saja)
                    'D2' => 'Makanan',      // KATEGORI (info saja)
                    'E2' => 'Snack',        // SUB_KATEGORI (info saja)
                    'F2' => 'BrandA',       // BRAND (info saja)
                    'G2' => 'Konsumsi',     // TIPE_BARANG (info saja)
                    'H2' => 'PCS',          // SATUAN_1 (wajib, harus ada di referensi)
                    'I2' => 100,            // ISI_1 (quantity)
                    'J2' => 'DUS',          // SATUAN_2 (diabaikan importer)
                    'K2' => 10,             // ISI_2 (diabaikan importer)
                    'L2' => '',             // SATUAN_3
                    'M2' => '',             // ISI_3
                    'N2' => 15000,          // HPP
                    'O2' => 18000,          // HARGA_JUAL
                    'P2' => '',             // PEMBELI (diabaikan importer)
                    'Q2' => 365,            // EARLY_EXPIRED (hari)
                    'R2' => 60,             // MID_EXPIRED (hari)
                    'S2' => 7,              // LAST_EXPIRED (hari)
                ];
                foreach ($example as $cell => $val) {
                    $sheet->setCellValue($cell, $val);
                }
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFC7CE']],
                    'font'    => ['color' => ['argb' => 'FF9C0006'], 'italic' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Freeze row pertama
                $sheet->freezePane('A2');

                // Keterangan kolom wajib
                $sheet->setCellValue('U1', '* = WAJIB DIISI');
                $sheet->setCellValue('U2', '(baris merah = contoh, hapus sebelum import)');
                $sheet->getColumnDimension('U')->setWidth(40);
            },
        ];
    }
}

/* ======================================================================
   Sheet 2 – Referensi ID Barang & Satuan
   ====================================================================== */
class BarangKiRefSheet implements WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string { return 'Referensi'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 35, 'C' => 5, 'D' => 15, 'E' => 20];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Header ─────────────────────────────────────────────
                $sheet->setCellValue('A1', 'DAFTAR BARANG (SKU)');
                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('D1', 'DAFTAR SATUAN');
                $sheet->mergeCells('D1:E1');

                $sheet->setCellValue('A2', 'SKU (KODE_BARANG)');
                $sheet->setCellValue('B2', 'Nama Barang');
                $sheet->setCellValue('D2', 'Kode Singkat (SATUAN_1)');
                $sheet->setCellValue('E2', 'Nama Lengkap');

                $headerStyle = [
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2F75B6']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ];
                $sheet->getStyle('A1:B2')->applyFromArray($headerStyle);
                $sheet->getStyle('D1:E2')->applyFromArray($headerStyle);

                // ── Data Barang ────────────────────────────────────────
                try {
                    $barangs = BarangModel::select('sku', 'name')->orderBy('name')->get();
                } catch (\Exception $e) {
                    $barangs = collect();
                }

                // ── Data Satuan ────────────────────────────────────────
                try {
                    $satuans = SatuanItem::select('cut_name', 'name')->orderBy('name')->get();
                } catch (\Exception $e) {
                    $satuans = collect();
                }

                $maxRows = max($barangs->count(), $satuans->count(), 1);

                for ($i = 0; $i < $maxRows; $i++) {
                    $row     = $i + 3;
                    $barang  = $barangs->get($i);
                    $satuan  = $satuans->get($i);

                    if ($barang) {
                        $sheet->setCellValue("A{$row}", $barang->sku);
                        $sheet->setCellValue("B{$row}", $barang->name);
                    }
                    if ($satuan) {
                        $sheet->setCellValue("D{$row}", $satuan->cut_name ?? $satuan->name);
                        $sheet->setCellValue("E{$row}", $satuan->name);
                    }

                    // Stripe rows
                    if ($i % 2 === 0) {
                        if ($barang) {
                            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                            ]);
                        }
                        if ($satuan) {
                            $sheet->getStyle("D{$row}:E{$row}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                            ]);
                        }
                    }
                }

                // Pesan jika kosong
                if ($barangs->isEmpty()) {
                    $sheet->setCellValue('A3', 'Belum ada barang terdaftar. Tambah barang terlebih dahulu.');
                    $sheet->mergeCells('A3:B3');
                    $sheet->getStyle('A3')->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['argb' => 'FFFF0000']],
                    ]);
                }
                if ($satuans->isEmpty()) {
                    $sheet->setCellValue('D3', 'Belum ada satuan terdaftar.');
                    $sheet->mergeCells('D3:E3');
                    $sheet->getStyle('D3')->applyFromArray([
                        'font' => ['italic' => true, 'color' => ['argb' => 'FFFF0000']],
                    ]);
                }
            },
        ];
    }
}
