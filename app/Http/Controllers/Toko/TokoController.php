<?php

namespace App\Http\Controllers\Toko;

use App\DataTables\TokoDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auth\UserModel;
use App\Models\Toko\JabatanModel;
use App\Models\Toko\TokoModel;
use App\Models\Toko\TokoUserModel;
use App\Services\Message\NotificationService;
use App\Services\Toko\TokoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TokoController extends Controller
{
    public $tokoService;
    public $notificationService;

    public function __construct(TokoService $tokoService, NotificationService $notificationService)
    {
        $this->tokoService = $tokoService;
        $this->notificationService = $notificationService;
    }

    public function index(TokoDataTable $dataTable)
    {
        $today = Carbon::today();
        $lastWeek = $today->copy()->subWeek();

        $totalTokoActive = TokoModel::where('status', 'active')->count();
        $totalTokoActiveLastWeek = TokoModel::where('status', 'active')
            ->whereDate('created_at', '<', $lastWeek)
            ->count();

        $totalActivePercentage = $totalTokoActiveLastWeek > 0
            ? (($totalTokoActive - $totalTokoActiveLastWeek) / $totalTokoActiveLastWeek) * 100
            : ($totalTokoActive > 0 ? 100 : 0);

        $totalTokens = TokoModel::sum('token');
        $totalTokensLastWeek = TokoModel::whereDate('created_at', '<', $lastWeek)->sum('token');

        $totalTokensPercentage = $totalTokensLastWeek > 0
            ? (($totalTokens - $totalTokensLastWeek) / $totalTokensLastWeek) * 100
            : ($totalTokens > 0 ? 100 : 0);

        $totalEmployee = TokoUserModel::where('status', 'active')->count();
        $totalEmployeeLastWeek = TokoUserModel::where('status', 'active')
            ->whereDate('created_at', '<', $lastWeek)
            ->count();
        $totalEmployeePercentage = $totalEmployeeLastWeek > 0
            ? (($totalEmployee - $totalEmployeeLastWeek) / $totalEmployeeLastWeek) * 100
            : ($totalEmployee > 0 ? 100 : 0);

        $totalPendingToko = TokoModel::where('status', 'pending')->count();

        return $dataTable->render('toko.index', [
            'totalTokoActive' => $totalTokoActive,
            'totalActivePercentage' => round($totalActivePercentage, 1),
            'totalTokens' => $totalTokens,
            'totalTokensPercentage' => round($totalTokensPercentage, 1),
            'totalEmployee' => $totalEmployee,
            'totalEmployeePercentage' => round($totalEmployeePercentage, 1),
            'totalPendingToko' => $totalPendingToko,
        ]);
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:toko,name',
                'address' => 'required|string',
                'status' => 'required|in:active,pending,suspend,hasReview',
                'owner_id' => 'required|exists:users,id',
                'employees' => 'nullable|array',
                'employees.*' => 'exists:users,id',
                'jabatan_id' => 'nullable|exists:jabatan,id',
                'description' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'token' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            $newOwner = UserModel::find($request->owner_id);
            $previousTokoName = null;

            $oldOwnedToko = TokoModel::where('owner_id', $newOwner->id)->first();
            if ($oldOwnedToko) {
                $previousTokoName = $oldOwnedToko->name;
                $oldOwnedToko->update(['owner_id' => null, 'status' => 'pending']);
            }
            $newOwner->tokos()->detach();

            if ($request->has('employees')) {
                foreach ($request->employees as $employeeId) {
                    $employee = UserModel::find($employeeId);
                    if ($employee) {
                        TokoModel::where('owner_id', $employee->id)->update(['owner_id' => null, 'status' => 'pending']);
                        $employee->tokos()->detach();
                    }
                }
            }

            $slug = Str::slug($request->name);
            $count = TokoModel::where('slug', 'like', $slug . '%')->count();
            $finalSlug = ($count > 0) ? "{$slug}-" . ($count + 1) : $slug;

            $toko = TokoModel::create([
                'name' => $request->name,
                'slug' => $finalSlug,
                'address' => $request->address,
                'status' => $request->status,
                'owner_id' => $request->owner_id,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'token' => $request->token ?? 0,
                'edited_by' => Auth::id(),
            ]);

            $newOwner->syncRoles($request->status === 'active' ? 'shop' : 'guest');

            if ($request->has('employees')) {
                $kasirJabatan = JabatanModel::where('name', 'kasir')->first();
                if (!$kasirJabatan) {
                    throw new \Exception("Default jabatan 'kasir' tidak ditemukan.");
                }

                $syncData = array_fill_keys($request->employees, ['jabatan_id' => $kasirJabatan->id]);
                $toko->users()->sync($syncData);
            }

            if ($previousTokoName) {
                $this->notificationService->sendToUserFromSystem(
                    $newOwner,
                    'toko_assignment_changed',
                    ['message' => "Anda telah dipindahkan dari toko '{$previousTokoName}' dan sekarang menjadi pemilik toko baru '{$toko->name}'."]
                );
            } else {
                $this->notificationService->sendToUserFromSystem(
                    $newOwner,
                    'toko_assignment_new',
                    ['message' => "Selamat! Anda telah ditunjuk sebagai pemilik toko baru '{$toko->name}'."]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditambahkan. Pengguna terkait telah otomatis dipindahkan.',
            ]);
        });
    }

    public function update(Request $request, string $id)
    {
        Log::info('Update Toko Request:', $request->all());

        return DB::transaction(function () use ($request, $id) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:toko,name,' . $id,
                'address' => 'required|string',
                'status' => 'required|in:active,pending,suspend,hasReview',
                'owner_id' => 'required|exists:users,id',
                'employees' => 'nullable|array',
                'employees.*' => 'exists:users,id',
                'employees_data' => 'nullable|string',
                'description' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'token' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
            }

            $toko = TokoModel::with('users')->findOrFail($id);
            $originalOwnerId = $toko->owner_id;
            $newOwnerId = (int)$request->owner_id;
            $newStatus = $request->status;

            if ($originalOwnerId !== $newOwnerId) {
                $oldOwner = UserModel::find($originalOwnerId);
                $newOwner = UserModel::find($newOwnerId);

                if ($oldOwner) {
                    $oldOwner->syncRoles('guest');
                    $this->notificationService->sendToUserFromSystem($oldOwner, 'toko_ownership_revoked', ['message' => "Kepemilikan Anda pada toko '{$toko->name}' telah dicabut."]);
                }

                TokoModel::where('owner_id', $newOwnerId)->where('id', '!=', $id)->update(['owner_id' => null, 'status' => 'pending']);
                $newOwner->tokos()->detach();
                $newOwner->syncRoles($newStatus === 'active' ? 'shop' : 'guest');
                $this->notificationService->sendToUserFromSystem($newOwner, 'toko_ownership_granted', ['message' => "Anda telah ditunjuk sebagai pemilik baru toko '{$toko->name}'."]);
            } elseif ($toko->status !== $newStatus) {
                $currentOwner = UserModel::find($originalOwnerId);
                if ($currentOwner) {
                    $currentOwner->syncRoles($newStatus === 'active' ? 'shop' : 'guest');
                }
            }

            // Process employees_data if provided (from edit modal with jabatan)
            $syncData = [];
            if ($request->has('employees_data') && $request->employees_data) {
                $employeesData = json_decode($request->employees_data, true);

                if (is_array($employeesData)) {
                    $currentEmployeeIds = $toko->users->pluck('id')->toArray();
                    $newEmployeeIds = array_column($employeesData, 'id');
                    $employeesToAdd = array_diff($newEmployeeIds, $currentEmployeeIds);

                    // Detach employees who are being added from other tokos
                    foreach ($employeesToAdd as $employeeId) {
                        $employee = UserModel::find($employeeId);
                        if ($employee) {
                            TokoModel::where('owner_id', $employee->id)->update(['owner_id' => null, 'status' => 'pending']);
                            $employee->tokos()->detach();
                        }
                    }

                    // Build sync data with jabatan_id from employees_data
                    foreach ($employeesData as $empData) {
                        if (isset($empData['id'])) {
                            $jabatanId = $empData['jabatan_id'] ?? null;

                            // If no jabatan specified, use default kasir
                            if (!$jabatanId) {
                                $kasirJabatan = JabatanModel::where('name', 'kasir')->first();
                                $jabatanId = $kasirJabatan ? $kasirJabatan->id : null;
                            }

                            if ($jabatanId) {
                                $syncData[$empData['id']] = ['jabatan_id' => $jabatanId];
                            }
                        }
                    }
                }
            }
            // Fallback to employees array (from create modal)
            elseif ($request->has('employees') && is_array($request->employees)) {
                $currentEmployeeIds = $toko->users->pluck('id')->toArray();
                $newEmployeeIds = $request->employees ?? [];
                $employeesToAdd = array_diff($newEmployeeIds, $currentEmployeeIds);

                foreach ($employeesToAdd as $employeeId) {
                    $employee = UserModel::find($employeeId);
                    if ($employee) {
                        TokoModel::where('owner_id', $employee->id)->update(['owner_id' => null, 'status' => 'pending']);
                        $employee->tokos()->detach();
                    }
                }

                // Use default kasir jabatan for all employees
                $kasirJabatan = JabatanModel::where('name', 'kasir')->first();
                if (!$kasirJabatan) {
                    throw new \Exception("Default jabatan 'kasir' tidak ditemukan.");
                }
                $syncData = array_fill_keys($newEmployeeIds, ['jabatan_id' => $kasirJabatan->id]);
            }

            // Sync employees with their jabatan ONLY if employees data is present
            if ($request->has('employees_data') || $request->has('employees')) {
                $toko->users()->sync($syncData);
            }

            $toko->fill($request->only(['name', 'address', 'status', 'description', 'latitude', 'longitude', 'token', 'owner_id']));
            $toko->edited_by = Auth::id();

            if ($toko->isDirty('name')) {
                $slug = Str::slug($request->name);
                $count = TokoModel::where('slug', 'like', $slug . '%')->where('id', '!=', $id)->count();
                $toko->slug = ($count > 0) ? "{$slug}-" . ($count + 1) : $slug;
            }

            $toko->save();

            return response()->json(['success' => true, 'message' => 'Data toko berhasil diupdate.']);
        });
    }

    public function show($id)
    {
        try {
            $toko = TokoModel::with(['owner', 'users'])->findOrFail($id);

            // Sales Data (Penjualan) - Paginated
            $sales = \App\Models\Toko\TokoSelling::where('toko_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'sales_page');

            $totalSalesRevenue = \App\Models\Toko\TokoSelling::where('toko_id', $id)->sum('total_harga');
            $totalSalesCount = \App\Models\Toko\TokoSelling::where('toko_id', $id)->count();

            // Purchase Data (Pembelian / Restock) - Paginated
            $purchases = \App\Models\Toko\TokoPayment::where('toko_id', $id)
                ->with(['pesanan.barangKI'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'purchases_page');

            $totalPurchaseAmount = \App\Models\Toko\TokoPayment::where('toko_id', $id)->sum('total');
            $totalPurchaseCount = \App\Models\Toko\TokoPayment::where('toko_id', $id)->count();

            // Stock Data - Paginated
            $barangs = $toko->barangs()->with('barang.satuan')->paginate(10, ['*'], 'stock_page');

            // Chart Data (Last 30 Days) - Optimized with SQL
            $endDate = now();
            $startDate = now()->subDays(29);

            $salesData = \App\Models\Toko\TokoSelling::where('toko_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(total_harga) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $purchasesData = \App\Models\Toko\TokoPayment::where('toko_id', $id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(total) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $dates = collect();
            $chartSales = [];
            $chartPurchases = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dates->push(\Carbon\Carbon::parse($date)->format('d M'));
                $chartSales[] = $salesData[$date] ?? 0;
                $chartPurchases[] = $purchasesData[$date] ?? 0;
            }

            $chartData = [
                'categories' => $dates->toArray(),
                'sales' => $chartSales,
                'purchases' => $chartPurchases,
            ];

            return view('toko.show', compact(
                'toko',
                'sales',
                'purchases',
                'barangs',
                'totalSalesRevenue',
                'totalSalesCount',
                'totalPurchaseAmount',
                'totalPurchaseCount',
                'chartData'
            ));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('toko.index')->with('error', 'Toko tidak ditemukan');
        } catch (\Exception $e) {
            Log::error("Error fetching toko data for ID {$id}: " . $e->getMessage());
            return redirect()->route('toko.index')->with('error', 'Terjadi kesalahan pada server');
        }
    }

    public function edit($id)
    {
        try {
            // Load toko dengan relationships
            // Note: pivot data (jabatan_id) is automatically loaded via withPivot() in the relationship definition
            $toko = TokoModel::with(['owner', 'users'])->findOrFail($id);

            $jabatans = JabatanModel::all();
            $barangs = $toko->barangs()->with('barang.satuan')->paginate(10);

            return view('toko.edit', compact('toko', 'jabatans', 'barangs'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('toko.index')->with('error', 'Toko tidak ditemukan');
        } catch (\Exception $e) {
            Log::error("Error loading toko edit page for ID {$id}: " . $e->getMessage());
            return redirect()->route('toko.index')->with('error', 'Terjadi kesalahan saat memuat halaman edit');
        }
    }

    public function getUserWithoutOrSameToko(Request $request, $tokoId)
    {
        $search = $request->input('q', '');
        $type = $request->input('type');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        if (!in_array($type, ['owner', 'employee'])) {
            return response()->json([
                'count' => 0,
                'next' => null,
                'previous' => null,
                'results' => []
            ]);
        }

        $query = UserModel::where(function ($q) use ($search) {
            $q->where('username', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        })->whereHas('roles', function ($q) {
            $q->whereIn('name', ['shop', 'guest']);
        });


        // Ambil user yang belum punya toko (owner/employee) ATAU user yang ada di toko ini
        $query->where(function ($q) use ($tokoId) {
            $q->where(function ($sub) {
                $sub->whereDoesntHave('ownedTokos')
                    ->whereDoesntHave('tokos');
            })
                ->orWhere(function ($sub) use ($tokoId) {
                    $sub->whereHas('ownedTokos', function ($k) use ($tokoId) {
                        $k->where('id', $tokoId);
                    })
                        ->orWhereHas('tokos', function ($k) use ($tokoId) {
                            $k->where('toko_id', $tokoId);
                        });
                });
        });

        $totalCount = $query->count();
        $users = $query->skip($offset)
            ->take($limit)
            ->get(['id', 'username', 'name']);

        $users->transform(function ($user) {
            $ownedTokos = TokoModel::where('owner_id', $user->id)->first();
            $employeeToko = $user->tokos()->first();

            $user->text = "@" . $user->username;
            if ($ownedTokos) {
                $user->existing_toko = 'Pemilik';
            } elseif ($employeeToko) {
                $user->existing_toko = 'Karyawan';
            } else {
                $user->existing_toko = 'Tidak ada toko';
            }
            return $user;
        });

        $nextOffset = $offset + $limit;
        $hasMore = $nextOffset < $totalCount;

        return response()->json([
            'count' => $totalCount,
            'next' => $hasMore ? route('toko.get-users-wost', ['q' => $search, 'type' => $type, 'offset' => $nextOffset, 'limit' => $limit]) : null,
            'previous' => $offset > 0 ? route('toko.get-users-wost', ['q' => $search, 'type' => $type, 'offset' => max(0, $offset - $limit), 'limit' => $limit]) : null,
            'results' => $users->values()
        ]);
    }
    public function getUserWithoutToko(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 20);

        if (!in_array($type, ['owner', 'employee'])) {
            return response()->json([
                'count' => 0,
                'next' => null,
                'previous' => null,
                'results' => []
            ]);
        }

        $query = UserModel::where(function ($q) use ($search) {
            $q->where('username', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        })->whereHas('roles', function ($q) {
            $q->whereIn('name', ['shop', 'guest']);
        });


        // Ambil user yang belum punya toko
        // Ambil user yang BENAR-BENAR belum punya toko (bukan owner & bukan employee di manapun)
        $query->whereDoesntHave('ownedTokos')
            ->whereDoesntHave('tokos');

        $totalCount = $query->count();
        $users = $query->skip($offset)
            ->take($limit)
            ->get(['id', 'username', 'name']);

        $users->transform(function ($user) {
            $ownedTokos = TokoModel::where('owner_id', $user->id)->first();
            $employeeToko = $user->tokos()->first();

            $user->text = "@" . $user->username;
            if ($ownedTokos) {
                $user->existing_toko = 'Pemilik';
            } elseif ($employeeToko) {
                $user->existing_toko = 'Karyawan';
            } else {
                $user->existing_toko = 'Tidak ada toko';
            }
            return $user;
        });

        $nextOffset = $offset + $limit;
        $hasMore = $nextOffset < $totalCount;

        return response()->json([
            'count' => $totalCount,
            'next' => $hasMore ? route('toko.get-users-wot', ['q' => $search, 'type' => $type, 'offset' => $nextOffset, 'limit' => $limit]) : null,
            'previous' => $offset > 0 ? route('toko.get-users-wot', ['q' => $search, 'type' => $type, 'offset' => max(0, $offset - $limit), 'limit' => $limit]) : null,
            'results' => $users->values()
        ]);
    }


    public function checkUserToko(Request $request)
    {
        $userId = $request->input('user_id');
        $currentTokoId = $request->input('current_toko_id');
        $user = UserModel::find($userId);

        if (!$user) {
            return response()->json(['has_toko' => false]);
        }

        $ownedTokos = TokoModel::where('owner_id', $userId)
            ->when($currentTokoId, function ($q) use ($currentTokoId) {
                $q->where('id', '!=', $currentTokoId);
            })
            ->first();

        if ($ownedTokos) {
            return response()->json([
                'has_toko' => true,
                'role' => 'owner',
                'toko_name' => $ownedTokos->name,
                'message' => "User ini adalah pemilik toko '{$ownedTokos->name}'. Jika dilanjutkan, user akan dipindahkan ke toko baru."
            ]);
        }

        $employeeToko = $user->tokos()
            ->when($currentTokoId, function ($q) use ($currentTokoId) {
                $q->where('toko_id', '!=', $currentTokoId);
            })
            ->first();

        if ($employeeToko) {
            return response()->json([
                'has_toko' => true,
                'role' => 'employee',
                'toko_name' => $employeeToko->name,
                'message' => "User ini adalah karyawan di toko '{$employeeToko->name}'. Jika dilanjutkan, user akan dipindahkan ke toko baru."
            ]);
        }

        return response()->json(['has_toko' => false]);
    }

    public function addEmployee(Request $request, $id)
    {
        try {
            Log::info($request->all());
            $toko = TokoModel::findOrFail($id);
            $userId = $request->input('user_id');
            $jabatanId = $request->input('jabatan_id');

            $user = UserModel::findOrFail($userId);

            // Detach from other tokos if any
            $user->tokos()->detach();
            TokoModel::where('owner_id', $user->id)->update(['owner_id' => null, 'status' => 'pending']);

            // Attach to new toko
            $toko->users()->attach($userId, ['jabatan_id' => $jabatanId]);

            // Reload user with pivot data
            $user = $toko->users()->where('user_id', $userId)->first();
            $jabatans = JabatanModel::all();

            $html = view('toko.partials.employee-row', compact('user', 'jabatans'))->render();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()], 500);
        }
    }

    public function removeEmployee($id, $userId)
    {
        try {
            $toko = TokoModel::findOrFail($id);
            $toko->users()->detach($userId);
            return response()->json(['success' => true, 'message' => 'Karyawan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()], 500);
        }
    }

    public function updateEmployeeJabatan(Request $request, $id, $userId)
    {
        try {
            $toko = TokoModel::findOrFail($id);
            $jabatanId = $request->input('jabatan_id');

            $toko->users()->updateExistingPivot($userId, ['jabatan_id' => $jabatanId]);

            return response()->json(['success' => true, 'message' => 'Jabatan berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui jabatan: ' . $e->getMessage()], 500);
        }
    }

    public function updateProduct(Request $request, $id, $barangId)
    {
        try {
            $toko = TokoModel::findOrFail($id);
            $hargaJual = $request->input('harga_jual');
            $stock = $request->input('jumlah_stock');

            // Find the pivot record
            $barangToko = $toko->barangs()->where('id', $barangId)->first();

            if (!$barangToko) {
                return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan di toko ini'], 404);
            }

            // Update pivot data directly or via relationship if model exists
            // Since we are using BarangToko model in relationship, we might need to update that model directly
            // Or use updateExistingPivot if relationship is BelongsToMany
            // Let's check relationship in TokoModel. It is hasMany BarangTokoModel

            $barangToko->update([
                'harga_jual' => $hargaJual,
                'jumlah_stock' => $stock
            ]);

            return response()->json(['success' => true, 'message' => 'Barang berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui barang: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProduct($id, $barangId)
    {
        try {
            $toko = TokoModel::findOrFail($id);
            $barangToko = $toko->barangs()->where('id', $barangId)->first();

            if (!$barangToko) {
                return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan di toko ini'], 404);
            }

            $barangToko->delete();

            return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus dari toko']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus barang: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $toko = TokoModel::findOrFail($id);
            if ($toko->barangs()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus toko yang masih memiliki barang'], 422);
            }
            if ($toko->users()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus toko yang masih memiliki karyawan'], 422);
            }
            $toko->delete();
            return response()->json(['success' => true, 'message' => 'Toko berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
