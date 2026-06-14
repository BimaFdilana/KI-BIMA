<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();

        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        return view('admin.product.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'nullable|image',
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {

            $gambar = $request->file('gambar')
                ->store('products', 'public');
        }

        Product::create([

            'nama' => $request->nama,

            'slug' => Str::slug($request->nama),

            'kategori' => $request->kategori,

            'subtitle' => $request->subtitle,

            'deskripsi' => $request->deskripsi,

            'badge' => $request->badge,

            'gambar' => $gambar,

            'fitur' => $request->fitur,
        ]);

        return redirect()
            ->route('product.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    //edit
     public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.product.edit', compact('product'));
    }

      public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
        ]);

        $gambar = $product->gambar;
        $fileEbook = $product->file_ebook;

        // update gambar
        if ($request->hasFile('gambar')) {

            if ($product->gambar) {

                Storage::disk('public')
                    ->delete($product->gambar);
            }

            $gambar = $request->file('gambar')
                ->store('products', 'public');
        }

        // update ebook
        if ($request->hasFile('file_ebook')) {

            if ($product->file_ebook) {

                Storage::disk('public')
                    ->delete($product->file_ebook);
            }

            $fileEbook = $request->file('file_ebook')
                ->store('ebooks', 'public');
        }

        $product->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'subtitle' => $request->subtitle,
            'deskripsi' => $request->deskripsi,
            'badge' => $request->badge,
            'gambar' => $gambar,
            'fitur' => $request->fitur,
            'file_ebook' => $fileEbook,
            'link_download' => $request->link_download,
        ]);

        return redirect()
            ->route('product.index')
            ->with('success', 'Produk berhasil diupdate');
    }
    public function destroy($id)
{
    $product = Product::findOrFail($id);

    // hapus gambar jika ada
    if ($product->gambar) {
        Storage::disk('public')->delete($product->gambar);
    }

    // hapus file ebook jika ada
    if ($product->file_ebook) {
        Storage::disk('public')->delete($product->file_ebook);
    }

    // hapus data dari database
    $product->delete();

    return redirect()
        ->route('product.index')
        ->with('success', 'Produk berhasil dihapus');
}
}