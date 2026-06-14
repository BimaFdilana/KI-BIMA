<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::latest()->get();

        return view('admin.laporan.index', compact('laporans'));
    }

    public function create()
    {
        return view('admin.laporan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'nullable|image'
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')
                ->store('laporan', 'public');
        }

        Laporan::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar
        ]);

        return redirect()->route('laporan.index');
    }

    public function edit(Laporan $laporan)
    {
        return view('admin.laporan.edit', compact('laporan'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $gambar = $laporan->gambar;

        if ($request->hasFile('gambar')) {

            if ($laporan->gambar) {
                Storage::disk('public')->delete($laporan->gambar);
            }

            $gambar = $request->file('gambar')
                ->store('laporan', 'public');
        }

        $laporan->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambar
        ]);

        return redirect()->route('laporan.index');
    }

    public function destroy(Laporan $laporan)
    {
        if ($laporan->gambar) {
            Storage::disk('public')->delete($laporan->gambar);
        }

        $laporan->delete();

        return back();
    }
}