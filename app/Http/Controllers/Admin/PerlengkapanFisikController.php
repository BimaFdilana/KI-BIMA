<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerlengkapanFisik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerlengkapanFisikController extends Controller
{
    public function index()
    {
        $data = PerlengkapanFisik::latest()->get();

        return view(
            'admin.perlengkapan.index',
            compact('data')
        );
    }

    public function create()
    {
        return view('admin.perlengkapan.create');
    }

    public function store(Request $request)
    {
        $gambar = $request->file('gambar')
                          ->store('perlengkapan', 'public');

        PerlengkapanFisik::create([
            'nama'       => $request->nama,
            'deskripsi'  => $request->deskripsi,
            'badge'      => $request->badge,
            'fitur'      => array_filter($request->fitur),
            'gambar'     => $gambar
        ]);

        return redirect()
            ->route('perlengkapan-fisik.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = PerlengkapanFisik::findOrFail($id);

        return view('admin.perlengkapan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PerlengkapanFisik::findOrFail($id);

        $request->validate([
            'nama'       => 'required',
            'deskripsi'  => 'required',
            'fitur'      => 'required',
        ]);

        $data = [
            'nama'       => $request->nama,
            'deskripsi'  => $request->deskripsi,
            'badge'      => $request->badge,
            'fitur'      => array_filter($request->fitur),
        ];

        if ($request->hasFile('gambar')) {

            if ($item->gambar && Storage::exists('public/' . $item->gambar)) {

                Storage::delete('public/' . $item->gambar);

            }

            $gambar = $request->file('gambar')
                              ->store('perlengkapan', 'public');

            $data['gambar'] = $gambar;
        }

        $item->update($data);

        return redirect()
            ->route('perlengkapan-fisik.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = PerlengkapanFisik::findOrFail($id);

        if ($item->gambar && Storage::exists('public/' . $item->gambar)) {

            Storage::delete('public/' . $item->gambar);

        }

        $item->delete();

        return redirect()
            ->route('perlengkapan-fisik.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}