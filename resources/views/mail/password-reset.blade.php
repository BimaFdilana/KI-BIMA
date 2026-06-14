<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .code-box {
            background-color: #f8f9fa;
            border: 2px dashed #dc3545;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }

        .code {
            font-size: 32px;
            font-weight: bold;
            color: #dc3545;
            letter-spacing: 5px;
            margin: 10px 0;
        }

        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #721c24;
        }

        .security-notice {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #0c5460;
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
            <h1>Reset Password</h1>
            @if ($userName)
                <p>Halo {{ $userName }},</p>
            @endif
        </div>

        <p>Kami menerima permintaan untuk reset password akun Anda. Gunakan kode verifikasi berikut untuk melanjutkan proses reset password:</p>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
            <p><strong>Kode Reset Password</strong></p>
        </div>

        <div class="warning">
            <h3>🔒 Keamanan Akun</h3>
            <ul>
                <li>Kode berlaku selama <strong>10 menit</strong></li>
                <li>Jangan berikan kode ini kepada siapapun</li>
                <li>Kode hanya dapat digunakan sekali</li>
            </ul>
        </div>

        <div class="security-notice">
            <h3>ℹ️ Tidak Meminta Reset Password?</h3>
            <p>Jika Anda tidak meminta reset password, <strong>abaikan email ini</strong>. Akun Anda tetap aman dan tidak ada perubahan yang akan dilakukan.</p>
            <p>Namun, kami menyarankan untuk segera mengganti password jika Anda merasa akun Anda mungkin telah dikompromikan.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
            <p>Jika Anda memiliki pertanyaan, hubungi tim dukungan kami.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
