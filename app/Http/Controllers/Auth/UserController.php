<?php

namespace App\Http\Controllers\Auth;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoSelling;
use App\Models\Toko\TokoUserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Services\Message\VerificationService;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserDataTable $dataTable)
    {
        $filter = $request->get('filter', null);

        // Apply filter and return dataTable
        return $dataTable->setFilter($filter)->render('auth.user.index');
    }


    public function detailIndex($username)
    {
        // Get current authenticated user
        $user = UserModel::withTrashed()->where('username', $username)->first();
        $roleName = $user ? $user->getRoleNames()->first() : null;
        $role = ucfirst($roleName);
        $permissions = $user->getAllPermissions()->sortBy('name');

        // Format phone number with country code
        $formattedPhoneNumber = $this->formatPhoneNumber($user->phone_number);
        $formattedPermissions = $permissions->map(function ($permission) {
            $permission->formatted_name = $this->formatPermissionNameAdvanced($permission->name);
            return $permission;
        });

        // Format date of birth - assuming you have a date_of_birth column
        $formattedBirthDate = null;
        if (!empty($user->date_of_birth)) {
            $birthDate = Carbon::parse($user->date_of_birth);
            $formattedBirthDate = $birthDate->translatedFormat('j F Y');
        }

        // Format created_at timestamp
        $formattedCreatedAt = null;
        if ($user->created_at) {
            $createdAt = Carbon::parse($user->created_at);
            $formattedCreatedAt = $createdAt->translatedFormat('j F Y');
        }

        // Get user's toko information if they have any
        $toko = $user->tokos()->first();
        $tokoInfo = null;
        if ($toko) {
            $jabatan = JabatanModel::find($toko->pivot->jabatan_id);
            $jabatanName = $jabatan ? $jabatan->name : 'Unknown Position';
            $employeeCount = $toko->users()->count();
            $formattedCreatedAt = $createdAt = Carbon::parse($user->created_at)->translatedFormat('j F Y');

            $rank = TokoSelling::selectRaw('toko_id, SUM(total_harga) as total')
                ->groupBy('toko_id')
                ->orderByDesc('total')
                ->get()
                ->pluck('total', 'toko_id')
                ->keys()
                ->search($toko->id) + 1;

            $totalHarga = TokoSelling::where('toko_id', $toko->id)
                ->sum('total_harga');


            $tokoInfo = [
                'id' => $toko->id,
                'name' => $toko->name,
                'position' => $jabatanName,
                'position_id' => $toko->pivot->jabatan_id,
                'status' => $toko->status,
                'address' => $toko->address,
                'description' => $toko->description,
                'owner' => $toko->owner,
                'employee_count' => $employeeCount,
                'token' => $toko->token,
                'ki_point' => $toko->ki_point,
                'formattedCreatedAt' => $formattedCreatedAt,
                'tokoRankMonth' => $totalHarga,
                'rank' => $rank,
            ];
        }



        return view('auth.user.detail-profile', compact(
            'user',
            'role',
            'formattedPermissions',
            'tokoInfo',
            'formattedPhoneNumber',
            'formattedBirthDate',
            'formattedCreatedAt'
        ));
    }

    /**
     * Get role and permission data for modal
     */
    public function getRolePermissionData($userId): JsonResponse
    {
        try {
            $user = UserModel::findOrFail($userId);

            // Get current user role and permissions
            $currentRole = $user->getRoleNames()->first();
            $currentPermissions = $user->getAllPermissions()->pluck('id')->toArray();

            // Get user's current toko and jabatan
            $currentToko = $user->tokos()->first();
            $currentJabatan = null;

            if ($currentToko) {
                $currentJabatan = $currentToko->pivot->jabatan_id;
            }

            // Get all available data
            $roles = Role::all();
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                $permission->formatted_name = $this->formatPermissionNameAdvanced($permission->name);
            }
            $tokos = TokoModel::where('status', 'active')
                ->limit(6)
                ->get();
            $jabatan = JabatanModel::all();

            return response()->json([
                'success' => true,
                'data' => [
                    'current_role' => $currentRole,
                    'current_permissions' => $currentPermissions,
                    'current_toko' => $currentToko ? $currentToko->id : null,
                    'current_jabatan' => $currentJabatan,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'tokos' => $tokos,
                    'jabatan' => $jabatan
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tokosearch(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $tokos = TokoModel::where('status', 'active')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->limit(6)
            ->get();
        return response()->json([
            'success' => true,
            'data' => $tokos
        ]);
    }

    public function searchPermission(Request $request): JsonResponse
    {
        $search = strtolower($request->get('search', ''));

        // Search in both name and formatted name
        $permissions = Permission::all()
            ->filter(function ($permission) use ($search) {
                $formattedName = strtolower($this->formatPermissionNameAdvanced($permission->name));
                return
                    strpos(strtolower($permission->name), $search) !== false ||
                    strpos($formattedName, $search) !== false;
            })
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'formatted_name' => $this->formatPermissionNameAdvanced($permission->name)
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Update user role and permissions
     */
    public function updateRolePermission(Request $request, $userId): JsonResponse
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'toko_id' => 'nullable|exists:toko,id',
            'jabatan_id' => 'nullable|exists:jabatan,id'
        ]);
        try {
            DB::beginTransaction();

            $user = UserModel::findOrFail($userId);

            // Remove all current roles and permissions
            $user->syncRoles([]);
            $user->syncPermissions([]);

            // Assign new role
            $user->assignRole($request->role);

            // Assign new permissions
            if ($request->has('permissions') && !empty($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $user->syncPermissions($permissions);
            }

            if ($request->role === 'shop' && $request->toko_id) {
                // Remove existing toko relationships
                TokoUserModel::where('user_id', $user->id)->delete();

                // Attach new toko with jabatan
                TokoUserModel::create([
                    'user_id' => $user->id,
                    'toko_id' => $request->toko_id,
                    'jabatan_id' => $request->jabatan_id ?? 1,
                ]);
            } else {
                // If not toko role, remove all toko relationships
                TokoUserModel::where('user_id', $user->id)->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role dan permission berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username'],
                'phone_number' => ['required', 'string', 'regex:/^(\+62|62|0)[0-9]{9,13}$/', 'unique:users,phone_number'],
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
                'role' => ['required', 'string', Rule::in(['founder', 'programmer', 'admin', 'accounting', 'operator', 'shop', 'guest'])]
            ], [
                'username.required' => 'Username wajib diisi',
                'username.min' => 'Username minimal 3 karakter',
                'username.max' => 'Username maksimal 50 karakter',
                'username.regex' => 'Username hanya boleh mengandung huruf, angka, titik, underscore, dan dash',
                'username.unique' => 'Username sudah digunakan',
                'phone_number.required' => 'Nomor HP wajib diisi',
                'phone_number.regex' => 'Format nomor HP tidak valid',
                'phone_number.unique' => 'Nomor HP sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka',
                'role.required' => 'Role wajib dipilih',
                'role.in' => 'Role tidak valid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Normalize phone number
            $phoneNumber = $this->normalizePhoneNumber($request->phone_number);
            $user = UserModel::create([
                'name' => $request->username,
                'username' => $request->username,
                'phone_number' => $phoneNumber,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }


    private function formatPermissionName($name)
    {
        return collect(preg_split('/[._]/', $name))
            ->filter() // Remove empty strings
            ->map(function ($word) {
                return ucfirst(strtolower($word));
            })
            ->implode(' ');
    }

    // Alternative method with more sophisticated formatting
    private function formatPermissionNameAdvanced($name)
    {
        $words = collect(preg_split('/[._]/', $name))
            ->filter()
            ->map(function ($word) {
                // Handle common abbreviations and specific cases
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
                    'wholesale' => 'Grosir',
                    'retail' => 'Eceran',
                    'payment' => 'Pembayaran',
                    'sell' => 'Penjualan',
                    'system' => 'Sistem',
                    'settings' => 'Pengaturan',
                    'restore' => 'Restore',
                    'access' => 'Akses',
                    'subscribe' => 'Berlangganan',
                    'unsubscribe' => 'Berhenti Langganan',
                    'configuration' => 'Konfigurasi',
                    'logs' => 'Log',
                    'add' => 'Tambah',
                    'remove' => 'Hapus',
                    'money' => 'Uang',
                    'changemoney' => 'Ubah Uang',
                    'reject' => 'Tolak',
                    'accept' => 'Terima',
                    'pos' => 'POS',
                    'notifications' => 'Notifikasi',
                    'discount' => 'Diskon',
                    'send' => 'Kirim',
                    'report' => 'Laporan',
                    'viewall' => 'Lihat Semua',
                    'roles' => 'Peran',
                    'users' => 'Pengguna',
                ];

                $lowerWord = strtolower($word);

                if (isset($specialCases[$lowerWord])) {
                    return $specialCases[$lowerWord];
                }

                // Handle specific permission prefixes
                if ($lowerWord === 'toko') {
                    return 'Toko';
                }

                return ucfirst($lowerWord);
            });

        return $words->implode(' ');
    }
    /**
     * Format phone number with proper country code in parentheses
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber($phoneNumber)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();

            // Bersihkan semua karakter kecuali angka dan +
            $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

            // Jika mulai dari 0, hapus 0
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = substr($phoneNumber, 1);
            }

            // Jika tidak diawali '+', tambah default +62
            if (substr($phoneNumber, 0, 1) !== '+') {
                $phoneNumber = '+62' . $phoneNumber;
            }

            $defaultRegion = 'ID';
            $parsedNumber = $phoneUtil->parse($phoneNumber, $defaultRegion);

            $countryCode = $parsedNumber->getCountryCode();
            $nationalNumber = $parsedNumber->getNationalNumber(); // <-- ini ambil raw number tanpa leading 0

            // Format manual biar fleksibel
            $formatted = '(+' . $countryCode . ') ' . $this->splitPhoneNumber($nationalNumber);

            return $formatted;
        } catch (NumberParseException $e) {
            return $phoneNumber;
        } catch (\Exception $e) {
            return $phoneNumber;
        }
    }

    // Fungsi bantu untuk kasih strip-stripnya
    private function splitPhoneNumber($number)
    {
        // Contoh sederhana: 3-4-4 digit split (812-3456-7891)
        return preg_replace("/(\d{3})(\d{4})(\d{4})/", "$1-$2-$3", $number);
    }


    /**
     * Check if username is available
     */
    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['exists' => false]);
        }

        $exists = UserModel::where('username', $request->username)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Username sudah digunakan' : 'Username tersedia'
        ]);
    }
    /**
     * Check if phone number is available
     */
    public function checkPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['exists' => false]);
        }

        // Normalize phone number sebelum check
        $normalizedPhone = $this->normalizePhoneNumber($request->phone_number);

        $exists = UserModel::where('phone_number', $normalizedPhone)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Nomor HP sudah digunakan' : 'Nomor HP tersedia'
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserModel $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ktp_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
            'ktp_name' => 'nullable|string|max:255',
            'ktp_address' => 'nullable|string|max:500',
            'ktp_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ktp_verified' => 'nullable|boolean',
            'ktp_verification_status' => 'nullable|in:pending,verified,rejected',
            'phone_verified' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'two_factor_enabled' => 'nullable|boolean',
            'status' => 'required|in:active,inactive,suspended',
        ]);


        // Handle profile image upload
        if ($request->hasFile('profile_photo_path')) {
            // Delete old profile image if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $request->file('profile_photo_path')->store('profile_images', 'public');
        }

        // Handle KTP image upload
        if ($request->hasFile('ktp_image')) {
            // Delete old KTP image if exists
            if ($user->ktp_image) {
                Storage::disk('public')->delete($user->ktp_image);
            }
            $validated['ktp_image'] = $request->file('ktp_image')->store('ktp_images', 'public');
        }

        // Handle verification status updates
        if ($request->has('phone_verified')) {
            $validated['phone_verified_at'] = $request->boolean('phone_verified') ? now() : null;
        }

        if ($request->has('email_verified')) {
            $validated['email_verified_at'] = $request->boolean('email_verified') ? now() : null;
        }

        // Convert boolean values
        $validated['ktp_verified'] = $request->boolean('ktp_verified');
        $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');

        // Update user
        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function updateApi(Request $request, $id)
    {
        $user = UserModel::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'gender' => ['nullable', 'in:male,female,other'],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
                'address' => ['nullable', 'string', 'max:500'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'ktp_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)->where('id', '<>', $user->id)],
                'ktp_name' => ['nullable', 'string', 'max:255'],
                'ktp_address' => ['nullable', 'string', 'max:500'],
                'ktp_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'ktp_verified' => ['nullable', 'boolean'],
                'ktp_verification_status' => ['nullable', 'in:pending,verified,rejected'],
                'phone_verified_at' => ['nullable', 'boolean'],
                'email_verified_at' => ['nullable', 'boolean'],
                'two_factor_enabled' => ['nullable', 'boolean'],
                'status' => ['required', 'in:active,inactive,suspended'],
            ], [
                'name.required' => 'Nama wajib diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'username.required' => 'Username wajib diisi',
                'username.min' => 'Username minimal 3 karakter',
                'username.max' => 'Username maksimal 50 karakter',
                'username.regex' => 'Username hanya boleh mengandung huruf, angka, titik, underscore, dan dash',
                'username.unique' => 'Username sudah digunakan',
                'email.required' => 'Email wajib diisi',
                'email.max' => 'Email maksimal 255 karakter',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'phone_number.required' => 'Nomor HP wajib diisi',
                'phone_number.regex' => 'Format nomor HP tidak valid',
                'phone_number.unique' => 'Nomor HP sudah digunakan',
                'gender.in' => 'Gender tidak valid',
                'date_of_birth.before' => 'Tanggal lahir tidak valid',
                'address.max' => 'Alamat maksimal 500 karakter',
                'profile_image.image' => 'File harus berupa gambar',
                'profile_image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
                'profile_image.max' => 'Ukuran gambar maksimal 2MB',
                'ktp_number.required' => 'Nomor KTP wajib diisi',
                'ktp_number.max' => 'Nomor KTP maksimal 20 karakter',
                'ktp_number.unique' => 'Nomor KTP sudah digunakan',
                'ktp_name.max' => 'Nama KTP maksimal 255 karakter',
                'ktp_address.max' => 'Alamat KTP maksimal 500 karakter',
                'ktp_image.image' => 'File KTP harus berupa gambar',
                'ktp_image.max' => 'Ukuran gambar KTP maksimal 2MB',
                'ktp_image.mimes' => 'Format gambar KTP harus jpeg, png, jpg, atau gif',
                'ktp_verified.boolean' => 'Status verifikasi KTP harus boolean',
                'phone_verified_at.boolean' => 'Status verifikasi nomor HP harus boolean',
                'email_verified_at.boolean' => 'Status verifikasi email harus boolean',
                'two_factor_enabled.boolean' => 'Status 2FA harus boolean',
                'status.in' => 'Status tidak valid',
            ]);
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $validated['profile_photo_path'] = $request->file('profile_image')->store('profile_images', 'public');
            }

            // Handle KTP image upload
            if ($request->hasFile('ktp_image')) {
                // Delete old KTP image if exists
                if ($user->ktp_image) {
                    Storage::disk('public')->delete($user->ktp_image);
                }
                $validated['ktp_image'] = $request->file('ktp_image')->store('ktp_images', 'public');
            }

            // Handle verification status updates
            if ($request->has('phone_verified_at')) {
                $validated['phone_verified_at'] = $request->boolean('phone_verified_at')
                    ? Carbon::now()
                    : null;
            }

            if ($request->has('email_verified_at')) {
                $validated['email_verified_at'] = $request->boolean('email_verified_at')
                    ? Carbon::now()
                    : null;
            }


            // Convert boolean values
            $validated['ktp_verified'] = $request->boolean('ktp_verified');
            $validated['two_factor_enabled'] = $request->boolean('two_factor_enabled');

            // Update user
            $user->update($validated);
            $user->updateProfileCompletedStatus();
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user->fresh()
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($username)
    {
        try {
            $user = UserModel::where('username', $username)->first();

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting current user
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Prevent deleting current user
            $ids = collect($request->ids)->reject(function ($id) {
                return $id == auth()->id();
            });

            if ($ids->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada user yang dapat dihapus'
                ], 400);
            }

            UserModel::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Users berhasil dihapus',
                'deleted_count' => $ids->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error bulk deleting users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => UserModel::count(),
                'by_role' => UserModel::selectRaw('role, COUNT(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role')
                    ->toArray(),
                'recent' => UserModel::where('created_at', '>=', now()->subDays(7))->count(),
                'active' => UserModel::where('last_login_at', '>=', now()->subDays(30))->count() // Jika ada kolom last_login_at
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting user statistics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik'
            ], 500);
        }
    }

    /**
     * Normalize phone number to consistent format
     */
    private function normalizePhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters except +
        $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Convert to +62 format
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+62' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 2) === '62') {
            $phoneNumber = '+' . $phoneNumber;
        } elseif (substr($phoneNumber, 0, 3) !== '+62') {
            $phoneNumber = '+62' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        try {
            $users = UserModel::select(['username', 'phone_number', 'role', 'created_at']);

            if ($request->has('role') && $request->role !== 'all') {
                $users->where('role', $request->role);
            }

            $users = $users->get();

            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');

                // Add BOM for Excel UTF-8 support
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header
                fputcsv($file, ['Username', 'Phone Number', 'Role', 'Created At']);

                // Data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->username,
                        $user->phone_number,
                        ucfirst($user->role),
                        $user->created_at->format('d/m/Y H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Error exporting users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengexport data'
            ], 500);
        }
    }

    public function resetPassword($username)
    {
        $user = UserModel::withTrashed()->where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        $password = Str::random(8);
        while (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]+$/', $password) === false) {
            $password = Str::random(8);
        }
        $user->password = Hash::make($password);
        $user->save();

        $verificationResult = $this->verificationService->sendNewPassword(
            $user,
            $password,
            VerificationService::CHANNEL_WHATSAPP,
        );
        return response()->json([
            'success' => true,
            'message' => 'Password ' . $user->username . ' berhasil direset menjadi ' . $password,
        ], 200);
    }

    public function suspend($username)
    {
        $user = UserModel::withTrashed()->where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengubah status akun sendiri'
            ], 403);
        }
        if ($user->status === 'suspended') {
            $user->status = 'active';
        } else {
            $user->status = 'suspended';
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User ' . $user->username . ' berhasil di ' . ($user->status === 'suspended' ? 'suspend' : 'unsuspend')
        ], 200);
    }

    public function delete($username)
    {
        $user = UserModel::withTrashed()->where('username', $username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri'
            ], 403);
        }
        if ($user->trashed()) {
            $user->restore();
        } else {
            $user->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'User ' . $user->username . ' berhasil di ' . ($user->trashed() ? 'hapus' : 'restore')
        ], 200);
    }
}
