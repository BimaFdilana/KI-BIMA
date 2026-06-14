<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daftar Barang</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        td {
            padding: 6px 8px;
            font-size: 11px;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        .inactive {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
        .header {
            position: relative;
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .date {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 10px;
        }
        .page-number {
            text-align: right;
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="date">Tanggal: {{ date('d/m/Y') }}</div>
        <h1>DAFTAR BARANG</h1>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Nama</th>
                <th>Brand</th>
                <th>Tipe</th>
                <th>Early</th>
                <th>Mid</th>
                <th>Late</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $index => $barang)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $barang->sku }}</td>
                <td>{{ $barang->name }}</td>
                <td>{{ $barang->brand ? $barang->brand->name : '-' }}</td>
                <td>{{ $barang->type ? $barang->type->name : '-' }}</td>
                <td>{{ $barang->early_expiry_days ?: '-' }}</td>
                <td>{{ $barang->mid_expiry_days ?: '-' }}</td>
                <td>{{ $barang->late_expiry_days ?: '-' }}</td>
                <td class="{{ $barang->status }}">{{ ucfirst($barang->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total Barang: {{ count($barangs) }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
    
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) - 50;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>