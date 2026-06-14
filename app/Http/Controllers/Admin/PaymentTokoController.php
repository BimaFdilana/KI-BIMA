<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Toko\TokoPayment;
use App\Models\Toko\TokoPesanan;
use Illuminate\Http\Request;

class PaymentTokoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');
        
        $query = TokoPayment::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        }
        
        $pesanan = $query->paginate(10);
        
        return view('login.pesanan.index', compact('pesanan', 'status'));
    }

    public function loadMore(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');
        $page = $request->query('page', 1);
        
        $query = TokoPayment::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pesanan', 'like', "%{$search}%")
                    ->orWhere('nama_pemesan', 'like', "%{$search}%");
            });
        }
        
        $pesanan = $query->paginate(10, ['*'], 'page', $page);
        
        return response()->json([
            'data' => $pesanan->items(),
            'hasMore' => $pesanan->hasMorePages(),
            'nextPage' => $pesanan->nextPageUrl()
        ]);
    }

    public function show($id)
    {
        $pesanan = TokoPayment::findOrFail($id);
        return view('login.pesanan.show', compact('pesanan'));
    }
}
