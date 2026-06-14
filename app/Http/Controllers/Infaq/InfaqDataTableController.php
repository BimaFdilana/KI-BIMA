<?php

namespace App\Http\Controllers\Infaq;

use App\DataTables\InfaqListDataTable;
use App\DataTables\InfaqHistoryDataTable;
use App\DataTables\InfaqImageDataTable;
use App\Http\Controllers\Controller;
use App\Models\Infaq\InfaqList;
use App\Models\Infaq\InfaqHistory;
use App\Models\Infaq\InfaqImage;
use Illuminate\Http\Request;

class InfaqDataTableController extends Controller
{
    /**
     * Display infaq list datatable
     */
    public function infaqListIndex(InfaqListDataTable $dataTable)
    {
        return $dataTable->render('infaq.list.index');
    }

    /**
     * Display infaq history datatable
     */
    public function infaqHistoryIndex(InfaqHistoryDataTable $dataTable)
    {
        return $dataTable->render('infaq.history.index');
    }

    /**
     * Display infaq image datatable
     */
    public function infaqImageIndex(InfaqImageDataTable $dataTable)
    {
        return $dataTable->render('infaq.image.index');
    }

    /**
     * Show the form for creating a new infaq list
     */
    public function createInfaqList()
    {
        $categories = InfaqList::getCategories();
        return view('infaq.list.create', compact('categories'));
    }

    /**
     * Store a newly created infaq list
     */
    public function storeInfaqList(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:infaq_lists,slug',
            'description' => 'nullable|string',
            'category' => 'required|in:operasional,sosial,pembangunan,bencana,umum',
            'dana_dibutuhkan' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $infaqList = InfaqList::create($validated);

        return redirect()
            ->route('infaq.list.index')
            ->with('success', 'Pos infaq berhasil ditambahkan');
    }

    /**
     * Display the specified infaq list
     */
    public function showInfaqList(InfaqList $infaqList)
    {
        $infaqList->load(['infaqImages', 'infaqHistories']);
        return view('infaq.list.show', compact('infaqList'));
    }

    /**
     * Show the form for editing the specified infaq list
     */
    public function editInfaqList(InfaqList $infaqList)
    {
        $categories = InfaqList::getCategories();
        return view('infaq.list.edit', compact('infaqList', 'categories'));
    }

    /**
     * Update the specified infaq list
     */
    public function updateInfaqList(Request $request, InfaqList $infaqList)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:infaq_lists,slug,' . $infaqList->id,
            'description' => 'nullable|string',
            'category' => 'required|in:operasional,sosial,pembangunan,bencana,umum',
            'dana_dibutuhkan' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $infaqList->update($validated);

        return redirect()
            ->route('infaq.list.index')
            ->with('success', 'Pos infaq berhasil diperbarui');
    }

    /**
     * Remove the specified infaq list
     */
    public function destroyInfaqList(InfaqList $infaqList)
    {
        $infaqList->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pos infaq berhasil dihapus'
        ]);
    }

    /**
     * Display the specified infaq history
     */
    public function showInfaqHistory(InfaqHistory $infaqHistory)
    {
        $infaqHistory->load(['user', 'infaqList', 'toko', 'selling']);
        return view('infaq.history.show', compact('infaqHistory'));
    }

    /**
     * Show the form for editing infaq history status
     */
    public function editInfaqHistory(InfaqHistory $infaqHistory)
    {
        if (!$infaqHistory->canChangeStatus()) {
            return redirect()
                ->back()
                ->with('error', 'Status tidak dapat diubah');
        }

        $statuses = InfaqHistory::getStatuses();
        return view('infaq.history.edit', compact('infaqHistory', 'statuses'));
    }

    /**
     * Update infaq history status
     */
    public function updateInfaqHistory(Request $request, InfaqHistory $infaqHistory)
    {
        if (!$infaqHistory->canChangeStatus()) {
            return redirect()
                ->back()
                ->with('error', 'Status tidak dapat diubah');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        $infaqHistory->update($validated);

        return redirect()
            ->route('infaq.history.index')
            ->with('success', 'Status infaq berhasil diperbarui');
    }

    /**
     * Remove the specified infaq history
     */
    public function destroyInfaqHistory(InfaqHistory $infaqHistory)
    {
        if ($infaqHistory->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya riwayat dengan status pending yang dapat dihapus'
            ], 400);
        }

        $infaqHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat infaq berhasil dihapus'
        ]);
    }

    /**
     * Show the form for creating a new infaq image
     */
    public function createInfaqImage()
    {
        $infaqLists = InfaqList::active()->get();
        return view('infaq.image.create', compact('infaqLists'));
    }

    /**
     * Store a newly created infaq image
     */
    public function storeInfaqImage(Request $request)
    {
        $validated = $request->validate([
            'infaq_list_id' => 'required|exists:infaq_lists,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_main' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('infaq/images', $imageName, 'public');

            $validated['image_path'] = 'storage/' . $imagePath;
        }

        InfaqImage::create($validated);

        return redirect()
            ->route('infaq.image.index')
            ->with('success', 'Gambar infaq berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified infaq image
     */
    public function editInfaqImage(InfaqImage $infaqImage)
    {
        $infaqLists = InfaqList::active()->get();
        return view('infaq.image.edit', compact('infaqImage', 'infaqLists'));
    }

    /**
     * Update the specified infaq image
     */
    public function updateInfaqImage(Request $request, InfaqImage $infaqImage)
    {
        $validated = $request->validate([
            'infaq_list_id' => 'required|exists:infaq_lists,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_main' => 'boolean',
        ]);

        // Handle image upload if new image provided
        if ($request->hasFile('image')) {
            // Delete old image
            if (file_exists(public_path($infaqImage->image_path))) {
                unlink(public_path($infaqImage->image_path));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('infaq/images', $imageName, 'public');

            $validated['image_path'] = 'storage/' . $imagePath;
        }

        $infaqImage->update($validated);

        return redirect()
            ->route('infaq.image.index')
            ->with('success', 'Gambar infaq berhasil diperbarui');
    }

    /**
     * Remove the specified infaq image
     */
    public function destroyInfaqImage(InfaqImage $infaqImage)
    {
        // Delete image file
        if (file_exists(public_path($infaqImage->image_path))) {
            unlink(public_path($infaqImage->image_path));
        }

        $infaqImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gambar infaq berhasil dihapus'
        ]);
    }
}
