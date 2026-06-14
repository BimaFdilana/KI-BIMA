<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .code-box {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Kode Verifikasi</h1>
            @if($userName)
            <p>Halo {{ $userName }},</p>
            @endif
        </div>
        
        <p>Ini adalah kode verifikasi Anda untuk melanjutkan proses:</p>
        
        <div class="code-box">
            <div class="code">{{ $code }}</div>
            <p><strong>Kode Verifikasi Anda</strong></p>
        </div>
        
        <div class="warning">
            <h3>⚠️ Perhatian!</h3>
            <ul>
                <li>Kode berlaku selama <strong>10 menit</strong></li>
                <li>Jangan berikan kode ini kepada siapapun</li>
                <li>Jika Anda tidak meminta kode ini, abaikan email ini</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>