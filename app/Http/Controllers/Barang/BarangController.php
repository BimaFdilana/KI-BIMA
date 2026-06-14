<?php

namespace App\Http\Controllers\Barang;

use App\DataTables\BarangDataTable;
use App\Models\Barang\BarangModel;
use App\Models\Barang\Brand;
use App\Models\Barang\TypeItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangMasterImport;
use App\Exports\BarangExport;
use App\Helpers\CurrencyHelper;
use App\Models\Barang\Image;
use App\Models\Barang\SatuanItem;
use App\Models\Barang\Subcategory;
use App\Services\Barang\ConvertSatuanService;
use Carbon\Carbon;
use PDF;
use Exception;
use Yajra\DataTables\DataTables;

class BarangController extends Controller
{
    public function __construct(
        protected ConvertSatuanService $convertSatuanService,
    ) {
        $this->convertSatuanService = $convertSatuanService;
    }
    /**
     * Display a listing of the resource.
     *
     * @param \App\DataTables\BarangDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(BarangDataTable $dataTable, Request $request)
    {
        $filter = $request->get('filter', null);

        $brands = Brand::all();
        $types = TypeItem::all();
        $subcategories = Subcategory::all();
        $jumlahBarang = BarangModel::withTrashed()->count();
        $totalActive = BarangModel::withTrashed()->where('status', 'active')->count();
        $totalInactive = BarangModel::withTrashed()->where('status', 'inactive')->count();


        $totalActivePercentage = $jumlahBarang > 0 ? $totalActive / $jumlahBarang * 100 : 0;
        $totalNonActivePercentage = $jumlahBarang > 0 ? $totalInactive / $jumlahBarang * 100 : 0;

        $totalNewThisMonth = BarangModel::withTrashed()->whereMonth('created_at', now()->month)->count();


        return $dataTable->setFilter($filter)->render('barang.index', compact(
            'brands',
            'types',
            'subcategories',
            'jumlahBarang',
            'totalActive',
            'totalInactive',
            'totalActivePercentage',
            'totalNonActivePercentage',
            'totalNewThisMonth',
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['status', 'subcategory_id', 'brand_id']);

        // For large datasets, use queued export
        $totalRecords = BarangModel::count();

        if ($totalRecords > 10000) {
            // Queue the export for large datasets
            $filename = 'barang_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

            ProcessBarangExport::dispatch($filters, $filename, auth()->user());

            return response()->json([
                'message' => 'Export sedang diproses. Anda akan mendapat notifikasi ketika selesai.',
                'status' => 'queued'
            ]);
        }

        // Direct export for smaller datasets
        $filename = 'barang_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new BarangExport($filters), $filename);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240', // 10MB
            ]
        ], [
            'file.required' => 'File harus dipilih',
            'file.mimes'    => 'Format file harus .xlsx atau .xls',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $file   = $request->file('file');
            $import = new BarangMasterImport();

            Excel::import($import, $file);

            $errors       = $import->getErrors();
            $successCount = $import->getSuccessCount();
            $updatedCount = $import->getUpdatedCount();
            $skippedCount = $import->getSkippedCount();

            if ($successCount === 0 && $updatedCount === 0 && count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import gagal. Tidak ada data yang berhasil diimpor.',
                    'errors'  => $errors,
                    'summary' => [
                        'created' => $successCount,
                        'updated' => $updatedCount,
                        'skipped' => $skippedCount,
                        'errors'  => count($errors),
                    ],
                ], 422);
            }

            $msg = "Import selesai. {$successCount} data baru ditambahkan";
            if ($updatedCount) $msg .= ", {$updatedCount} data diperbarui";
            if ($skippedCount) $msg .= ", {$skippedCount} baris dilewati";
            if (count($errors)) $msg .= ', ' . count($errors) . ' baris error.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'summary' => [
                    'created' => $successCount,
                    'updated' => $updatedCount,
                    'skipped' => $skippedCount,
                    'errors'  => count($errors),
                ],
                'errors'  => $errors,
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->failures() as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return response()->json([
                'success' => false,
                'message' => 'Validasi data excel gagal.',
                'errors'  => $errorMessages,
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        try {
            $filename = 'Template_Import_Barang_' . date('Y-m-d') . '.xlsx';

            return Excel::download(
                new \App\Exports\BarangMasterTemplateExport(),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload template: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkImportStatus($jobId)
    {
        // Check job status in queue
        $job = Queue::where('id', $jobId)->first();

        if (!$job) {
            return response()->json([
                'status' => 'completed',
                'message' => 'Import selesai'
            ]);
        }

        return response()->json([
            'status' => 'processing',
            'message' => 'Import sedang diproses...'
        ]);
    }

    private function generateSKU($name)
    {
        // Clean and prepare the name
        $cleanName = trim($name);
        \Log::info('Clean Name: ' . $cleanName);

        // Extract brand initials (letters before numbers)
        preg_match('/([a-zA-Z\s]+)(?=\d)/', $cleanName, $matches);
        $brand = '';

        if (isset($matches[1])) {
            // Get the letters part before numbers
            $lettersPart = trim($matches[1]);

            // Split by spaces to get words
            $words = explode(' ', $lettersPart);

            if (count($words) == 1) {
                // Only 1 word: take first and last letter
                $word = $words[0];
                if (strlen($word) == 1) {
                    $brand = strtoupper($word);
                } else {
                    $brand = strtoupper(substr($word, 0, 1) . substr($word, -1));
                }
            } else {
                // Multiple words: take first letter of each word (max 2)
                $brand = '';
                for ($i = 0; $i < min(count($words), 2); $i++) {
                    $brand .= strtoupper(substr($words[$i], 0, 1));
                }
            }
        } else {
            // If no numbers found, apply same logic to entire name
            $words = explode(' ', $cleanName);

            if (count($words) == 1) {
                // Only 1 word: take first and last letter
                $word = $words[0];
                if (strlen($word) == 1) {
                    $brand = strtoupper($word);
                } else {
                    $brand = strtoupper(substr($word, 0, 1) . substr($word, -1));
                }
            } else {
                // Multiple words: take first letter of each word (max 2)
                $brand = '';
                for ($i = 0; $i < min(count($words), 2); $i++) {
                    $brand .= strtoupper(substr($words[$i], 0, 1));
                }
            }
        }

        // Extract size and unit
        preg_match('/(\d+\s*[a-zA-Z]+)$/', $cleanName, $matches);
        if (isset($matches[1])) {
            // Clean size and unit
            $sizeUnit = strtoupper(preg_replace('/[^A-Z0-9]/', '', $matches[1]));
        } else {
            // Default to '0' if no size is found
            $sizeUnit = '0';
        }

        // Base SKU format: Brand + Size + Unit
        $baseSku = $brand . $sizeUnit;

        // Ensure minimum 5 characters by padding with '0' if needed
        if (strlen($baseSku) < 5) {
            $baseSku = str_pad($baseSku, 5, '0', STR_PAD_RIGHT);
        }

        // Ensure maximum 5 characters for the base part
        if (strlen($baseSku) > 5) {
            $baseSku = substr($baseSku, 0, 5);
        }


        // Generate unique SKU
        $sku = $baseSku;
        $counter = 1;

        // Check if SKU already exists and generate unique one
        while (BarangModel::withTrashed()->where('sku', $sku)->exists()) {
            // For counter, we need to ensure total length doesn't exceed reasonable limit
            // Replace last digit(s) with counter
            if ($counter < 10) {
                // Single digit counter
                $sku = substr($baseSku, 0, 4) . $counter;
            } elseif ($counter < 100) {
                // Two digit counter
                $sku = substr($baseSku, 0, 3) . $counter;
            } elseif ($counter < 1000) {
                // Three digit counter
                $sku = substr($baseSku, 0, 2) . $counter;
            } else {
                // If we reach 1000, something is seriously wrong
                throw new \Exception('Unable to generate unique SKU after 1000 attempts');
            }

            $counter++;
        }

        return $sku;
    }

    public function getsku(Request $request)
    {
        return response()->json(['sku' => $this->generateSKU($request->name)]);
    }

    public function tambah_barang()
    {
        $brands = Brand::all();
        $subcategories = Subcategory::all();
        $types = TypeItem::all();
        $satuans = SatuanItem::all();
        $tipe_unit = SatuanItem::all()->pluck('type')->unique()->values();

        return view('barang.tambah-barang', compact('brands', 'subcategories', 'types', 'satuans', 'tipe_unit'));
    }

    public function edit_barang($sku)
    {
        $brands = Brand::all();
        $subcategories = Subcategory::all();
        $types = TypeItem::all();
        $satuans = SatuanItem::all();

        $barang = BarangModel::where('sku', $sku)->first();
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan');
        }
        $tipe_unit = SatuanItem::all()->pluck('type')->unique()->values();
        // Fetch images and conversions
        $images = $barang->images; // Get associated images
        $conversions = $barang->fromConversions; // Get associated conversions
        return view('barang.edit-barang', compact('brands', 'subcategories', 'types', 'satuans', 'tipe_unit', 'barang', 'images', 'conversions'));
    }

    public function show_barang($sku)
    {
        $barang = BarangModel::where('sku', $sku)->first();
        if (!$barang) {
            return redirect()->route('barang.index')->with('error', 'Barang tidak ditemukan');
        }
        $images = $barang->images;
        $conversions = $barang->fromConversions;
        $page = request()->query('page', 1);
        $show_barangki = request()->query('show_barangki', 10);
        $barangki = $barang->barangki()->paginate($show_barangki, ['*'], 'page', $page);
        return view('barang.show-barang', compact('barang', 'images', 'conversions', 'barangki', 'show_barangki'));
    }


    public function getSatuanByType(Request $request)
    {
        $type = $request->type;
        $satuan = SatuanItem::where('type', $type)->get();

        if ($satuan->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'satuan' => $satuan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|exists:brands,id',
            'kebutuhan' => 'required|exists:type_barang,id',
            'description' => 'nullable|string',
            'early_expiry_days' => 'nullable|integer|min:0',
            'subcategory' => 'required|exists:sub_categories,id',
            'mid_expiry_days' => 'nullable|integer|min:0',
            'late_expiry_days' => 'nullable|integer|min:0',
            // Conversion validation
            'from_unit' => 'nullable|array',
            'from_unit.*' => 'nullable|exists:satuan_items,id',
            'to_unit' => 'nullable|array',
            'to_unit.*' => 'nullable|exists:satuan_items,id',
            'conversion_factor' => 'nullable|array',
            'conversion_factor.*' => 'nullable|numeric|min:0.001',
            // Image validation
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        // Custom validation for unit conversions
        $fromUnits = $request->from_unit ?? [];
        $toUnits = $request->to_unit ?? [];
        $conversionFactors = $request->conversion_factor ?? [];
        $validConversions = [];
        $seenCombinations = [];
        $errors = [];

        for ($i = 0; $i < count($fromUnits); $i++) {
            // Skip if any value is null or empty
            if (empty($fromUnits[$i]) || empty($toUnits[$i]) || empty($conversionFactors[$i])) {
                continue;
            }

            $fromUnit = $fromUnits[$i];
            $toUnit = $toUnits[$i];
            $factor = $conversionFactors[$i];

            // Check if from_unit and to_unit are the same
            if ($fromUnit == $toUnit) {
                $errors["conversion_{$i}"] = "Unit asal dan unit tujuan tidak boleh sama pada baris " . ($i + 1);
                continue;
            }

            // Create combination keys for both directions
            $forwardKey = $fromUnit . '-' . $toUnit;
            $reverseKey = $toUnit . '-' . $fromUnit;

            // Check for duplicates
            if (in_array($forwardKey, $seenCombinations) || in_array($reverseKey, $seenCombinations)) {
                $errors["conversion_{$i}"] = "Kombinasi unit sudah ada pada baris " . ($i + 1);
                continue;
            }

            // Add both directions to seen combinations
            $seenCombinations[] = $forwardKey;
            $seenCombinations[] = $reverseKey;
            $validConversions[] = [
                'from_unit' => $fromUnit,
                'to_unit' => $toUnit,
                'factor' => $factor
            ];
        }
        // Return errors if any validation failed
        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi unit conversion gagal',
                'errors' => $errors
            ], 422);
        }
        // Check if at least one valid conversion exists
        if (empty($validConversions)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal satu unit conversion harus diisi dengan benar',
                'errors' => ['conversions' => 'Tidak ada unit conversion yang valid']
            ], 422);
        }
        DB::beginTransaction();
        try {
            // Create barang
            $barang = new BarangModel();
            $barang->name = $request->name;
            $barang->sku = $request->sku ?? $this->generateSKU($request->name);
            $barang->brand_id = $request->brand;
            $barang->type_id = $request->kebutuhan;
            $barang->subcategory_id = $request->subcategory;
            $barang->description = $request->description ?? 'No Description';
            $barang->early_expiry_days = $request->early_expiry_days;
            $barang->mid_expiry_days = $request->mid_expiry_days;
            $barang->late_expiry_days = $request->late_expiry_days;
            $barang->save();
            // Handle multiple unit conversions using validated data
            foreach ($validConversions as $conversion) {
                // Create forward conversion
                $barang->fromConversions()->create([
                    'from_satuan_id' => $conversion['from_unit'],
                    'to_satuan_id' => $conversion['to_unit'],
                    'conversion_factor' => $conversion['factor'],
                ]);

                // Create reverse conversion
                $barang->toConversions()->create([
                    'from_satuan_id' => $conversion['to_unit'],
                    'to_satuan_id' => $conversion['from_unit'],
                    'conversion_factor' => 1 / $conversion['factor'],
                ]);
            }
            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $path = $request->file('main_image')->store('barang_images', 'public');
                $barang->images()->create([
                    'url' => $path,
                    'barang_id' => $barang->id,
                    'is_main' => true, // Add this if you have is_main column
                ]);
            }
            // Handle additional images upload
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $imageFile) {
                    $path = $imageFile->store('barang_images', 'public');
                    $barang->images()->create([
                        'url' => $path,
                        'barang_id' => $barang->id,
                        'is_main' => false, // Add this if you have is_main column
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan.',
                'data' => $barang->load(['brand', 'type', 'subcategory', 'images', 'fromConversions', 'toConversions'])
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */


    public function show($sku)
    {
        $barang = BarangModel::where('sku', $sku)->first();

        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }

        $allbarangki = $barang->barangki()->get();
        $totalSold = 0;
        foreach ($allbarangki as $item) {
            $convert = $this->convertSatuanService->convertToSmallestUnit($item, $item->sold_quantity);
            $totalSold += $convert['converted_amount'];
        }

        $totalAvailable = $this->convertSatuanService->convertBarangKeTerkecilDatatables($allbarangki)['total'];

        $totalStock = $totalSold + $totalAvailable;
        // Get barang KI with pagination
        $barangki = $barang->barangki()
            ->with(['satuan']) // assuming you have satuan relationship
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Transform data for DataTable
        $data = $barangki->map(function ($item) {
            return [
                'id' => $item->id,
                'barcode' => $item->barcode,
                'satuan' => $item->satuan->name ?? 'N/A',
                'quantity' => CurrencyHelper::formatStock($item->quantity) . ' ' . $item->satuan->name,
                'sold_quantity' => CurrencyHelper::formatStock($item->sold_quantity) . ' ' . $item->satuan->name,
                'available_quantity' => CurrencyHelper::formatStock($item->quantity - $item->sold_quantity) . ' ' . $item->satuan->name,
                'price_buy' => 'Rp ' . number_format($item->price_buy, 0, ',', '.'),
                'price_sell' => 'Rp ' . number_format($item->price_sell + ($item->barang->subcategory->margin / 100), 0, ',', '.'),
                'expired_time' => $item->expired_time ? Carbon::parse($item->expired_time)->format('d/m/Y') : 'N/A',
                'status' => $item->status,
                'status_badge' => $this->getStatusBadge($item->status),
                'expiry_status' => $this->getExpiryStatus($item->expired_time, $item->barang->early_expiry_days, $item->barang->mid_expiry_days, $item->barang->late_expiry_days),
                'discount' => $this->getDiscountInfo($item)
            ];
        });

        // Calculate total stock

        return response()->json([
            'barangki' => [
                'data' => $data,
                'current_page' => $barangki->currentPage(),
                'last_page' => $barangki->lastPage(),
                'per_page' => $barangki->perPage(),
                'total' => $barangki->total(),
            ],
            'summary' => [
                'total_stock' => CurrencyHelper::formatStock($totalStock),
                'total_sold' => CurrencyHelper::formatStock($totalSold),
                'total_available' => CurrencyHelper::formatStock($totalAvailable)
            ]
        ]);
    }

    private function getStatusBadge($status)
    {
        $badges = [
            'active' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>',
            'inactive' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>',
            'sold_out' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Sold Out</span>',
        ];

        return $badges[$status] ?? $badges['inactive'];
    }


    private function getExpiryStatus($expiredTime, $early_expiry_days, $mid_expiry_days, $late_expiry_days)
    {
        if (!$expiredTime) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Expiry</span>';
        }
        $now = Carbon::now();
        $expiry = Carbon::parse($expiredTime);
        $diffInDays = $now->diffInDays($expiry, false);

        if ($diffInDays > $early_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-green-100 text-green-800">Fresh</span>';
        }

        // Early expiry: between 60 and 365 days
        if ($diffInDays > $mid_expiry_days && $diffInDays <= $early_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-green-100 text-green-800">Early Expiry</span>';
        }
        // Mid expiry: between 3 and 60 days
        elseif ($diffInDays > $late_expiry_days && $diffInDays <= $mid_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-blue-100 text-blue-800">Mid Expiry</span>';
        }
        // Late expiry: between 0 and 3 days
        elseif ($diffInDays > 0 && $diffInDays <= $late_expiry_days) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-yellow-100 text-yellow-800">Late Expiry</span>';
        }
        // Expired: less than 0 days
        else {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full justify-center text-center text-xs font-medium bg-red-100 text-red-800">Expired</span>';
        }
    }

    private function getDiscountInfo($item)
    {
        $discountText = 'No Discount';
        if ($item->discount_start && $item->discount_start > Carbon::now()) {
            $discountText = 'Discount starts at ' . Carbon::parse($item->discount_start)->format('d/m/Y');
        } else {
            $priceSell = $item->price_sell + ($item->barang->subcategory->margin / 100);
            if ($item->discount_amount > 0) {
                $discountAmount = $item->discount_amount;
                $discountPercentage = round(($discountAmount / $priceSell) * 100, 2);
                $discountText = 'Rp ' . number_format($discountAmount, 0, ',', '.') . ' (' . $discountPercentage . '%)';
            } elseif ($item->discount_percentage > 0) {
                $discountAmount = round(($priceSell / 100) * $item->discount_percentage, 0);
                $discountText = $item->discount_percentage . '% (Rp ' . number_format($discountAmount, 0, ',', '.') . ')';
            }
            if ($item->discount_end && $item->discount_end < Carbon::now()) {
                $discountText .= ' (Expired)';
            }
        }
        if ($discountText) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">' . $discountText . '</span>';
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // Find the barang
            $barang = BarangModel::findOrFail($id);
            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'subcategory' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'kebutuhan' => 'required|string|max:255',
                'select_tipe_unit' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'early_expiry_days' => 'required|numeric',
                'mid_expiry_days' => 'required|numeric',
                'late_expiry_days' => 'required|numeric',
                'description' => 'nullable|string',
                // Conversion validation
                'from_unit' => 'nullable|array',
                'from_unit.*' => 'nullable|exists:satuan_items,id',
                'to_unit' => 'nullable|array',
                'to_unit.*' => 'nullable|exists:satuan_items,id',
                'conversion_factor' => 'nullable|array',
                'conversion_factor.*' => 'nullable|numeric',

                // Image validation
                'new_main_image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
                'new_additional_images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',

                // Deletion arrays
                'deleted_conversions.*' => 'nullable|exists:satuan_conversions,id',
                'deleted_images.*' => 'nullable|exists:barang_images,id',
            ];
            $request->validate($rules);

            // Update basic barang information
            $barang->update([
                'name' => $request->name,
                'subcategory_id' => $request->subcategory,
                'brand_id' => $request->brand,
                'type_id' => $request->kebutuhan,
                'description' => $request->description,
                'status' => $request->status,
                'early_expiry_days' => $request->early_expiry_days,
                'mid_expiry_days' => $request->mid_expiry_days,
                'late_expiry_days' => $request->late_expiry_days,
            ]);
            // Handle conversion deletions
            if ($request->has('deleted_conversions')) {
                foreach ($request->deleted_conversions as $conversionId) {
                    $conversion = $barang->fromConversions()->find($conversionId);
                    if ($conversion) {
                        $conversion->delete();
                    }
                }
            }

            // Handle new/updated conversions
            if ($request->has('from_unit') && $request->has('to_unit') && $request->has('conversion_factor')) {
                $fromUnits = $request->from_unit;
                $toUnits = $request->to_unit;
                $conversionFactors = $request->conversion_factor;

                // Clear existing conversions that aren't deleted (to avoid duplicates)
                $existingConversions = $barang->fromConversions()
                    ->whereNotIn('id', $request->deleted_conversions ?? [])
                    ->get();

                foreach ($existingConversions as $conversion) {
                    $conversion->delete();
                }

                // Add all conversions from form
                foreach ($fromUnits as $index => $fromUnitId) {
                    if (isset($toUnits[$index]) && isset($conversionFactors[$index])) {
                        $barang->fromConversions()->create([
                            'from_satuan_id' => $fromUnitId,
                            'to_satuan_id' => $toUnits[$index],
                            'conversion_factor' => $conversionFactors[$index],
                        ]);
                    }
                }
            }

            // Handle image deletions
            if ($request->has('deleted_images')) {
                foreach ($request->deleted_images as $imageId) {
                    $image = $barang->images()->find($imageId);
                    if ($image) {
                        // Delete file from storage
                        if (Storage::disk('public')->exists($image->url)) {
                            Storage::disk('public')->delete($image->url);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle new main image
            if ($request->hasFile('new_main_image')) {
                // Delete old main image if exists
                $oldMainImage = $barang->images()->where('is_main', true)->first();
                if ($oldMainImage) {
                    if (Storage::disk('public')->exists($oldMainImage->url)) {
                        Storage::disk('public')->delete($oldMainImage->url);
                    }
                    $oldMainImage->delete();
                }

                // Store new main image
                $mainImageFile = $request->file('new_main_image');
                $mainImagePath = $mainImageFile->store('barang/images', 'public');

                Image::create([
                    'barang_id' => $barang->id,
                    'url' => $mainImagePath,
                    'is_main' => true,
                ]);
            }

            // Handle new additional images
            if ($request->hasFile('new_additional_images')) {
                foreach ($request->file('new_additional_images') as $additionalImageFile) {
                    // Check current additional images count
                    $currentAdditionalCount = $barang->images()->where('is_main', false)->count();

                    if ($currentAdditionalCount >= 4) {
                        break; // Maximum 4 additional images
                    }

                    $additionalImagePath = $additionalImageFile->store('barang/images', 'public');

                    Image::create([
                        'barang_id' => $barang->id,
                        'url' => $additionalImagePath,
                        'is_main' => false,
                    ]);
                }
            }

            DB::commit();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diperbarui.',
                'data' => $barang->fresh()->load(['brand', 'type', 'images', 'fromConversions.conversionFrom', 'fromConversions.conversionTo']),
                'redirectUrl' => route('barang.index')
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.'
            ], 404);
        } catch (Exception $e) {
            DB::rollback();

            // Log the error for debugging
            \Log::error('Barang update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'barang_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $barang = BarangModel::withTrashed()->findOrFail($id);

            if ($barang->trashed()) {
                $barang->restore();
                $message = 'Barang berhasil di restore.';
            } else {
                if ($barang->barangki->count() > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Barang tidak dapat dihapus karena ada data di BarangKI.'
                    ], 400);
                }
                $barang->delete();
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

    /**
     * Export barang to PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        try {
            $barangs = BarangModel::with(['brand', 'type'])->get();
            $pdf = PDF::loadView('barang.component.pdf', compact('barangs'));
            return $pdf->download('barang-' . date('Y-m-d-His') . '.pdf');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}