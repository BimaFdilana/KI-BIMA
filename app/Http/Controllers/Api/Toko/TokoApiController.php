<?php

namespace App\Http\Controllers\Api\Toko;

use App\Events\TokoVerificationUpdated;
use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Toko\TokoService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class TokoApiController extends Controller
{
    protected $tokoService;

    public function __construct(TokoService $tokoService)
    {
        $this->tokoService = $tokoService;
    }

    /**
     * Create a new toko
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createToko(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terautentikasi'
                ], 401);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $existingToko = $this->tokoService->getTokoByUser($user);
            $pendingToko = TokoModel::where('owner_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingToko || $pendingToko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda memiliki toko yang sudah terdaftar'
                ], 422);
            }

            $toko = TokoModel::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'owner_id' => $user->id,
                'edited_by' => $user->id,
                'verified_by' => $user->id,
                'status' => 'pending',
            ]);

            $pemilikJabatan = JabatanModel::where('slug', 'pemilik-toko')->first();

            if (!$pemilikJabatan) {
                Log::error('Jabatan pemilik-toko tidak ditemukan');
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi jabatan tidak valid'
                ], 500);
            }

            $toko->users()->attach($user->id, ['jabatan_id' => $pemilikJabatan->id]);

            $this->assignPermissionsByJabatan($user, $pemilikJabatan);

            TokoVerificationUpdated::dispatch($toko, 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil didaftarkan dan sedang menunggu verifikasi',
                'data' => [
                    'toko_id' => $toko->id,
                    'slug' => $toko->slug
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Create toko error', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Invite user to toko
     *
     * @param Request $request
     * @param string $tokoSlug
     * @return JsonResponse
     */
    public function inviteUser(Request $request, $tokoSlug): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna belum terautentikasi'
                ], 401);
            }

            $toko = TokoModel::where('slug', $tokoSlug)->first();

            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }

            // Cek apakah user adalah bagian dari toko dan memiliki permission untuk invite
            $userToko = $user->tokos()->where('toko_id', $toko->id)->first();

            if (!$userToko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan bagian dari toko ini'
                ], 403);
            }

            $jabatan = JabatanModel::find($userToko->pivot->jabatan_id);

            if (!$jabatan || !$jabatan->can_invite_users) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengundang user'
                ], 403);
            }

            // Validasi input
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
                'jabatan_id' => 'required|exists:jabatan,id',
            ]);

            $invitedUser = UserModel::where('email', $validated['email'])->first();

            if (!$invitedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User dengan email tersebut tidak ditemukan'
                ], 404);
            }

            // Cek apakah user yang diundang sudah melengkapi profil
            if (!$invitedUser->isProfileCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User yang diundang harus melengkapi profil terlebih dahulu'
                ], 422);
            }

            // Cek apakah user yang diundang sudah bagian dari toko ini
            $existingUserToko = $invitedUser->tokos()
                ->where('toko_id', $toko->id)
                ->first();

            if ($existingUserToko) {
                return response()->json([
                    'success' => false,
                    'message' => 'User sudah bagian dari toko ini'
                ], 422);
            }

            // Validasi level jabatan
            $newJabatan = JabatanModel::find($validated['jabatan_id']);

            if (!$newJabatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jabatan tidak valid'
                ], 404);
            }

            // Cek level jabatan yang mengundang harus lebih tinggi dari yang diundang
            if ($jabatan->level <= $newJabatan->level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya dapat mengundang user dengan level jabatan di bawah Anda'
                ], 403);
            }

            // Tambahkan user ke toko dengan jabatan yang ditentukan
            $toko->users()->attach($invitedUser->id, ['jabatan_id' => $newJabatan->id]);

            // Berikan permission berdasarkan jabatan
            $this->assignPermissionsByJabatan($invitedUser, $newJabatan);

            Log::info('User berhasil diundang ke toko', [
                'inviter_id' => $user->id,
                'invited_user_id' => $invitedUser->id,
                'toko_id' => $toko->id,
                'jabatan_id' => $newJabatan->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diundang ke toko',
                'data' => [
                    'user_id' => $invitedUser->id,
                    'jabatan_id' => $newJabatan->id
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Invite user error', [
                'user_id' => Auth::id(),
                'toko_slug' => $tokoSlug,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign permissions to user based on jabatan
     *
     * @param UserModel $user
     * @param JabatanModel $jabatan
     * @return void
     */
    private function assignPermissionsByJabatan(UserModel $user, JabatanModel $jabatan): void
    {
        try {
            // Reset permission toko terlebih dahulu
            $tokoPermissions = Permission::where('name', 'like', 'toko.%')->pluck('name');
            $user->revokePermissionTo($tokoPermissions);

            // Assign permission berdasarkan jabatan
            $permissions = $this->getPermissionsForJabatan($jabatan);

            $user->givePermissionTo($permissions);

            Log::info('Permissions berhasil di-assign', [
                'user_id' => $user->id,
                'jabatan_id' => $jabatan->id,
                'permissions_count' => count($permissions)
            ]);
        } catch (\Exception $e) {
            Log::error('Assign permissions error', [
                'user_id' => $user->id,
                'jabatan_id' => $jabatan->id,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get permissions array for a specific jabatan
     *
     * @param JabatanModel $jabatan
     * @return array
     */
    private function getPermissionsForJabatan(JabatanModel $jabatan): array
    {
        $permissions = [];

        // Permissions dasar untuk semua user
        $permissions[] = 'toko.view';
        $permissions[] = 'toko.view.inventory';

        // Permissions berdasarkan capability
        if ($jabatan->can_manage_orders) {
            $permissions[] = 'toko.manage.orders';
            $permissions[] = 'toko.view.orders';
        }

        if ($jabatan->can_manage_inventory) {
            $permissions[] = 'toko.manage.inventory';
        }

        if ($jabatan->can_view_reports) {
            $permissions[] = 'toko.view.finances';
        }

        if ($jabatan->can_invite_users) {
            $permissions[] = 'toko.invite';
            $permissions[] = 'toko.manage.staff';
            $permissions[] = 'toko.view.staff';
        }

        // Permissions untuk level jabatan tertentu
        if ($jabatan->level >= 5) {
            $permissions[] = 'toko.create';
            $permissions[] = 'toko.edit';
            $permissions[] = 'toko.manage.finances';
            $permissions[] = 'toko.purchase.wholesale';
            $permissions[] = 'toko.sell.retail';
        }

        return array_unique($permissions);
    }
}
