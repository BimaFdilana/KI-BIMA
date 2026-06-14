<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use App\Models\Toko\JabatanModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserProfileController extends Controller
{
    /**
     * Get user profile with roles, permissions, and toko information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diambil',
                'data' => $this->formatUserResponse($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Get profile error', [
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
     * Update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email:rfc,dns|max:255|unique:users,email,' . $user->id,
                'gender' => 'sometimes|required|in:male,female,other',
                'date_of_birth' => 'sometimes|required|date|date_format:Y-m-d|before:today',
                'address' => 'sometimes|required|string|max:500',
                'profile_photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'ktp_number' => 'sometimes|required|string|max:255',
                'ktp_name' => 'sometimes|required|string|max:255',
                'ktp_address' => 'sometimes|required|string|max:500',
                'ktp_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'name.required' => 'Nama wajib diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'gender.required' => 'Jenis kelamin wajib diisi',
                'gender.in' => 'Jenis kelamin tidak valid',
                'date_of_birth.required' => 'Tanggal lahir wajib diisi',
                'date_of_birth.date_format' => 'Format tanggal harus Y-m-d',
                'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini',
                'address.required' => 'Alamat wajib diisi',
                'address.max' => 'Alamat maksimal 500 karakter',
                'profile_photo.image' => 'File harus berupa gambar',
                'profile_photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
                'profile_photo.max' => 'Ukuran gambar maksimal 2MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update basic fields
            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
                if ($user->email != $request->email) {
                    $user->email_verified_at = null;
                }
            }

            if ($request->filled('gender')) {
                $user->gender = $request->gender;
            }

            if ($request->filled('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }

            if ($request->filled('address')) {
                $user->address = $request->address;
            }

            if ($request->filled('ktp_number')) {
                $user->ktp_number = $request->ktp_number;
            }

            if ($request->filled('ktp_name')) {
                $user->ktp_name = $request->ktp_name;
            }

            if ($request->filled('ktp_address')) {
                $user->ktp_address = $request->ktp_address;
            }

            if ($request->hasFile('ktp_image')) {
                if ($user->ktp_image && Storage::exists($user->ktp_image)) {
                    Storage::delete($user->ktp_image);
                }

                $file = $request->file('ktp_image');
                $path = $file->store('ktp_images', 'public');
                $user->ktp_image = $path;
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo_path && Storage::exists($user->profile_photo_path)) {
                    Storage::delete($user->profile_photo_path);
                }

                $file = $request->file('profile_photo');
                $path = $file->store('profile_photos', 'public');
                $user->profile_photo_path = $path;
            }

            $user->save();
            $user->updateProfileCompletionStatus();

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'updated_fields' => $request->only(['name', 'email', 'gender', 'date_of_birth', 'address'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diperbarui',
                'data' => $this->formatUserResponse($user)
            ]);
        } catch (\Exception $e) {
            Log::error('Update profile error', [
                'user_id' => $request->user()?->id,
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
     * Update user password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string|min:8',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    'confirmed'
                ]
            ], [
                'current_password.required' => 'Password lama wajib diisi',
                'password.required' => 'Password baru wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.max' => 'Password maksimal 255 karakter',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka',
                'password.confirmed' => 'Konfirmasi password tidak cocok'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ], 401);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            $user->tokens()->delete();

            Log::info('User password updated', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui. Silahkan login kembali.'
            ]);
        } catch (\Exception $e) {
            Log::error('Update password error', [
                'user_id' => $request->user()?->id,
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
     * Check profile completion status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCompletion(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $completionDetails = $user->getProfileCompletionDetails();

            return response()->json([
                'success' => true,
                'message' => 'Status profil berhasil diambil',
                'data' => $completionDetails
            ]);
        } catch (\Exception $e) {
            Log::error('Check profile completion error', [
                'user_id' => $request->user()?->id,
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
     * Get user roles and permissions configuration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRolesAndPermissions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $userRoles = $user->getRoleNames();
            $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            $allRoles = Role::all()->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => ucfirst($role->name)
                ];
            });

            $allPermissions = Permission::all()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'formatted_name' => $this->formatPermissionName($permission->name)
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data role dan permission berhasil diambil',
                'data' => [
                    'current_roles' => $userRoles,
                    'current_permissions' => $userPermissions,
                    'all_roles' => $allRoles,
                    'all_permissions' => $allPermissions
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get roles and permissions error', [
                'user_id' => $request->user()?->id,
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
     * Get user toko and jabatan information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTokoInfo(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $tokoInfo = $this->getUserTokoInfo($user);

            return response()->json([
                'success' => true,
                'message' => 'Data toko berhasil diambil',
                'data' => $tokoInfo
            ]);
        } catch (\Exception $e) {
            Log::error('Get toko info error', [
                'user_id' => $request->user()?->id,
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
     * Get list of available toko and jabatan
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableTokoJabatan(Request $request): JsonResponse
    {
        try {
            $tokos = TokoModel::where('status', 'active')
                ->get()
                ->map(function ($toko) {
                    return [
                        'id' => $toko->id,
                        'name' => $toko->name,
                        'slug' => $toko->slug,
                        'status' => $toko->status
                    ];
                });

            $jabatans = JabatanModel::all()->map(function ($jabatan) {
                return [
                    'id' => $jabatan->id,
                    'name' => $jabatan->name,
                    'level' => $jabatan->level,
                    'can_invite_users' => $jabatan->can_invite_users,
                    'can_manage_inventory' => $jabatan->can_manage_inventory,
                    'can_view_reports' => $jabatan->can_view_reports,
                    'can_manage_orders' => $jabatan->can_manage_orders,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data toko dan jabatan berhasil diambil',
                'data' => [
                    'tokos' => $tokos,
                    'jabatans' => $jabatans
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get available toko jabatan error', [
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
     * Helper method to format permission name
     */
    private function formatPermissionName(string $name): string
    {
        $specialCases = [
            'view' => 'Lihat',
            'create' => 'Buat',
            'edit' => 'Ubah',
            'delete' => 'Hapus',
            'manage' => 'Kelola',
            'permissions' => 'Izin',
            'orders' => 'Pesanan',
            'inventory' => 'Stok',
            'finances' => 'Keuangan',
            'staff' => 'Karyawan',
            'users' => 'Pengguna',
            'reports' => 'Laporan',
        ];

        $words = collect(preg_split('/[._]/', $name))
            ->filter()
            ->map(function ($word) use ($specialCases) {
                $lowerWord = strtolower($word);
                return $specialCases[$lowerWord] ?? ucfirst($lowerWord);
            });

        return $words->implode(' ');
    }

    /**
     * Helper method to get user toko information
     */
    private function getUserTokoInfo(UserModel $user): array
    {
        $tokoUsers = TokoUserModel::where('user_id', $user->id)
            ->with(['toko', 'jabatan'])
            ->get();

        if ($tokoUsers->isEmpty()) {
            return [];
        }

        return $tokoUsers->map(function ($tokoUser) use ($user) {
            return [
                'toko_id' => $tokoUser->toko->id,
                'toko_name' => $tokoUser->toko->name,
                'toko_slug' => $tokoUser->toko->slug,
                'toko_status' => $tokoUser->toko->status,
                'jabatan_id' => $tokoUser->jabatan->id,
                'jabatan_name' => $tokoUser->jabatan->name,
                'jabatan_level' => $tokoUser->jabatan->level,
                'is_owner' => $tokoUser->toko->owner_id === $user->id,
                'access' => [
                    'can_invite_users' => $tokoUser->jabatan->can_invite_users,
                    'can_manage_inventory' => $tokoUser->jabatan->can_manage_inventory,
                    'can_view_reports' => $tokoUser->jabatan->can_view_reports,
                    'can_manage_orders' => $tokoUser->jabatan->can_manage_orders,
                ]
            ];
        })->toArray();
    }

    /**
     * Helper method to format user response
     */
    private function formatUserResponse(UserModel $user, ?array $tokoInfo = null): array
    {
        if ($tokoInfo === null) {
            $tokoInfo = $this->getUserTokoInfo($user);
        }

        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'formatted_name' => $this->formatPermissionName($permission->name)
            ];
        });

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
            'phone_number' => $user->phone_number,
            'phone_verified_at' => $user->phone_verified_at ? $user->phone_verified_at->toIso8601String() : null,
            'gender' => $user->gender,
            'gender_display' => $user->getGenderDisplayAttribute(),
            'date_of_birth' => $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : null,
            'age' => $user->age,
            'address' => $user->address,
            'profile_photo_url' => $user->thumbnail,
            'profile_completed' => $user->profile_completed,
            'two_factor_enabled' => $user->two_factor_enabled,
            'ktp_number' => $user->ktp_number,
            'ktp_name' => $user->ktp_name,
            'ktp_address' => $user->ktp_address,
            'ktp_verified' => $user->ktp_verified,
            'ktp_image' => $user->ktp_image ? Storage::url($user->ktp_image) : null,
            'status' => $user->status,
            'roles' => $roles,
            'permissions' => $permissions,
            'toko_info' => $tokoInfo,
            'shop_request_pending' => \App\Models\Toko\TokoModel::where('owner_id', $user->id)->where('status', 'pending')->exists(),
            'has_pending_invitation' => DB::table('toko_invitations')->where('invited_id', $user->id)->where('status', 'pending')->exists(),
            'created_at' => $user->created_at->toIso8601String(),
            'updated_at' => $user->updated_at->toIso8601String(),
        ];
    }
}
