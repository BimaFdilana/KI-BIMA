<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::latest()->get();

        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer'   => 'required',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        return redirect()
            ->route('faq.index')
            ->with('success', 'FAQ berhasil ditambahkan');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);

        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required',
            'answer'   => 'required',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer'   => $request->answer,
        ]);

        return redirect()
            ->route('faq.index')
            ->with('success', 'FAQ berhasil diupdate');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return redirect()
            ->route('faq.index')
            ->with('success', 'FAQ berhasil dihapus');
    }
}