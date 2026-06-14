<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlugIn;
use Illuminate\Http\Request;

class PlugInController extends Controller
{
    public function index()
    {
        $data = PlugIn::latest()->get();

        return view(
            'admin.plugin.index',
            compact('data')
        );
    }

    public function create()
    {
        return view('admin.plugin.create');
    }

    public function store(Request $request)
    {
        $gambar = $request->file('gambar')
                          ->store('plugin', 'public');

        PlugIn::create([
            'nama' => $request->nama,
            'subtitle' => $request->subtitle,
            'deskripsi' => $request->deskripsi,
            'fitur' => $request->fitur,
            'gambar' => $gambar
        ]);

        return redirect()
            ->route('plug-in.index');
    }
}