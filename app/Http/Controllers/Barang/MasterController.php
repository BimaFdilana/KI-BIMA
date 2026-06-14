<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use App\DataTables\Master\CategoryDataTable;
use App\DataTables\Master\SatuanItemDataTable;
use App\DataTables\Master\BrandDataTable;
use App\DataTables\Master\SubCategoryDataTable;
use App\DataTables\Master\TypeItemDataTable;
use App\Models\Barang\Category;
use App\Models\Barang\SatuanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterController extends Controller
{
    // Fungsi untuk menampilkan halaman master barang dengan DataTables
    public function index(
        CategoryDataTable $categoryDataTable, 
        SatuanItemDataTable $satuanItemDataTable, 
        BrandDataTable $brandDataTable, 
        SubCategoryDataTable $subCategoryDataTable,
        TypeItemDataTable $typeItemDataTable
        )
    {
        $satuanItemTypes = SatuanItem::distinct()->pluck('type')
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => ucfirst($type)
                ];
            })
            ->values()
            ->toArray();
        return view('barang.master', [
            'categoryDataTable' => $categoryDataTable->html()->ajax([
                'url' => route('barang.master.categories'), // URL untuk mendapatkan data kategori
                'type' => 'GET',
            ]),
            'satuanItemTypes' => $satuanItemTypes,
            'satuanItemDataTable' => $satuanItemDataTable->html()->ajax([
                'url' => route('barang.master.satuanItems'), // URL untuk mendapatkan data satuan item
                'type' => 'GET',
            ]),
            'brandDataTable' => $brandDataTable->html()->ajax([
                'url' => route('barang.master.brands'), // URL untuk mendapatkan data satuan item
                'type' => 'GET',
            ]),
            'subcategoryDataTable' => $subCategoryDataTable->html()->ajax([
                'url' => route('barang.master.subCategories'), // URL untuk mendapatkan data satuan item
                'type' => 'GET',
            ]),
            'typeItemDataTable' => $typeItemDataTable->html()->ajax([
                'url' => route('barang.master.typeItems'), // URL untuk mendapatkan data satuan item
                'type' => 'GET',
            ]),
        ]);
    }

    public function getCategories(CategoryDataTable $categoryDataTable)
    {
        return $categoryDataTable->ajax();
    }

    // Fungsi untuk mengambil data satuan item
    public function getSatuanItems(SatuanItemDataTable $satuanItemDataTable)
    {
        return $satuanItemDataTable->ajax();
    }

    public function getBrands(BrandDataTable $brandDataTable)
    {
        return $brandDataTable->ajax();
    }
    
    public function getSubCategories(SubCategoryDataTable $subCategoryDataTable)
    {
        return $subCategoryDataTable->ajax();
    }
    
    public function getTypeItems(TypeItemDataTable $typeItemDataTable)
    {
        return $typeItemDataTable->ajax();
    }    
    
    public function updateSatuanItem(Request $request)
    {
        $satuanItem = SatuanItem::findOrFail($request->id);
        $satuanItem->selling = $request->selling;
        
        $satuanItem->save();
        return response()->json(['success' => true]);
    }
}
