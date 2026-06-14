<?php

namespace App\Http\Controllers\Barang;

use App\DataTables\BarangTokoDataTable;
use App\Http\Controllers\Controller;
use App\Jobs\Exports\ExportBarangTokoJob;
use Illuminate\Http\Request;
use App\Models\Barang\Category;
use App\Models\Barang\Brand;
use App\Models\Barang\Subcategory;
use App\Models\Barang\TypeItem;
use App\Services\Message\NotificationService;
use App\Models\Toko\TokoModel;
use Illuminate\Support\Facades\Auth;

class BarangTokoController extends Controller
{
    protected $notifService;

    public function __construct(NotificationService $notifService)
    {
        $this->notifService = $notifService;
    }
    public function index(Request $request, BarangTokoDataTable $dataTable)
    {
        $filter = $request->get('filter', null);
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();
        $types = TypeItem::all();
        $tokos = TokoModel::all();
        // Apply filter and return dataTable
        return $dataTable->setFilter($filter)->render('barangtoko.index', compact('categories', 'subcategories', 'brands', 'types', 'tokos'));
    }


    public function penjualanToko(Request $request, BarangTokoDataTable $dataTable)
    {
        $filter = $request->get('filter', null);
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();
        $types = TypeItem::all();
        $tokos = TokoModel::all();
        // Apply filter and return dataTable
        return $dataTable->setFilter($filter)->render('barangtoko.index', compact('categories', 'subcategories', 'brands', 'types', 'tokos'));
    }


    // Di dalam controller kamu
    public function export(Request $request, $format)
    {
        $user = Auth::user();
        $filters = $request->except(['format', 'page']);

        if (!in_array($format, ['excel', 'csv', 'pdf'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format tidak didukung.',
            ]);
        }

        $extension = match ($format) {
            'csv' => 'csv',
            'pdf' => 'pdf',
            default => 'xlsx',
        };

        $fileName = "barang_toko_{$user->id}_" . now()->format('Y_m_d_H_i_s') . ".{$extension}";
        $filePath = "/exports/{$fileName}";

        $notification = $this->notifService->sendToUserFromSystem(
            $user,
            'export_data',
            [
                'message' => 'Export sedang diproses di background. Anda akan diberitahu saat selesai.',
                'title' => 'Export Queued: Barang Toko (' . strtoupper($format) . ')',
            ],
            $filePath
        );

        ExportBarangTokoJob::dispatch(
            $filters,
            $user,
            $format,
            $notification->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Export sedang diproses di background. Anda akan diberitahu saat selesai.',
            'time' => now()->diffForHumans(),
        ]);
    }


    public function import(Request $request, $type)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        ImportBarangTokoJob::dispatch(auth()->user(), $file->store('imports'), $type);

        return response()->json([
            'message' => 'Proses import sedang berjalan.'
        ]);
    }
}
