<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Backend Configuration (Laravel Server)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Laravel backend menggunakan Service Account.
    | Digunakan untuk mengirim push notification dari server.
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS'),
    'project_id' => env('FIREBASE_PROJECT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Frontend Configuration (Web/Mobile App)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk aplikasi frontend (JavaScript, Android, iOS).
    | Digunakan untuk menerima push notification di client side.
    |
    */

    'web_config' => [
        'apiKey' => env('FIREBASE_API_KEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
        'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | CATATAN PENTING
    |--------------------------------------------------------------------------
    |
    | 1. Backend (Laravel):
    |    - Butuh Service Account JSON file
    |    - Download dari: Firebase Console > Project Settings > Service Accounts
    |    - Simpan di: storage/app/firebase/firebase-credentials.json
    |
    | 2. Frontend (Web/Mobile):
    |    - Gunakan web_config untuk inisialisasi Firebase di client
    |    - Untuk Android: gunakan google-services.json
    |    - Untuk iOS: gunakan GoogleService-Info.plist
    |
    | 3. Database notifikasi tetap di MySQL Laravel!
    |    Firebase hanya untuk push notification ke device.
    |
    */
];
