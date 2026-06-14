<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\KasirProfile;
use App\Models\Toko\KasirShift;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use App\Models\Toko\KasirPinThrottle;
use App\Services\KasirAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasirController extends Controller
{
    /**
     * Owner creates a new cashier account directly.
     */
    public function createKasir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'username' => 'required|string|min:3|unique:users,username',
            'password' => 'required|string|min:8',
            'pin' => 'required|string|digits:6',
        ], [
            'pin.digits' => 'PIN harus tepat 6 digit angka.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $owner = Auth::user();
        $toko = TokoModel::where('owner_id', $owner->id)->first();

        if (!$toko) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki toko.'], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Create User
            $user = UserModel::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'username' => $request->username,
                'password' => $request->password,
                'status' => 'active',
                'profile_completed' => true,
                'phone_verified_at' => now(),
            ]);

            // 2. Assign Role 'kasir'
            $user->assignRole('kasir');

            // 3. Assign Jabatan 'kasir' in toko_user
            $jabatan = JabatanModel::where('slug', 'kasir')->first();
            TokoUserModel::create([
                'user_id' => $user->id,
                'toko_id' => $toko->id,
                'jabatan_id' => $jabatan->id,
                'status' => 'active',
            ]);

            // 4. Create Kasir Profile for PIN
            KasirProfile::create([
                'user_id' => $user->id,
                'toko_id' => $toko->id,
                'pin' => Hash::make($request->pin),
                'is_active' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun kasir berhasil dibuat.',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function listKasir()
    {
        $owner = Auth::user();
        $toko = TokoModel::where('owner_id', $owner->id)->first();

        if (!$toko) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan.'], 404);
        }

        $kasirIds = TokoUserModel::where('toko_id', $toko->id)
            ->whereHas('jabatan', function($q) {
                $q->where('slug', 'kasir');
            })
            ->pluck('user_id');

        $kasirs = UserModel::whereIn('id', $kasirIds)->get();

        return response()->json(['success' => true, 'data' => $kasirs]);
    }

    /**
     * Open a new shift.
     */
    public function openShift(Request $request)
    {
        $request->validate([
            'shift_awal' => 'required|numeric|min:0',
            'pin' => 'required|string|digits:6',
        ]);

        $user = Auth::user();
        $tokoUser = TokoUserModel::where('user_id', $user->id)->first();
        
        if (!$tokoUser) {
            return response()->json(['success' => false, 'message' => 'Anda tidak terdaftar di toko manapun.'], 403);
        }

        // Check Throttle
        $throttle = KasirPinThrottle::firstOrCreate(
            ['kasir_id' => $user->id, 'toko_id' => $tokoUser->toko_id],
            ['failed_attempts' => 0]
        );

        if ($throttle->isLocked()) {
            $minutesLeft = now()->diffInMinutes($throttle->locked_until, false);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak kesalahan PIN. Coba lagi dalam $minutesLeft menit.",
            ], 429);
        }

        // Verify PIN
        $profile = KasirProfile::where('user_id', $user->id)->where('toko_id', $tokoUser->toko_id)->first();
        
        if (!$profile || !Hash::check($request->pin, $profile->pin)) {
            $throttle->increment('failed_attempts');
            $throttle->update(['last_attempt_at' => now()]);
            
            if ($throttle->failed_attempts >= 5) {
                $throttle->update(['locked_until' => now()->addMinutes(15)]);
                KasirAuditService::log($user->id, $tokoUser->toko_id, 'ACCOUNT_LOCKED', 'locked', [], $request);
                return response()->json(['success' => false, 'message' => 'Terlalu banyak kesalahan PIN. Akun terkunci 15 menit.'], 429);
            }

            KasirAuditService::log($user->id, $tokoUser->toko_id, 'PIN_VERIFY_FAILED', 'failed', ['attempts' => $throttle->failed_attempts], $request);
            return response()->json(['success' => false, 'message' => 'PIN salah.'], 401);
        }

        $throttle->reset();

        try {
            $shift = DB::transaction(function() use ($user, $tokoUser, $request) {
                $activeShift = KasirShift::where('kasir_id', $user->id)
                    ->where('toko_id', $tokoUser->toko_id)
                    ->whereNull('closed_at')
                    ->lockForUpdate()
                    ->first();

                if ($activeShift) {
                    throw new \Exception('Anda masih memiliki shift aktif.');
                }

                return KasirShift::create([
                    'kasir_id' => $user->id,
                    'toko_id' => $tokoUser->toko_id,
                    'shift_awal' => $request->shift_awal,
                    'opened_at' => now(),
                ]);
            });

            KasirAuditService::log($user->id, $tokoUser->toko_id, 'SHIFT_OPEN', 'success', ['shift_id' => $shift->id], $request);

            return response()->json(['success' => true, 'message' => 'Shift berhasil dibuka.', 'data' => $shift]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Close active shift.
     */
    public function closeShift(Request $request)
    {
        $request->validate([
            'shift_akhir' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tokoUser = TokoUserModel::where('user_id', $user->id)->first();

        try {
            $shift = DB::transaction(function() use ($user, $tokoUser, $request) {
                $shift = KasirShift::where('kasir_id', $user->id)
                    ->where('toko_id', $tokoUser->toko_id)
                    ->whereNull('closed_at')
                    ->lockForUpdate()
                    ->first();

                if (!$shift) {
                    throw new \Exception('Tidak ada shift aktif.');
                }

                $expectedAmount = $shift->shift_awal + $shift->total_transaksi_tunai;
                $shiftBalance = $request->shift_akhir - $expectedAmount;
                $discrepancyAmount = abs($shiftBalance);
                $discrepancyPercent = $expectedAmount > 0 ? ($discrepancyAmount / $expectedAmount) * 100 : 100;

                $discrepancyStatus = 'verified';
                if ($discrepancyAmount > 50000 || $discrepancyPercent > 5) {
                    $discrepancyStatus = 'pending_verification';
                }

                $shift->update([
                    'shift_akhir' => $request->shift_akhir,
                    'shift_balance' => $shiftBalance,
                    'discrepancy_amount' => $discrepancyAmount,
                    'discrepancy_status' => $discrepancyStatus,
                    'closed_at' => now(),
                    'notes' => $request->notes,
                ]);

                return $shift;
            });

            KasirAuditService::log($user->id, $tokoUser->toko_id, 'SHIFT_CLOSE', 'success', [
                'shift_id' => $shift->id,
                'balance' => $shift->shift_balance
            ], $request);

            return response()->json(['success' => true, 'message' => 'Shift berhasil ditutup.', 'data' => $shift]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function activeShift()
    {
        $user = Auth::user();
        $tokoUser = TokoUserModel::where('user_id', $user->id)->first();
        
        if (!$tokoUser) return response()->json(['success' => false, 'data' => null]);

        $shift = KasirShift::where('kasir_id', $user->id)
            ->where('toko_id', $tokoUser->toko_id)
            ->whereNull('closed_at')
            ->first();

        return response()->json(['success' => true, 'data' => $shift]);
    }

    public function latestClosing()
    {
        $user = Auth::user();
        $tokoUser = TokoUserModel::where('user_id', $user->id)->first();
        
        if (!$tokoUser) return response()->json(['success' => false, 'data' => 0]);

        $latestShift = KasirShift::where('toko_id', $tokoUser->toko_id)
            ->whereNotNull('closed_at')
            ->orderBy('closed_at', 'desc')
            ->first();

        return response()->json([
            'success' => true, 
            'data' => $latestShift ? $latestShift->shift_akhir : 0
        ]);
    }

    public function shiftHistory()
    {
        $user = Auth::user();
        
        $toko = TokoModel::where('owner_id', $user->id)->first();
        if ($toko) {
            $shifts = KasirShift::with('kasir:id,name')
                ->where('toko_id', $toko->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $tokoUser = TokoUserModel::where('user_id', $user->id)->first();
            $shifts = KasirShift::where('kasir_id', $user->id)
                ->where('toko_id', $tokoUser->toko_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json(['success' => true, 'data' => $shifts]);
    }
}
