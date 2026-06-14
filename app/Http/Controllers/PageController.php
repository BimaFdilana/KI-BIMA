<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;
use App\Models\KontakPesan;
use App\Models\Laporan;

class PageController extends Controller
{
    public function home()
    {
        $latestArticles = Artikel::latest()->take(4)->get();

        $laporans = Laporan::latest()->get();

        return view('guest.home', compact(
            'latestArticles',
            'laporans'
        ));
    }

    public function hubungiKami()
    {
        return view('pages.hubungi-kami');
    }

    // Halaman list artikel
    public function artikel()
    {
        $artikels = Artikel::latest()->get();

        return view('pages.artikel', compact('artikels'));
    }

    // Halaman detail artikel
    public function show($slug)
    {
        $artikel = Artikel::where('slug', $slug)->firstOrFail();

        return view('pages.detail-artikel', compact('artikel'));
    }

    public function kirimPesan(Request $request)
    {
        $request->validate([
            'nama'  => 'required',
            'email' => 'required|email',
            'pesan' => 'required',
        ]);

        KontakPesan::create([
            'nama'  => $request->nama,
            'email' => $request->email,
            'pesan' => $request->pesan,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim!');
    }
}