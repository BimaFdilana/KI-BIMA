<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KontakPesan;

class PesanController extends Controller
{
    public function index(Request $request)
    {
        $query = KontakPesan::query();

        // Filter status
        if ($request->status == 'unread') {
            $query->where('is_read', false);
        }

        if ($request->status == 'read') {
            $query->where('is_read', true);
        }

        $pesans = $query->latest()->get();

        return view('admin.pesan.index', compact('pesans'));
    }

    // Tandai sudah dibaca
    public function markAsRead($id)
    {
        $pesan = KontakPesan::findOrFail($id);

        $pesan->is_read = true;
$pesan->save();
        return back()->with('success', 'Pesan ditandai sudah dibaca');
    }
}