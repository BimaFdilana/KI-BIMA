<!DOCTYPE html>
<html>

<head>
    <title>Barang KI PDF</title>
    <style>
        /* Tambahkan CSS jika perlu */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Barang KI List</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <!-- Tambahkan kolom lain jika diperlukan -->
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $barangki)
                <tr>
                    <td>{{ $barangki->id }}</td>
                    <td>{{ $barangki->barang->name }}</td>
                    <td>{{ $barangki->barang->category }}</td>
                    <td>{{ $barangki->price_sell }}</td>
                    <td>{{ $barangki->stok }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
