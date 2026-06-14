<?php

namespace App\Http\Controllers\Barang;

use App\Http\Controllers\Controller;
use App\Models\Barang\SatuanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SatuanItemController extends Controller
{
    public function index()
    {
        $satuanItemTypes = SatuanItem::distinct()->pluck('type')->toArray();
        return view('barang.master', compact('satuanItemTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cut_name' => 'required|string|max:20',
            'type' => 'required|string|in:berat,volume,panjang,luas,waktu,suhu,unit',
            'selling' => 'required|string|in:true,false',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all(),
            ]);
        }

        $satuanItem = new SatuanItem();
        $satuanItem->name = $request->name;
        $satuanItem->cut_name = $request->cut_name;
        $satuanItem->type = $request->type;
        $satuanItem->selling = $request->selling;
        $satuanItem->description = $request->description;
        $satuanItem->save();
        
        // Set the tab in session
        session(['tab' => 'satuanItemDataTable']);

        return redirect()->back()->with('toast', [
            'message' => 'Satuan item berhasil ditambahkan!',
            'type' => 'success'
        ]);
    }

    public function edit($id)
    {
        $satuanItem = SatuanItem::findOrFail($id);
        return view('barang.master', compact('satuanItem'));
    }

    public function update(Request $request, $id)
    {
        // Update logic
    }

    public function destroy($id)
    {
        try {
            $satuanItem = SatuanItem::findOrFail($id);
            if ($satuanItem->barangki()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Satuan item tidak dapat dihapus karena ada barang yang menggunakan satuan ini!'
                ], 400);
            }
            $satuanItem->delete();
            
            // Set the tab in session
            session(['tab' => 'satuanItemDataTable']);

            return response()->json([
                'success' => true,
                'message' => 'Satuan item berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus satuan item: ' . $e->getMessage()
            ], 500);
        }
    }
}
