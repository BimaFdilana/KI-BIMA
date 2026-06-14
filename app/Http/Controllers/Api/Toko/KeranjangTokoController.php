<?php

namespace App\Http\Controllers\Api\Toko;

use App\Models\Barang\BarangKI;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Toko\TokoKeranjang;
use App\Services\Toko\KeranjangTokoService;
use App\Services\Toko\TokoService;
use App\Services\Barang\BarangKIService;

class KeranjangTokoController extends BaseController
{
    protected $tokoService;
    protected $barangKIService;
    protected $keranjangTokoService;

    public function __construct(TokoService $tokoService, BarangKIService $barangKIService, KeranjangTokoService  $keranjangTokoService)
    {
        $this->tokoService = $tokoService;
        $this->barangKIService = $barangKIService;
        $this->keranjangTokoService = $keranjangTokoService;
    }

    public function viewKeranjang()
    {
        $user = auth()->user();
        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);
        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $result = $this->keranjangTokoService->updateKeranjangToko($toko);
        $keranjang = $this->keranjangTokoService->getKeranjangBarangAktif($toko);


        $totalAvailableItems = 0; // Jumlah barang Available
        $totalAvailablePrice = 0;
        $totalOutOfStockItems = 0; // Jumlah barang Out of Stock
        $totalOutOfStockPrice = 0;
        $baseUrl = env('APP_URL') . '/storage/';
        $barangData = [];

        foreach ($keranjang as $item) {
            $data = $item->barangki;
            $cekDiscount = $this->barangKIService->applyDiscountsToBarang($data);
            $status = $data->quantity > $item->quantity ? "Available" : "Out of Stock";

            if ($status === "Available") {
                $totalAvailableItems++; // Hitung berdasarkan jumlah barang
                $totalAvailablePrice += $cekDiscount->final_price * $item->quantity;
            } else {
                $totalOutOfStockItems++; // Hitung berdasarkan jumlah barang
                $totalOutOfStockPrice += $cekDiscount->final_price * $item->quantity;
            }

            $barangData[] = [
                'id' => $item->id,
                'id_barcode' => $data->id_barcode,
                'name' => $data->barang->name,
                'kategori' => $data->barang->subcategory->category->name,
                'subkategori' => $data->barang->subcategory->name,
                'brand' => $data->barang->brand->name,
                'type' => $data->barang->type->name,
                'satuan' => $data->satuan->name,
                'amount' => $item->quantity,
                'final_price' => $cekDiscount->final_price,
                'is_discounted' => $cekDiscount->is_discounted,
                'discount_info' => $cekDiscount->discount_info,
                'image' => $data->barang->image ? $baseUrl . $data->barang->image : null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dimuat',
            'itemAvaible' => $totalAvailableItems,
            'hargaAvaible' => number_format($totalAvailablePrice, 0, ',', '.'),
            'itemOos' => $totalOutOfStockItems,
            'hargaOos' => number_format($totalOutOfStockPrice, 0, ',', '.'),
            'barangData' => $barangData,
            'result' =>   $result,
        ]);
    }


    public function storeKeranjang(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'id_barcode' => 'required|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Cari barang berdasarkan barcode
        $barangki = BarangKI::where('id_barcode', $validatedData['id_barcode'])->first();

        if (!$barangki) {
            return response()->json(['error' => 'Barang tidak ditemukan.'], 404);
        }

        // Cari barang valid berdasarkan barang_id dan satuan_id
        $barangValid =  $this->barangKIService->getBarangValid($barangki->barang_id, $barangki->satuan_id);

        if (!$barangValid) {
            return response()->json(['error' => 'Tidak ada barang yang valid untuk ditambahkan.'], 400);
        }
        // Periksa apakah barang sudah ada di keranjang toko
        $existingItem = TokoKeranjang::where('toko_id', $toko->id)
            ->where('barangki_id', $barangValid->id)
            ->first();

        $newQuantity = $validatedData['quantity'];

        if ($existingItem) {
            $newQuantity += $existingItem->quantity;
        }

        // Periksa stok barang valid
        if ($newQuantity > $barangValid->quantity) {
            return response()->json(['error' => 'Jumlah barang di keranjang melebihi quantity yang tersedia.'], 400);
        }

        // Tambahkan atau perbarui item di keranjang
        if ($existingItem) {
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            TokoKeranjang::create([
                'toko_id'     => $toko->id,
                'barangki_id' => $barangValid->id,
                'quantity'    => $validatedData['quantity'],
            ]);
        }

        return response()->json([
            'success' => 'Barang berhasil ditambahkan ke keranjang.',
            'data' => [
                'barang_name' => $barangValid->barang->name,
                'toko'        => $toko->name,
                'jumlah'      => $validatedData['quantity'] . ' ' . $barangValid->satuan->name,
                'total'       => $newQuantity . ' ' . $barangValid->satuan->name,
            ],
        ], 200);
    }

    public function updateKeranjang(Request $request)
    {
        $validatedData = $request->validate([
            'id_barcode' => [
                'required_if:action,add,remove,change',
                'string',
            ],
            'quantity' => [
                'required_if:action,add,remove,change',
                'integer',
                'min:1',
            ],
            'action' => 'required|string|in:add,remove,change,clear',
        ]);

        $user = auth()->user();

        // Cek apakah user ada di toko tertentu
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        switch ($validatedData['action']) {
            case 'add':
            case 'remove':
            case 'change':
                // Cari barang berdasarkan barcode
                $barangki = BarangKI::where('id_barcode', $validatedData['id_barcode'])->first();

                if (!$barangki) {
                    return response()->json(['error' => 'Barang tidak ditemukan.'], 404);
                }

                // Cari barang valid berdasarkan barang_id dan satuan_id
                $barangValid = $this->barangKIService->getBarangValid($barangki->barang_id, $barangki->satuan_id);

                if (!$barangValid) {
                    return response()->json(['error' => 'Tidak ada barang yang valid untuk diperbarui.'], 400);
                }

                // Proses berdasarkan jenis aksi
                if ($validatedData['action'] === 'add') {
                    return $this->keranjangTokoService->addToKeranjang($toko, $barangValid, $validatedData['quantity']);
                } elseif ($validatedData['action'] === 'remove') {
                    return $this->keranjangTokoService->removeFromKeranjang($toko, $barangValid, $validatedData['quantity']);
                } elseif ($validatedData['action'] === 'change') {
                    return $this->keranjangTokoService->changeKeranjang($toko, $barangValid, $validatedData['quantity']);
                }
                break;

            case 'clear':
                // Hapus semua barang pada tabel TokoKeranjang berdasarkan tokoid
                $deletedCount = TokoKeranjang::where('toko_id', $toko->id)->delete();

                return response()->json([
                    'message' => 'Keranjang berhasil dikosongkan.',
                    'deleted_items' => $deletedCount,
                ]);

            default:
                return response()->json(['error' => 'Aksi tidak valid.'], 400);
        }
    }
}
