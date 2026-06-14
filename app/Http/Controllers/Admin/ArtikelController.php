<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::latest()->get();

        return view('admin.artikel.index', compact('artikels'));
    }

    public function create()
    {
        return view('admin.artikel.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'judul' => 'required',
        'isi' => 'required',
        'gambar' => 'required|image',
    ]);

    $gambar = $request->file('gambar')->store('artikel', 'public');

    Artikel::create([
        'judul' => $request->judul,
        'slug' => Str::slug($request->judul),
        'deskripsi_singkat' => $request->deskripsi_singkat,
        'isi' => $request->isi,
        'gambar' => $gambar,
        'published_at' => $request->published_at,
    ]);

    return redirect()->route('artikel.index')
        ->with('success', 'Artikel berhasil ditambahkan');
}

    public function edit($id)
{
    $artikel = Artikel::findOrFail($id);
    return view('admin.artikel.edit', compact('artikel'));
}

    public function update(Request $request, $id)
    {
        $artikel = Artikel::findOrFail($id);

        $gambar = $artikel->gambar;

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('artikel', 'public');
        }

        $artikel->update([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul),
            'gambar' => $gambar,
            'deskripsi_singkat' => $request->deskripsi_singkat,
            'isi' => $request->isi,
        ]);

        return redirect()->route('artikel.index')
            ->with('success', 'Artikel berhasil diupdate');
    }

    public function destroy($id)
    {
        Artikel::findOrFail($id)->delete();

        return back()->with('success', 'Artikel berhasil dihapus');
    }

 public function show($slug)
{
    $artikel = Artikel::where('slug', $slug)->firstOrFail();

    $artikel->increment('views');

    return view('pages.detail-artikel', compact('artikel'));
}
}