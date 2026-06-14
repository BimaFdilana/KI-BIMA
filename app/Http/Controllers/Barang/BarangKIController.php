<?php

namespace App\Http\Controllers\Barang;

use App\DataTables\BarangKIDataTable;
use App\Exports\BarangKI\BarangKiTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\Barang\BarangKI;
use Illuminate\Http\Request;
use App\Helpers\CurrencyHelper;
use App\Jobs\Exports\BarangKI\ExportBarangKIJob;
use App\Models\Barang\BarangIOModel;
use App\Models\Barang\BarangModel;
use App\Models\Barang\Subcategory;
use App\Models\Barang\SatuanItem;
use App\Services\Barang\BarangIOService;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\ConvertSatuanService;
use App\Services\Message\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Imports\BarangKI\BarangKiImport;
use App\Jobs\ImportBarangKiJob;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Facades\Excel;

class BarangKIController extends Controller
{
    protected $barangKIService;
    protected $convertSatuanService;
    protected $barangIOService;
    protected $notifService;

    public function __construct(
        BarangKIService $barangKIService,
        ConvertSatuanService $convertSatuanService,
        BarangIOService $barangIOService,
        NotificationService $notifService
    ) {
        $this->barangKIService = $barangKIService;
        $this->convertSatuanService = $convertSatuanService;
        $this->barangIOService = $barangIOService;
        $this->notifService = $notifService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BarangKIDataTable $dataTable)
    {
        $filter = $request->get('filter', null);
        $subcategories = Subcategory::all();
        // Apply filter and return dataTable
        return $dataTable->setFilter($filter)->render('barangki.index', compact('subcategories'));
    }



    public function getBarangSameId(Request $request)
    {
        $search = $request->get('search');
        $barangId = $request->get('barang_id'); // Get barang_id from request

        // First get all matching items
        $matchingBarang = BarangModel::withTrashed()
            ->where('name', 'like', '%' . $search . '%')
            ->with(['images' => function ($query) {
                $query->where('is_main', true);
            }])
            ->get();

        // Add selected barang if not already in results
        $selectedBarang = BarangModel::withTrashed()
            ->where('id', $barangId)
            ->with(['images' => function ($query) {
                $query->where('is_main', true);
            }])
            ->get();

        // Combine results and remove duplicates
        $allBarang = $matchingBarang->merge($selectedBarang)->unique('id');

        // Order by FIELD to ensure selected barang is first
        $orderedBarang = $allBarang->sortBy(function ($item) use ($barangId) {
            return $item->id == $barangId ? 0 : 1;
        });

        // Format the response with main image URL
        $formattedBarang = $orderedBarang->map(function ($item) use ($barangId) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'main_image_url' => $item->images->first() ? asset('storage/' . $item->images->first()->url) : null,
                'selected' => $item->id == $barangId ? true : false
            ];
        });

        // Take only first 10 items
        $paginatedBarang = $formattedBarang->take(3)->values();

        return response()->json([
            'success' => true,
            'data' => $paginatedBarang
        ]);
    }

    /**
     * Display the specified barang KI detail.
     *
     * @param string $barcode
     * @return View
     */
    public function detail($barcode)
    {
        // Get BarangKI with relationships
        $barangKi = BarangKI::withTrashed()
            ->with(['barang.images', 'barang.subcategory'])
            ->where('id_barcode', $barcode)
            ->first();

        if (!$barangKi) {
            abort(404, 'Barang KI tidak ditemukan');
        }

        // Basic Information
        $mainImage = $barangKi->barang->images->where('is_main', true)->first();
        $discount = $this->barangKIService->calculateDiscount($barangKi);
        $margin = $barangKi->barang->subcategory->margin ?? 0;
        $formattedMargin = number_format($margin, 0);

        // Format dates
        $discountRange = $barangKi->discount_start && $barangKi->discount_end
            ? Carbon::parse($barangKi->discount_start)->format('d M') . ' - ' . Carbon::parse($barangKi->discount_end)->format('d M Y')
            : null;
        $expiredTime = $barangKi->expired_time
            ? Carbon::parse($barangKi->expired_time)->format('d M Y')
            : null;

        // Stock Information
        $smallestSold = $this->convertSatuanService->convertToSmallestUnit($barangKi, $barangKi->sold_quantity);
        $smallestAvailable = $this->convertSatuanService->convertToSmallestUnit($barangKi, $barangKi->quantity);
        $smallestSoldFormatted = CurrencyHelper::formatStock($smallestSold['converted_amount']);
        $smallestAvailableFormatted = CurrencyHelper::formatStock($smallestAvailable['converted_amount']);

        // Date ranges for queries
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $lastMonth = now()->subMonth();

        // Current month IN/OUT data
        $thisMonthIn = BarangIOModel::where('barangki_id', $barangKi->id)
            ->where('type', 'in')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();

        $thisMonthOut = BarangIOModel::where('barangki_id', $barangKi->id)
            ->where('type', 'out')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->get();

        // Last month IN/OUT data
        $lastMonthIn = BarangIOModel::where('barangki_id', $barangKi->id)
            ->where('type', 'in')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->get();

        $lastMonthOut = BarangIOModel::where('barangki_id', $barangKi->id)
            ->where('type', 'out')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->get();

        // Calculate total values for current month
        $totalHargaMasuk = $thisMonthIn->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $totalHargaKeluar = $thisMonthOut->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Calculate total values for last month
        $totalHargaMasukLastMonth = $lastMonthIn->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $totalHargaKeluarLastMonth = $lastMonthOut->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Calculate percentages (with zero division protection)
        $persentaseHargaMasuk = $totalHargaMasukLastMonth > 0
            ? ($totalHargaMasuk / $totalHargaMasukLastMonth) * 100
            : 0;

        $persentaseHargaKeluar = $totalHargaKeluarLastMonth > 0
            ? ($totalHargaKeluar / $totalHargaKeluarLastMonth) * 100
            : 0;

        // Calculate total quantities (converted to smallest unit)
        $totalMasuk = $thisMonthIn->sum(function ($item) {
            $converted = $this->convertSatuanService->convertToSmallestUnit($item, $item->quantity);
            return $converted['converted_amount'];
        });

        $totalKeluar = $thisMonthOut->sum(function ($item) {
            $converted = $this->convertSatuanService->convertToSmallestUnit($item, $item->quantity);
            return $converted['converted_amount'];
        });

        // Calculate last month quantities for comparison
        $totalMasukLastMonth = $lastMonthIn->sum(function ($item) {
            $converted = $this->convertSatuanService->convertToSmallestUnit($item, $item->quantity);
            return $converted['converted_amount'];
        });

        $totalKeluarLastMonth = $lastMonthOut->sum(function ($item) {
            $converted = $this->convertSatuanService->convertToSmallestUnit($item, $item->quantity);
            return $converted['converted_amount'];
        });

        // Calculate quantity percentages
        $persentaseMasuk = $totalMasukLastMonth > 0
            ? ($totalMasuk / $totalMasukLastMonth) * 100
            : 0;

        $persentaseKeluar = $totalKeluarLastMonth > 0
            ? ($totalKeluar / $totalKeluarLastMonth) * 100
            : 0;

        // Additional analytics
        $turnoverRatio = $smallestAvailable['converted_amount'] > 0
            ? $totalKeluar / $smallestAvailable['converted_amount']
            : 0;

        $netMovement = $totalMasuk - $totalKeluar;
        $stockValue = $barangKi->price * $smallestAvailable['converted_amount'];

        return view('barangki.detail', [
            // Basic Data
            'barangKi' => $barangKi,
            'mainImage' => $mainImage,
            'discount' => $discount,
            'margin' => $margin,
            'formattedMargin' => $formattedMargin,
            'discountRange' => $discountRange,
            'expiredTime' => $expiredTime,

            // Stock Data
            'smallestSold' => $smallestSold,
            'smallestAvailable' => $smallestAvailable,
            'smallestSoldFormatted' => $smallestSoldFormatted,
            'smallestAvailableFormatted' => $smallestAvailableFormatted,

            // Current Month Financial Data
            'totalHargaMasuk' => CurrencyHelper::formatStock($totalHargaMasuk),
            'totalHargaKeluar' => CurrencyHelper::formatStock($totalHargaKeluar),
            'rawTotalHargaMasuk' => $totalHargaMasuk,
            'rawTotalHargaKeluar' => $totalHargaKeluar,

            // Last Month Financial Data
            'totalHargaMasukLastMonth' => CurrencyHelper::formatStock($totalHargaMasukLastMonth),
            'totalHargaKeluarLastMonth' => CurrencyHelper::formatStock($totalHargaKeluarLastMonth),
            'rawTotalHargaMasukLastMonth' => $totalHargaMasukLastMonth,
            'rawTotalHargaKeluarLastMonth' => $totalHargaKeluarLastMonth,

            // Financial Percentages
            'persentaseHargaMasuk' => round($persentaseHargaMasuk, 2),
            'persentaseHargaKeluar' => round($persentaseHargaKeluar, 2),

            // Current Month Quantity Data
            'totalMasuk' => CurrencyHelper::formatStock($totalMasuk),
            'totalKeluar' => CurrencyHelper::formatStock($totalKeluar),
            'rawTotalMasuk' => $totalMasuk,
            'rawTotalKeluar' => $totalKeluar,

            // Last Month Quantity Data
            'totalMasukLastMonth' => CurrencyHelper::formatStock($totalMasukLastMonth),
            'totalKeluarLastMonth' => CurrencyHelper::formatStock($totalKeluarLastMonth),
            'rawTotalMasukLastMonth' => $totalMasukLastMonth,
            'rawTotalKeluarLastMonth' => $totalKeluarLastMonth,

            // Quantity Percentages
            'persentaseMasuk' => round($persentaseMasuk, 2),
            'persentaseKeluar' => round($persentaseKeluar, 2),

            // Additional Analytics
            'turnoverRatio' => round($turnoverRatio, 4),
            'netMovement' => CurrencyHelper::formatStock($netMovement),
            'rawNetMovement' => $netMovement,
            'stockValue' => CurrencyHelper::formatStock($stockValue),
            'rawStockValue' => $stockValue,

            // Collections for detailed analysis
            'thisMonthInData' => $thisMonthIn,
            'thisMonthOutData' => $thisMonthOut,
            'lastMonthInData' => $lastMonthIn,
            'lastMonthOutData' => $lastMonthOut,

            // Status indicators
            'isStockLow' => $smallestAvailable['converted_amount'] < ($barangKi->min_stock ?? 10),
            'isExpiringSoon' => $barangKi->expired_time && Carbon::parse($barangKi->expired_time)->diffInDays(now()) <= 30,
            'hasDiscount' => $discount > 0,
            'isActive' => !$barangKi->trashed(),
        ]);
    }

    public function tambah_barang()
    {
        $barang = BarangModel::all();

        return view('barangki.tambah-barang', compact('barang'));
    }


    public function findBarcode(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'barcode' => 'required|string|max:255',
            ]);

            $barcode = $request->barcode;

            // Try multiple approaches to find the barcode
            $barangKi = BarangKI::withTrashed()
                ->where('id_barcode', $barcode)
                ->orWhere('id_barcode', 'LIKE', '%' . $barcode . '%')
                ->first();

            if (!$barangKi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan untuk barcode ' . $barcode,
                    'barcode' => $barcode
                ]);
            }


            // Format the response
            $formattedBarangKi = [
                'id' => $barangKi->id,
                'id_barcode' => $barangKi->id_barcode,
                'quantity' => $barangKi->quantity ?? 0,
                'price_buy' => $barangKi->price_buy ?? 0,
                'price_sell' => $barangKi->price_sell ?? 0,
                'price_up' => $barangKi->price_up ?? 0,
                'expired_time_date' => $barangKi->expired_time ?
                    Carbon::parse($barangKi->expired_time)->format('Y-m-d') : null,
                'expired_time_time' => $barangKi->expired_time ?
                    Carbon::parse($barangKi->expired_time)->format('H:i') : null,
                'discount_start' => $barangKi->discount_start,
                'discount_end' => $barangKi->discount_end,
                'discount_type' => $barangKi->discount_amount ? 'amount' : ($barangKi->discount_percentage ? 'percentage' : null),
                'discount' => $barangKi->discount_amount ?? $barangKi->discount_percentage ?? null,
                'status' => $barangKi->status,
                'satuan_id' => $barangKi->satuan_id,
                'barang_id' => $barangKi->barang_id,
                'name' => $barangKi->barang->name ?? 'Tidak tersedia',
                'satuan' => [
                    'name' => $barangKi->satuan ? $barangKi->satuan->name : 'Tidak tersedia',
                    'cut_name' => $barangKi->satuan ? $barangKi->satuan->cut_name : 'Tidak tersedia'
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Barang ditemukan untuk barcode ' . $barcode,
                'data' => $formattedBarangKi
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSatuanConvertBarang(Request $request)
    {
        $barangId = $request->barang_id;
        $satuanId = $request->satuan_id;
        if (!$barangId) {
            return response()->json([
                'success' => false,
                'message' => 'Barang ID dan Satuan ID harus diisi'
            ]);
        }
        $barangKi = BarangKI::where('barang_id', $barangId)->first();
        $satuan = SatuanItem::whereHas('fromConversions', function ($query) use ($barangKi) {
            $query->where('barang_id', $barangKi->barang_id);
        })->orWhereHas('conversionTo', function ($query) use ($barangKi) {
            $query->where('barang_id', $barangKi->barang_id);
        })
            ->distinct()
            ->get();

        if ($satuan->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan'
            ]);
        }

        $formattedSatuan = $satuan->map(function ($item) use ($satuanId) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'level' => $item->level,
                'selected' => $item->id == $satuanId ? true : false
            ];
        });


        return response()->json([
            'success' => true,
            'data' => $formattedSatuan
        ]);
    }

    /**
     * Process DataTables ajax request.
     */
    public function getBarangKI(Request $request)
    {
        $filter = $request->get('filter', null);

        $query = BarangKI::with(['barang', 'barang.images', 'barang.subcategory', 'satuan']);

        // Apply filter logic based on request
        if ($filter === 'ongoing') {
            $query->where(function ($query) {
                $query->where('discount_start', '<=', now())
                    ->where('discount_end', '>=', now());
            });
        } elseif ($filter === 'coming') {
            $query->where(function ($query) {
                $query->where('discount_start', '>', now());
            });
        } elseif ($filter === 'expired') {
            $query->where('expired_time', '<=', now());
        } elseif ($filter === 'no_discount') {
            $query->where(function ($query) {
                $query->whereNull('discount_amount')
                    ->whereNull('discount_percentage');
            });
        }

        // Create DataTable instance manually for direct AJAX calls
        return DataTables::of($query)
            // Add your custom columns here
            // Similar to what's in the DataTable class
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required|exists:barang,id',
            'id_barcode' => 'required|unique:barang_ki,id_barcode',
            'stock' => 'required|numeric',
            'price_buy' => 'required|numeric',
            'price_sell' => 'required|numeric',
            'price_up' => 'nullable|numeric',
            'expired_time' => 'required|date',
            'satuan_id' => 'required|exists:satuan_items,id',
            'status' => 'required|in:active,nonactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }
        $barang = BarangModel::findOrFail($request->barang_id);

        $price_up = 0;
        if ($request->price_up == null) {
            $price_up = $barang->subcategory->margin;
        }
        $barangKi = BarangKI::create([
            'barang_id' => $request->barang_id,
            'id_barcode' => $request->id_barcode,
            'quantity' => $request->stock,
            'price_buy' => $request->price_buy,
            'price_sell' => $request->price_sell,
            'price_up' => $price_up,
            'expired_time' => $request->expired_time,
            'satuan_id' => $request->satuan_id,
            'status' => $request->status,
        ]);

        $this->barangIOService->addBarangAdmin($barangKi, $request->stock, Auth::user(), $request->price_buy);
        return response()->json([
            'success' => true,
            'message' => 'Barang KI berhasil ditambahkan',
            'data' => $barangKi
        ]);
    }

    public function update(Request $request, $id)
    {
        $barangKi = BarangKI::findOrFail($id);

        // Remove unique validation for barcode since it's causing issues
        $validator = Validator::make($request->all(), [
            'barang' => 'required|exists:barang,id',
            'barcode' => 'required',
            'satuan' => 'required|exists:satuan_items,id',
            'quantity' => 'required|numeric',
            'price_buy' => 'required|numeric',
            'price_sell' => 'required|numeric',
            'price_up' => 'required|numeric',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date',
            'discount_type' => 'nullable|in:percentage,amount',
            'discount' => function ($attribute, $value, $fail) use ($request) {
                if ($request->discount_start || $request->discount_end || $request->discount_type) {
                    if (!$value) {
                        $fail('Diskon harus diisi');
                    }
                }
            },
            'status' => 'required|in:active,nonactive',
            'expired_time_date' => 'required|date',
            'expired_time_time' => 'required',
        ], [
            'barang.required' => 'Barang harus dipilih',
            'barang.exists' => 'Barang tidak ditemukan',
            'barcode.required' => 'Barcode harus diisi',
            'barcode.unique' => 'Barcode sudah digunakan',
            'satuan.required' => 'Satuan harus dipilih',
            'satuan.exists' => 'Satuan tidak ditemukan',
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.numeric' => 'Jumlah harus berupa angka',
            'price_buy.required' => 'Harga beli harus diisi',
            'price_buy.numeric' => 'Harga beli harus berupa angka',
            'price_sell.required' => 'Harga jual harus diisi',
            'price_sell.numeric' => 'Harga jual harus berupa angka',
            'price_up.required' => 'Harga up harus diisi',
            'price_up.numeric' => 'Harga up harus berupa angka',
            'discount_start.date' => 'Tanggal mulai diskon harus berupa tanggal',
            'discount_end.date' => 'Tanggal akhir diskon harus berupa tanggal',
            'discount_type.in' => 'Tipe diskon harus berupa percentage atau amount',
            'discount.required' => 'Diskon harus diisi',
            'discount.numeric' => 'Diskon harus berupa angka',
            'status.required' => 'Status harus diisi',
            'status.in' => 'Status harus berupa active atau nonactive',
            'expired_time_date.required' => 'Tanggal kadaluarsa harus diisi',
            'expired_time_date.date' => 'Tanggal kadaluarsa harus berupa tanggal',
            'expired_time_time.required' => 'Waktu kadaluarsa harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Manual unique validation for barcode (only if barcode is changing)
        if ($barangKi->id_barcode !== $request->barcode) {
            $existingBarcode = BarangKI::where('id_barcode', $request->barcode)
                ->where('id', '!=', $barangKi->id)
                ->exists();

            if ($existingBarcode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => ['barcode' => ['Barcode sudah digunakan']]
                ], 422);
            }
        }

        $discount_amount = null;
        $discount_percentage = null;

        if ($request->discount_type == 'percentage') {
            $discount_percentage = $request->discount;
        } elseif ($request->discount_type == 'amount') {
            $discount_amount = $request->discount;
        }

        $oldBarcode = $barangKi->id_barcode;
        $newBarcode = $request->barcode;
        $link = null;
        if ($oldBarcode != $newBarcode) {
            $link = $oldBarcode;
        }

        $rangeQuantity = $request->quantity - $barangKi->quantity;
        if ($rangeQuantity > 0) {
            $this->barangIOService->addBarangAdmin($barangKi, $rangeQuantity, Auth::user(), $request->price_buy);
        } elseif ($rangeQuantity < 0) {
            $this->barangIOService->removeBarangAdmin($barangKi, $rangeQuantity, Auth::user(), $request->price_buy);
        }

        $barangKi->update([
            'barang_id' => $request->barang,
            'id_barcode' => $request->barcode,
            'quantity' => $request->quantity,
            'price_buy' => $request->price_buy,
            'price_sell' => $request->price_sell,
            'price_up' => $request->price_up,
            'discount_start' => $request->discount_start,
            'discount_end' => $request->discount_end,
            'discount_amount' => $discount_amount,
            'discount_percentage' => $discount_percentage,
            'status' => $request->status,
            'expired_time' => $request->expired_time_date . ' ' . $request->expired_time_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil di update',
            'data' => $barangKi,
            'link' => $link
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($barcode)
    {
        DB::beginTransaction();
        try {
            $barangKi = BarangKI::where('id_barcode', $barcode)->withTrashed()->first();
            if (!$barangKi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            if ($barangKi->trashed()) {
                $barangKi->restore();
                $message = 'Barang berhasil di restore.';
            } else {
                if ($barangKi->barangtoko->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Barang tidak dapat dihapus karena ada data di BarangToko.'
                    ], 400);
                }
                $barangKi->delete();
                $message = 'Barang berhasil dihapus.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 500);
        }
    }


    public function getDataFromSameExpired(Request $request)
    {
        $barangKi = BarangKI::withTrashed()->where('id_barcode', $request->query('barcode'))->first();
        $action = $request->query('action');

        if (!$barangKi) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        $allbarangki = [];
        // Hitung summary dulu dengan semua data (tanpa pagination)
        if ($action === 'delete') {
            $allbarangki = BarangKI::where('barang_id', $barangKi->barang_id)
                ->where('expired_time', $barangKi->expired_time)
                ->whereDoesntHave('barangtoko')
                ->get();
        } else {
            $allbarangki = BarangKI::where('barang_id', $barangKi->barang_id)
                ->where('expired_time', $barangKi->expired_time)
                ->get();
        }

        $totalSold = 0;
        foreach ($allbarangki as $item) {
            $convert = $this->convertSatuanService->convertToSmallestUnit($item, $item->sold_quantity);
            $totalSold += $convert['converted_amount'];
        }

        $totalAvailable = $this->convertSatuanService->convertBarangKeTerkecilDatatables($allbarangki)['total'];
        $totalStock = $totalSold + $totalAvailable;

        $barangki = [];
        if ($action === 'delete') {
            $barangki = BarangKI::where('barang_id', $barangKi->barang_id)
                ->where('expired_time', $barangKi->expired_time)
                ->whereDoesntHave('barangtoko')
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10)); // default 10 per page
        } else {
            $barangki = BarangKI::where('barang_id', $barangKi->barang_id)
                ->where('expired_time', $barangKi->expired_time)
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10)); // default 10 per page
        }
        // Transform data for DataTable
        $data = $barangki->map(function ($item) {
            $status = null;
            if ($item->deleted_at) {
                $status = 'deleted';
            } else {
                $status = $item->status;
            }
            return [
                'id' => $item->id,
                'name' => $item->barang->name,
                'barcode' => $item->id_barcode,
                'satuan' => $item->satuan->name ?? 'N/A',
                'quantity' => CurrencyHelper::formatStock($item->quantity) . ' ' . ($item->satuan->name ?? ''),
                'sold_quantity' => CurrencyHelper::formatStock($item->sold_quantity) . ' ' . ($item->satuan->name ?? ''),
                'available_quantity' => CurrencyHelper::formatStock($item->quantity - $item->sold_quantity) . ' ' . ($item->satuan->name ?? ''),
                'price_buy' => 'Rp ' . number_format($item->price_buy, 0, ',', '.'),
                'price_sell' => 'Rp ' . number_format($item->price_sell, 0, ',', '.'),
                'expired_time' => $item->expired_time ? Carbon::parse($item->expired_time)->format('d/m/Y') : 'N/A',
                'expired_time_value' => $item->expired_time,
                'status' => $status,
                'status_badge' => $this->barangKIService->getStatusBadge($status),
                'expiry_status' => $this->barangKIService->getExpiryStatus($item->expired_time, $item->barang->early_expiry_days, $item->barang->mid_expiry_days, $item->barang->late_expiry_days),
                'discount' => $this->barangKIService->applyDiscountsToBarang($item),
                'deleted_at' => $item->deleted_at,
                'barangtoko_count' => $item->barangtoko->count(),
                'can_view' => auth()->user()->can('view.barang.ki'),
                'can_edit' => auth()->user()->can('edit.barang.ki'),
                'can_delete' => auth()->user()->can('delete.barang.ki')

            ];
        });

        return response()->json([
            'success' => true,
            'barangki' => [
                'barang' => $barangKi->barang->name,
                'data' => $data,
                'current_page' => $barangki->currentPage(),
                'last_page' => $barangki->lastPage(),
                'per_page' => $barangki->perPage(),
                'total' => $barangki->total(),
                'from' => $barangki->firstItem(),
                'to' => $barangki->lastItem(),
            ],
            'summary' => [
                'total_stock' => $totalStock,
                'total_sold' => $totalSold,
                'total_available' => $totalAvailable
            ],

        ]);
    }

    /**
     * Process batch actions
     */
    public function batchAction(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;

        if (empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        switch ($action) {
            case 'delete':
                BarangKI::whereIn('id', $ids)->delete();
                return response()->json(['success' => 'Items deleted successfully']);

            case 'active':
                BarangKI::whereIn('id', $ids)->update(['status' => 'active']);
                return response()->json(['success' => 'Items activated successfully']);

            case 'nonactive':
                BarangKI::whereIn('id', $ids)->update(['status' => 'nonactive']);
                return response()->json(['success' => 'Items deactivated successfully']);

            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    public function addStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|exists:barang_ki,id_barcode',
            'quantity' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        try {
            $barangKi = BarangKI::where('id_barcode', $request->barcode)->first();
            $rangeQuantity = $request->quantity - $barangKi->quantity;
            if ($rangeQuantity > 0) {
                $this->barangIOService->addBarangAdmin($barangKi, $rangeQuantity, Auth::user(), $request->price_buy);
            } elseif ($rangeQuantity < 0) {
                $this->barangIOService->removeBarangAdmin($barangKi, $rangeQuantity, Auth::user(), $request->price_buy);
            }
            $barangKi->quantity = $request->quantity;
            $barangKi->save();

            return response()->json([
                'success' => true,
                'data' => $barangKi
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request, $format)
    {
        $user = Auth::user();
        $filters = $request->except(['format', 'page']);

        if (!in_array($format, ['excel', 'csv', 'pdf'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format tidak didukung.',
            ]);
        }

        $extension = match ($format) {
            'csv' => 'csv',
            'pdf' => 'pdf',
            default => 'xlsx',
        };

        $fileName = "barang_ki_{$user->id}_" . now()->format('Y_m_d_H_i_s') . ".{$extension}";
        $filePath = "/exports/{$fileName}";

        $notification = $this->notifService->sendToUserFromSystem(
            $user,
            'export_data',
            [
                'message' => 'Export sedang diproses di background. Anda akan diberitahu saat selesai.',
                'title' => 'Export Queued: Barang KI (' . strtoupper($format) . ')',
            ],
            $filePath
        );

        ExportBarangKIJob::dispatch(
            $filters,
            $user,
            $format,
            $notification->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Export sedang diproses di background. Anda akan diberitahu saat selesai.',
            'time' => now()->diffForHumans(),
        ]);
    }

    public function downloadTemplate()
    {
        try {
            $fileName = 'Template_Import_Barang_KI_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new BarangKiTemplateExport(), $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data dari Excel - langsung diproses (synchronous)
     */
    public function import(Request $request)
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120' // 5MB max
            ]
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes' => 'Format file harus .xlsx atau .xls',
            'file.max' => 'Ukuran file maksimal 5MB'
        ]);

        if ($validator->fails()) {
            Log::warning('Import Barang KI: validasi file gagal', [
                'errors' => $validator->errors()->toArray(),
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $importId = (string) Str::uuid();

            Log::info('Import Barang KI: mulai proses', [
                'import_id'   => $importId,
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $file->getSize(),
                'user_id'     => auth()->id(),
            ]);

            // Jalankan import langsung (tanpa queue)
            $import = new BarangKiImport($importId);
            ExcelFacade::import($import, $file);

            $errors       = $import->getErrors();
            $successCount = $import->getSuccessCount();
            $skippedCount = $import->getSkippedCount();

            Log::info('Import Barang KI: selesai', [
                'import_id'     => $importId,
                'success_count' => $successCount,
                'skipped_count' => $skippedCount,
                'error_count'   => count($errors),
                'errors'        => $errors,
            ]);

            if ($successCount === 0 && count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal. Tidak ada data yang berhasil diimpor.',
                    'errors'  => $errors,
                    'summary' => [
                        'success' => $successCount,
                        'skipped' => $skippedCount,
                        'errors'  => count($errors),
                    ],
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Import selesai. {$successCount} data berhasil diimpor" .
                             ($skippedCount ? ", {$skippedCount} baris dilewati" : '') .
                             (count($errors) ? ", " . count($errors) . " baris error." : '.'),
                'summary' => [
                    'success' => $successCount,
                    'skipped' => $skippedCount,
                    'errors'  => count($errors),
                ],
                'errors'   => $errors,
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            Log::error('Import Barang KI: ValidationException', [
                'errors' => $errorMessages,
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi data excel gagal.',
                'errors'  => $errorMessages,
            ], 422);

        } catch (\Exception $e) {
            Log::error('Import Barang KI: exception tidak terduga', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cek status import
     */
    public function checkImportStatus(Request $request)
    {
        $importId = $request->get('import_id');

        // Cek dari cache atau database status import
        $status = cache()->get("import_barang_ki_status_{$importId}");
        $progress = cache()->get("import_barang_ki_progress_{$importId}", 0);
        $errors = cache()->get("import_barang_ki_errors_{$importId}", []);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Import ID tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'progress' => $progress,
            'errors' => $errors
        ]);
    }

    /**
     * Preview data dari Excel sebelum import
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $import = new BarangKiImport();

            // Load data tanpa menyimpan
            $data = Excel::toArray($import, $file);

            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau format tidak valid'
                ], 422);
            }

            // Ambil sheet pertama (Template Import)
            $rows = $data[0];

            // Skip header dan contoh data
            $dataRows = array_slice($rows, 4); // Mulai dari baris ke-5

            // Ambil maksimal 10 baris untuk preview
            $previewData = array_slice($dataRows, 0, 10);

            return response()->json([
                'success' => true,
                'data' => $previewData,
                'total_rows' => count($dataRows)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error membaca file: ' . $e->getMessage()
            ], 500);
        }
    }
}
