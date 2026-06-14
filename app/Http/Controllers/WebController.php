<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Faq;
use App\Models\Artikel;
class WebController extends Controller
{
    /**
     * Halaman Produk
     */
    public function index()
{
    $latestArticles = Artikel::whereNotNull('published_at')
        ->orderBy('published_at', 'desc')
        ->take(5)
        ->get();

    return view('welcome', compact('latestArticles'));
}
    public function produk()
    {
        $perlengkapan = Product::where('kategori', 'perlengkapan_fisik')
            ->latest()
            ->get();

        $plugins = Product::where('kategori', 'plugin')
            ->latest()
            ->get();

        $ebooks = Product::where('kategori', 'ebook')
            ->latest()
            ->get();

        return view('guest.produk', compact(
            'perlengkapan',
            'plugins',
            'ebooks'
        ));
    }

    /**
     * Detail Perlengkapan
     */
    public function detailPerlengkapan($id)
    {
        $item = Product::findOrFail($id);

        return view(
            'guest.detail-perlengkapan',
            compact('item')
        );
    }

    /**
     * Detail Plugin
     */
    public function detailPlugin($id)
    {
        $item = Product::findOrFail($id);

        return view(
            'guest.detail-plugin',
            compact('item')
        );
    }

    /**
     * Detail Ebook
     */
    public function detailEbook($id)
    {
        $item = Product::findOrFail($id);

        return view(
            'guest.detail-ebook',
            compact('item')
        );
    }

    /**
     * Halaman FAQ
     */
    public function faq()
    {
        // mengambil semua data FAQ dari database
        $faqs = Faq::all();

        return view(
            'faq',
            compact('faqs')
        );
    }
}