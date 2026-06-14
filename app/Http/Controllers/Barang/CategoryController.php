<?php

namespace App\Http\Controllers\Barang;

use Illuminate\Http\Request;
use App\Models\Barang\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'files' => 'nullable|array|min:1',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif|max:2048', // Hanya image untuk kategori
        ]);

        try {
            $photoPath = null;

            // Proses unggahan file (hanya satu file untuk kategori)
            if ($request->hasFile('files')) {
                $file = $request->file('files')[0]; // Ambil file pertama saja
                
                // Generate unique filename
                $name = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store file in public/categories directory
                $photoPath = $file->storeAs('categories', $name, 'public');
            }

            // Save category
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'photo' => $photoPath
            ]);

            // Return success response
            return redirect()
                ->back()
                ->with('toast', [
                    'message' => 'Kategori berhasil ditambahkan!',
                    'type' => 'success',
                    'tab' => 'categoryDataTable'
                ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('toast', [
                    'message' => 'Gagal menambahkan kategori: ' . $e->getMessage(),
                    'type' => 'error',
                    'tab' => 'categoryDataTable'
                ])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Format photo URL if exists
            $photoUrl = $category->photo ? asset('storage/' . $category->photo) : null;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'photo' => $photoUrl
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            \Log::info($category);
            // Validasi data
            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($category->id)
                ],
                'description' => 'nullable|string',
                'files' => 'nullable|array|max:1',
                'files.*' => 'file|mimes:jpeg,png,jpg,gif|max:2048',
                'remove_photo' => 'nullable|in:0,1'
            ]);

            \Log::info($validatedData);

            $photoPath = $category->photo;

            // Handle photo removal
            if ($request->remove_photo == '1') {
                if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                    Storage::disk('public')->delete($category->photo);
                }
                $photoPath = null;
            }
            // Handle new photo upload
            if ($request->hasFile('files')) {
                // Delete old photo if exists
                if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                    Storage::disk('public')->delete($category->photo);
                }

                $file = $request->file('files')[0];
                
                // Generate unique filename
                $name = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store new file
                $photoPath = $file->storeAs('categories', $name, 'public');
            }

            // Update category
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'photo' => $photoPath
            ]);

            return redirect()
                ->back()
                ->with('toast', [
                    'message' => 'Kategori berhasil diperbarui!',
                    'type' => 'success',
                    'tab' => 'categoryDataTable'
                ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('toast', [
                    'message' => 'Validasi gagal, periksa kembali data yang diinput!',
                    'type' => 'error',
                    'tab' => 'categoryDataTable'
                ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('toast', [
                    'message' => 'Gagal memperbarui kategori: ' . $e->getMessage(),
                    'type' => 'error',
                    'tab' => 'categoryDataTable'
                ])
                ->withInput();
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->subcategories->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena memiliki subkategori'
                ], 400);
            }

            // Delete photo file if exists
            if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }

            // Delete category
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category data for editing (alias for edit method for backward compatibility)
     */
    public function getItemData($id)
    {
        return $this->edit($id);
    }

    /**
     * Display a listing of categories (if needed for API or other purposes)
     */
    public function index()
    {
        try {
            $categories = Category::orderBy('name')->get();
            
            // Format photo URLs
            $categories->transform(function ($category) {
                $category->photo_url = $category->photo ? asset('storage/' . $category->photo) : null;
                return $category;
            });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified category.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $category->photo_url = $category->photo ? asset('storage/' . $category->photo) : null;

            return response()->json([
                'success' => true,
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Toggle category status (if you have status field)
     */
    public function toggleStatus($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Assuming you have 'is_active' field
            if (Schema::hasColumn('categories', 'is_active')) {
                $category->is_active = !$category->is_active;
                $category->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Status kategori berhasil diubah!',
                    'data' => $category
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Field status tidak tersedia'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for select options
     */
    public function getForSelect()
    {
        try {
            $categories = Category::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id'
        ]);

        try {
            $categories = Category::whereIn('id', $request->ids)->get();
            
            // Delete photos
            foreach ($categories as $category) {
                if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                    Storage::disk('public')->delete($category->photo);
                }
            }

            // Delete categories
            Category::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' kategori berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}