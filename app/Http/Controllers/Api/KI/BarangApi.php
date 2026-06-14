<?php

namespace App\Http\Controllers\API\KI;

use App\Http\Controllers\Controller;
use App\Models\Barang\BarangKI;
use App\Models\Barang\Category;
use App\Services\Barang\BarangKIService;
use App\Services\Barang\PriceFormatterService;

class BarangApi extends Controller
{
    protected $priceFormatter;
    protected $barangKIService;

    public function __construct(PriceFormatterService $priceFormatter, BarangKIService $barangKIService)
    {
        $this->priceFormatter = $priceFormatter;
        $this->barangKIService = $barangKIService;
    }

    public function viewBarang()
    {
        $search = urldecode(request()->query('search'));
        $category = urldecode(request()->query('category'));

        $barangKiList = $this->barangKIService->getBarangKIPagination($search, $category); // Kirim ke getBarangKIPagination()
        $result = [];
        $baseUrl = env('APP_URL') . '/storage/';
        $result = $barangKiList->map(function ($barangKi) use ($baseUrl) {
            return [
                'id' => $barangKi->id,
                'id_barcode' => $barangKi->id_barcode,
                'name' => $barangKi->barang->name,
                'stock' => $barangKi->quantity,
                'kategori' => [
                    'id' => $barangKi->barang->subcategory->category->id,
                    'name' => $barangKi->barang->subcategory->category->name,
                    'description' => $barangKi->barang->subcategory->category->description,
                    'photo' => $barangKi->barang->subcategory->category->photo ? $baseUrl . $barangKi->barang->subcategory->category->photo : null,
                ],
                'subkategori' => [
                    'id' => $barangKi->barang->subcategory->id,
                    'name' => $barangKi->barang->subcategory->name,
                    'description' => $barangKi->barang->subcategory->description,
                    'photo' => $barangKi->barang->subcategory->photo ? $baseUrl . $barangKi->barang->subcategory->photo : null,
                ],
                'brand' => $barangKi->barang->brand->name,
                'type' => $barangKi->barang->type->name,
                'satuan' => $barangKi->satuan->name,
                'final_price' => $barangKi->final_price,
                'is_discounted' => $barangKi->is_discounted,
                'discount_info' => $barangKi->discount_info,
                'image' => $barangKi->barang->images->where('is_main', true)->first() 
                    ? $baseUrl . $barangKi->barang->images->where('is_main', true)->first()->url 
                    : ($barangKi->barang->images->first() ? $baseUrl . $barangKi->barang->images->first()->url : null),
            ];
        })->values();

        return response()->json([
            'barang' => $result,
            'pagination' => [
                'current_page' => $barangKiList->currentPage(),
                'last_page' => $barangKiList->lastPage(),
                'per_page' => $barangKiList->perPage(),
                'total' => $barangKiList->total(),
                'next_page_url' => $barangKiList->nextPageUrl(),
                'prev_page_url' => $barangKiList->previousPageUrl(),
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function category()
    {
        $categories = Category::with('subcategories')->get();
        return response()->json($categories, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
