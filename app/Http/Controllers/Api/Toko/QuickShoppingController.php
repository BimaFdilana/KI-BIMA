<?php

namespace App\Http\Controllers\Api\Toko;

use App\Models\Toko\TokoKeranjang;
use App\Services\Barang\BarangKIService;
use App\Services\Toko\BelanjaCepatService;
use App\Services\Toko\KeranjangTokoService;
use App\Services\Toko\TokoService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class QuickShoppingController extends Controller
{
    protected $belanjaCepatService;
    protected $tokoService;
    protected $barangKIService;
    protected $keranjangTokoService;

    public function __construct(BelanjaCepatService $belanjaCepatService, TokoService $tokoService, BarangKIService $barangKIService, KeranjangTokoService $keranjangTokoService)
    {
        $this->belanjaCepatService = $belanjaCepatService;
        $this->tokoService = $tokoService;
        $this->barangKIService = $barangKIService;
        $this->keranjangTokoService = $keranjangTokoService;
    }

    /**
     * Mendapatkan daftar belanja cepat berdasarkan parameter yang diberikan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function validateRequest($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter input tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        return true;
    }

    private function validateStore($user)
    {
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        return $toko;
    }

    private function generateRequestId()
    {
        return uniqid('qs_');
    }

    private function logRequest($requestId, $message, $context = [])
    {
        Log::info("[$requestId] " . $message, $context);
    }

    private function logWarning($requestId, $message)
    {
        Log::warning("[$requestId] " . $message);
    }

    public function getQuickShoppingList(Request $request)
    {
        $validationRules = [
            'budget' => 'nullable|numeric|min:1',
            'max_items' => 'nullable|integer|min:1|max:100',
            'include_expiring' => 'nullable|boolean',
            'include_popular_items' => 'nullable|boolean',
        ];

        $valid = $this->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        $requestId = $this->generateRequestId();
        $user = Auth::user();
        $toko = $this->validateStore($user);
        if ($toko instanceof \Illuminate\Http\JsonResponse) {
            return $toko;
        }

        $tokoId = $toko->id;
        $budget = $request->budget;
        $maxItems = $request->max_items;
        $includeExpiring = $request->include_expiring ?? true;
        $includePopularItems = $request->include_popular_items ?? true;

        $eligibility = $this->belanjaCepatService->checkStoreEligibility($tokoId, $requestId);
        if (!$eligibility['eligible']) {
            $this->logWarning($requestId, "Store doesn't meet requirements: " . $eligibility['reason']);
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak memenuhi syarat untuk belanja cepat',
                'reason' => $eligibility['reason'],
            ], 403);
        }

        if (!$budget) {
            $weeklySales = $eligibility['weekly_sales'];
            $budget = $weeklySales * 0.75;
            $this->logRequest($requestId, 'No budget provided, calculated budget', ['budget' => $budget, 'weekly_sales' => $weeklySales]);
        }

        if (!$maxItems) {
            $maxItems = 10;
            $this->logRequest($requestId, 'No max_items provided, using default', ['max_items' => $maxItems]);
        }

        $recommendations = $this->belanjaCepatService->generateComprehensiveShoppingList(
            $tokoId,
            $budget,
            $maxItems,
            $includeExpiring,
            $includePopularItems,
            $requestId
        );

        if (empty($recommendations['items'])) {
            $this->logWarning($requestId, 'No recommendations generated');
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada rekomendasi untuk digunakan',
            ], 400);
        }

        $this->logRequest($requestId, 'Successfully generated shopping list', ['item_count' => count($recommendations['items'])]);

        $response = [
            'success' => true,
            'message' => 'Berhasil Generate List Belanja Cepat',
            'toko' => $toko->name,
            'budget' => $budget,
            'total_cost' => round($recommendations['total_cost']),
            'remaining_budget' => round($budget - $recommendations['total_cost']),
            'items_count' => count($recommendations['items']),
            'items' => collect($recommendations['items'])->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'current_stock' => $item['current_stock'],
                    'weekly_sales' => $item['weekly_sales'],
                    'quantity' => $item['recommended_quantity'] . ' ' . $item['unit_name'],
                    'price' => $item['price_per_unit'],
                    'total_price' => $item['total_price'],
                    'discount_info' => $item['discount_info'],
                    'type' => $item['type'],
                    'reason' => $item['purchase_reason'],
                    'is_depleted' => $item['is_depleted'],
                ];
            }),
        ];

        if (!empty($recommendations['grouped_items'])) {
            $response['grouped_items'] = collect($recommendations['grouped_items'])->map(function ($group) {
                return [
                    'type_name' => $group['type_name'],
                    'items' => collect($group['items'])->map(function ($item) {
                        return [
                            'name' => $item['name'],
                            'current_stock' => $item['current_stock'],
                            'weekly_sales' => $item['weekly_sales'],
                            'quantity' => $item['recommended_quantity'] . ' ' . $item['unit_name'],
                            'price' => $item['price_per_unit'],
                            'total_price' => $item['total_price'],
                            'discount_info' => $item['discount_info'],
                            'type' => $item['type'],
                            'reason' => $item['purchase_reason'],
                            'is_depleted' => $item['is_depleted'],
                        ];
                    }),
                ];
            });
        }

        return response()->json($response, 200);
    }

    public function addQuickShoppingItem(Request $request)
    {
        $validationRules = [
            'budget' => 'nullable|numeric|min:1',
            'max_items' => 'nullable|integer|min:1|max:100',
            'include_expiring' => 'nullable|boolean',
            'include_popular_items' => 'nullable|boolean',
        ];

        $valid = $this->validateRequest($request, $validationRules);
        if ($valid !== true) {
            return $valid;
        }

        $requestId = $this->generateRequestId();
        $this->logRequest($requestId, 'Quick shopping request started', ['params' => $request->all()]);

        $user = Auth::user();
        $toko = $this->validateStore($user);
        if ($toko instanceof \Illuminate\Http\JsonResponse) {
            return $toko;
        }

        $tokoId = $toko->id;
        $budget = $request->budget;
        $maxItems = $request->max_items;
        $includeExpiring = $request->include_expiring ?? true;
        $includePopularItems = $request->include_popular_items ?? true;

        $eligibility = $this->belanjaCepatService->checkStoreEligibility($tokoId, $requestId);
        if (!$eligibility['eligible']) {
            $this->logWarning($requestId, "Store doesn't meet requirements: " . $eligibility['reason']);
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak memenuhi syarat untuk belanja cepat',
                'reason' => $eligibility['reason'],
            ], 403);
        }

        if (!$budget) {
            $weeklySales = $eligibility['weekly_sales'];
            $budget = $weeklySales * 0.75;
            $this->logRequest($requestId, 'No budget provided, calculated budget', ['budget' => $budget, 'weekly_sales' => $weeklySales]);
        }

        if (!$maxItems) {
            $maxItems = 10;
            $this->logRequest($requestId, 'No max_items provided, using default', ['max_items' => $maxItems]);
        }

        $recommendations = $this->belanjaCepatService->generateComprehensiveShoppingList(
            $tokoId,
            $budget,
            $maxItems,
            $includeExpiring,
            $includePopularItems,
            $requestId
        );
        if (empty($recommendations['items'])) {
            $this->logWarning($requestId, 'No recommendations generated');
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada rekomendasi untuk digunakan',
            ], 400);
        }

        $successItems = [];
        $failedItems = [];

        $toko = $this->tokoService->getTokoByUser($request->user());

        if (!$toko) {
            $this->logWarning($requestId, 'Toko not found for user');
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan',
            ], 404);
        }

        TokoKeranjang::where('toko_id', $tokoId)->delete();

        foreach ($recommendations['items'] as $item) {
            try {
                $response = $this->keranjangTokoService->addToKeranjangBarangID(
                    $toko,
                    $item['barang_id'],
                    $item['unit_id'],
                    $item['recommended_quantity']
                );

                $responseData = json_decode($response->getContent(), true);

                if ($response->status() != 200) {
                    $failedItems[] = [
                        'name' => $item['name'],
                        'unit_name' => $item['unit_name'],
                        'quantity' => $item['recommended_quantity'],
                        'error' => $responseData['error'] ?? 'Failed to add item to cart'
                    ];
                    continue;
                }

                $successItems[] = [
                    'name' => $item['name'],
                    'unit_name' => $item['unit_name'],
                    'quantity' => $item['recommended_quantity']
                ];
            } catch (\Exception $e) {
                $failedItems[] = [
                    'name' => $item['name'],
                    'unit_name' => $item['unit_name'],
                    'quantity' => $item['recommended_quantity'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambahkan item ke keranjang',
            'data' => [
                'success_count' => count($successItems),
                'failed_count' => count($failedItems),
                'success_items' => $successItems,
                'failed_items' => $failedItems
            ]
        ], 200);
    }
}
