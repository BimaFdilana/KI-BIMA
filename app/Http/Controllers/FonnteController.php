<?php

namespace App\Http\Controllers;

use App\Services\Message\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FonnteController extends Controller
{
    protected FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Menampilkan dashboard dengan daftar perangkat.
     */
    public function index()
    {
        $devicesData = $this->fonnteService->getAllDevices();
        $accountStatus = [];
        $devices = [];
        $canAddDevice = true;

        if ($devicesData['status'] && isset($devicesData['data'])) {
            $accountStatus = $devicesData['data'];
            if (isset($accountStatus['data']) && is_array($accountStatus['data'])) {
                $devices = $accountStatus['data'];
                // Cek apakah ada perangkat 'Free' yang terhubung
                foreach ($devices as $device) {
                    if (($device['package'] ?? null) === 'Free' && ($device['status'] ?? null) === 'connect') {
                        $canAddDevice = false;
                        break;
                    }
                }
            }
        }

        return view('fonnte_dashboard', [
            'devices' => $devices,
            'accountStatus' => $accountStatus,
            'canAddDevice' => $canAddDevice,
            'error' => $devicesData['status'] ? null : ($devicesData['error'] ?? 'Terjadi kesalahan saat mengambil data perangkat.')
        ]);
    }

    /**
     * Menambahkan perangkat baru.
     * Mengembalikan response JSON.
     */
    public function addDevice(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string',
        ]);

        $devicesData = $this->fonnteService->getAllDevices();
        if ($devicesData['status'] && isset($devicesData['data']['data'])) {
            foreach ($devicesData['data']['data'] as $device) {
                if (($device['package'] ?? null) === 'Free' && ($device['status'] ?? null) === 'connect') {
                    return response()->json(['success' => false, 'error' => 'Anda sudah memiliki satu perangkat gratis yang terhubung. Harap putuskan koneksi perangkat yang ada untuk menambahkan yang baru.'], 400);
                }
            }
        }

        $result = $this->fonnteService->addDevice($request->name, $request->phone_number);

        if ($result['status']) {
            return response()->json(['success' => true, 'message' => 'Perangkat berhasil ditambahkan. Silakan pindai QR Code untuk terhubung.']);
        }

        Log::error('Gagal menambah perangkat', ['response' => $result]);
        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal menambah perangkat.'], 400);
    }

    /**
     * Mengirim pesan WhatsApp.
     * Mengembalikan response JSON.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|string',
            'message' => 'required|string',
            'device_token' => 'required|string',
            'message_type' => 'required|in:normal,bulk',
        ]);

        $phoneNumbers = explode("\n", str_replace("\r", "", $request->phone_numbers));
        $phoneNumbers = array_map('trim', $phoneNumbers);
        $phoneNumbers = array_filter($phoneNumbers); // Hapus baris kosong

        if ($request->message_type === 'bulk') {
            $result = $this->fonnteService->sendBulkMessage(
                $phoneNumbers,
                $request->message,
                $request->device_token
            );
        } else {
            $result = $this->fonnteService->sendWhatsAppMessage(
                $phoneNumbers[0] ?? null,
                $request->message,
                $request->device_token
            );
        }

        if ($result['status']) {
            return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim.']);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal mengirim pesan.'], 400);
    }

    /**
     * Memutuskan koneksi perangkat.
     * Mengembalikan response JSON.
     */
    public function disconnectDevice(Request $request)
    {
        $request->validate(['device_token' => 'required|string']);
        $result = $this->fonnteService->disconnectDevice($request->device_token);

        if ($result['status']) {
            return response()->json(['success' => true, 'message' => 'Perangkat berhasil diputuskan.']);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal memutus perangkat.'], 400);
    }

    /**
     * Meminta QR Code untuk aktivasi perangkat.
     * Mengembalikan response JSON.
     */
    public function requestQR(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'device_token' => 'required|string',
        ]);

        $result = $this->fonnteService->requestQRActivation($request->phone_number, $request->device_token);

        if ($result['status'] && isset($result['data']['url'])) {
            return response()->json(['success' => true, 'url' => $result['data']['url']]);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal mendapatkan QR Code.'], 400);
    }

    /**
     * Meminta OTP untuk menghapus perangkat.
     * Mengembalikan response JSON.
     */
    public function requestDeleteOTP(Request $request)
    {
        $request->validate(['device_token' => 'required|string']);
        $result = $this->fonnteService->requestOTPForDeleteDevice($request->device_token);

        if ($result['status']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal meminta OTP.'], 400);
    }
    /**
     * Mengecek status koneksi perangkat WhatsApp.
     * Mengembalikan response JSON dengan status koneksi terkini.
     */
    public function checkConnection(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        try {
            // Ambil semua data perangkat untuk mencari status device yang diminta
            $devicesData = $this->fonnteService->getAllDevices();

            if (!$devicesData['status'] || !isset($devicesData['data']['data'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal mengambil data perangkat.'
                ], 400);
            }

            $devices = $devicesData['data']['data'];
            $targetDevice = null;

            // Cari device berdasarkan token
            foreach ($devices as $device) {
                if (($device['token'] ?? '') === $request->device_token) {
                    $targetDevice = $device;
                    break;
                }
            }

            if (!$targetDevice) {
                return response()->json([
                    'success' => false,
                    'error' => 'Perangkat tidak ditemukan.'
                ], 404);
            }

            $currentStatus = $targetDevice['status'] ?? 'disconnect';

            // Jika status masih disconnect, cek apakah QR masih valid
            $qrExpired = false;
            if ($currentStatus === 'disconnect') {
                // Cek apakah QR sudah expired (biasanya QR Fonnte expired setelah 2 menit)
                // Kita bisa menggunakan timestamp atau memanggil API khusus untuk cek QR
                $qrExpired = $this->checkQRExpiration($request->device_token);
            }

            return response()->json([
                'success' => true,
                'status' => $currentStatus,
                'qr_expired' => $qrExpired,
                'device_info' => [
                    'name' => $targetDevice['name'] ?? '',
                    'phone' => $targetDevice['device'] ?? '',
                    'package' => $targetDevice['package'] ?? '',
                    'expired' => $targetDevice['expired'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking connection status', [
                'device_token' => $request->device_token,
                'phone_number' => $request->phone_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengecek status koneksi.'
            ], 500);
        }
    }

    /**
     * Helper method untuk mengecek apakah QR Code sudah expired.
     * QR Code Fonnte biasanya expired setelah 2 menit.
     */
    private function checkQRExpiration(string $deviceToken): bool
    {
        try {
            // Memanggil service untuk cek status QR
            // Implementasi ini tergantung pada API Fonnte
            $qrStatus = $this->fonnteService->checkQRStatus($deviceToken);

            if (isset($qrStatus['status']) && $qrStatus['status'] === false) {
                // Jika API mengembalikan status false, berarti QR expired
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('Error checking QR expiration', [
                'device_token' => $deviceToken,
                'error' => $e->getMessage()
            ]);

            // Jika error, anggap QR masih valid untuk menghindari false positive
            return false;
        }
    }
    /**
     * Menghapus perangkat dengan OTP.
     * Mengembalikan response JSON.
     */
    public function deleteDevice(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'device_token' => 'required|string',
        ]);

        $result = $this->fonnteService->submitOTPForDeleteDevice($request->otp, $request->device_token);

        if ($result['status']) {
            return response()->json(['success' => true, 'message' => 'Perangkat berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal menghapus perangkat.'], 400);
    }
}
