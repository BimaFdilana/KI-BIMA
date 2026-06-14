<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Invoice - {{ $payment->transaction_id }}</title>
    <style type="text/css">
        @page {
            margin: 0;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .capitalize {
            text-transform: capitalize;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .invoice-info div {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .invoice-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .total-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .payment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-paid {
            background: #cce5ff;
            color: #004085;
        }

        .status-delivery {
            background: #e8f0fe;
            color: #004085;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body class="capitalize">
    <div class="invoice-container">
        <div class="header">
            <h1>INVOICE</h1>
            <p>Transaction ID: {{ $payment->transaction_id }}</p>
            <p>Date: {{ $payment->created_at->format('d M Y') }}</p>
        </div>

        <div class="invoice-info">
            <div>
                <h3>Customer Information</h3>
                <p>Name: {{ $payment->user->name }}</p>
                <p>Email: {{ $payment->user->email }}</p>
            </div>
            <div>
                <h3>Store Information</h3>
                <p>Store: {{ $payment->toko->name }}</p>
                <p>Store ID: {{ $payment->toko->slug }}</p>
            </div>
        </div>

        <div class="payment-details">
            <h3>Payment Details</h3>
            <div class="payment-info">
                <p>Payment Method: {{ $payment->payment_method }}</p>
                <p>Payment Type: @if ($payment->payment_type == 'C')
                        Cash
                    @else
                        Virtual Account
                    @endif
                </p>
                <p>Payment Status: <span class="status-success">{{ $payment->status }}</span></p>
                <p>Total Amount: Rp {{ number_format($payment->total, 0, ',', '.') }}</p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->pesanan as $pesanan)
                    <tr>
                        <td>{{ $pesanan->barangki->barang->name }}</td>
                        <td>{{ $pesanan->quantity }}</td>
                        <td>Rp {{ number_format($pesanan->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong>Rp {{ number_format($payment->total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>This is an automatically generated invoice.</p>
        </div>
    </div>
</body>

</html>
