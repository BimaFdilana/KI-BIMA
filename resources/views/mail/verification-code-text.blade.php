KODE VERIFIKASI
===============

@if ($userName)
    Halo {{ $userName }},
@endif

Ini adalah kode verifikasi Anda: {{ $code }}

PERHATIAN:
- Kode berlaku selama 10 menit
- Jangan berikan kode ini kepada siapapun
- Jika Anda tidak meminta kode ini, abaikan email ini

Email otomatis - mohon tidak membalas
© {{ date('Y') }} {{ config('app.name') }}
