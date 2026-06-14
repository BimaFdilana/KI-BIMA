RESET PASSWORD
==============

@if ($userName)
    Halo {{ $userName }},
@endif

Kode reset password Anda: {{ $code }}

KEAMANAN AKUN:
- Kode berlaku selama 10 menit
- Jangan berikan kode ini kepada siapapun
- Kode hanya dapat digunakan sekali

TIDAK MEMINTA RESET PASSWORD?
Jika Anda tidak meminta reset password, abaikan email ini.
Akun Anda tetap aman dan tidak ada perubahan yang akan dilakukan.

Email otomatis - mohon tidak membalas
© {{ date('Y') }} {{ config('app.name') }}
