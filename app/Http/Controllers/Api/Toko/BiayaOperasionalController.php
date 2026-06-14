<?php

namespace App\Http\Controllers\Api\Toko;

use App\Http\Controllers\Controller;
use App\Models\Toko\BiayaOperasional;
use App\Services\Toko\TokoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BiayaOperasionalController extends Controller
{
    protected $tokoService;

    public function __construct(TokoService $tokoService)
    {
        $this->tokoService = $tokoService;
    }

    /**
     * Display a listing of operational costs
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Get filter parameters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategori = $request->query('kategori');

        // Build query
        $query = BiayaOperasional::where('toko_id', $toko->id);

        // Apply filters
        if ($startDate) {
            $query->where('tanggal', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('tanggal', '<=', Carbon::parse($endDate)->endOfDay());
        }
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $biayaOperasional = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $biayaOperasional,
            'total' => $biayaOperasional->sum('jumlah')
        ]);
    }

    /**
     * Store a newly created operational cost
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|integer|min:0',
            'tanggal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create operational cost
        $biayaOperasional = BiayaOperasional::create([
            'toko_id' => $toko->id,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biaya operasional berhasil ditambahkan',
            'data' => $biayaOperasional
        ], 201);
    }

    /**
     * Display the specified operational cost
     */
    public function show($id)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $biayaOperasional = BiayaOperasional::where('toko_id', $toko->id)
            ->where('id', $id)
            ->first();

        if (!$biayaOperasional) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya operasional tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $biayaOperasional
        ]);
    }

    /**
     * Update the specified operational cost
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $biayaOperasional = BiayaOperasional::where('toko_id', $toko->id)
            ->where('id', $id)
            ->first();

        if (!$biayaOperasional) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya operasional tidak ditemukan',
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'kategori' => 'sometimes|required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'sometimes|required|integer|min:0',
            'tanggal' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update operational cost
        $biayaOperasional->update($request->only(['kategori', 'deskripsi', 'jumlah', 'tanggal']));

        return response()->json([
            'success' => true,
            'message' => 'Biaya operasional berhasil diupdate',
            'data' => $biayaOperasional
        ]);
    }

    /**
     * Remove the specified operational cost
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $toko = $this->tokoService->getTokoByUser($user);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terdaftar di toko manapun.',
            ], 403);
        }

        $biayaOperasional = BiayaOperasional::where('toko_id', $toko->id)
            ->where('id', $id)
            ->first();

        if (!$biayaOperasional) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya operasional tidak ditemukan',
            ], 404);
        }

        $biayaOperasional->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biaya operasional berhasil dihapus'
        ]);
    }
}
