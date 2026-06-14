<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    public function index(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $path = $request->get('path', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        // Get storage statistics
        $stats = $this->getStorageStats($disk);

        // Get files and folders
        $items = $this->getDirectoryContents($disk, $path, $page, $perPage);

        // Get breadcrumb
        $breadcrumb = $this->getBreadcrumb($path);
        if ($request->ajax()) {
            return response()->json([
                'items' => $items['items'],
                'hasMore' => $items['hasMore'],
                'currentPage' => $items['currentPage']
            ]);
        }

        return view('file-manager.index', compact('stats', 'items', 'breadcrumb', 'disk', 'path'));
    }

    private function getStorageStats($disk)
    {
        $storage = Storage::disk($disk);
        $files = $storage->allFiles();

        $totalSize = 0;
        $extensionCounts = [];
        $totalFiles = count($files);

        foreach ($files as $file) {
            $size = $storage->size($file);
            $totalSize += $size;

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!isset($extensionCounts[$extension])) {
                $extensionCounts[$extension] = 0;
            }
            $extensionCounts[$extension]++;
        }

        // Sort extensions by count
        arsort($extensionCounts);

        return [
            'totalSize' => $this->formatBytes($totalSize),
            'totalFiles' => $totalFiles,
            'extensionCounts' => $extensionCounts,
            'diskUsage' => $this->getDiskUsage()
        ];
    }

    private function getDirectoryContents($disk, $path, $page, $perPage)
    {
        $storage = Storage::disk($disk);
        $directories = $storage->directories($path);
        $files = $storage->files($path);

        // Combine and sort
        $items = [];

        // Add directories first
        foreach ($directories as $dir) {
            $items[] = [
                'name' => basename($dir),
                'type' => 'directory',
                'path' => $dir,
                'size' => null,
                'modified' => null,
                'extension' => null
            ];
        }

        // Add files
        foreach ($files as $file) {
            $items[] = [
                'name' => basename($file),
                'type' => 'file',
                'path' => $file,
                'size' => $this->formatBytes($storage->size($file)),
                'modified' => date('Y-m-d H:i:s', $storage->lastModified($file)),
                'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
                'url' => $storage->url($file)
            ];
        }

        // Pagination
        $total = count($items);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = array_slice($items, $offset, $perPage);

        return [
            'items' => $paginatedItems,
            'hasMore' => $offset + $perPage < $total,
            'currentPage' => $page,
            'total' => $total
        ];
    }

    private function getBreadcrumb($path)
    {
        if (empty($path)) {
            return [['name' => 'Home', 'path' => '']];
        }

        $parts = explode('/', $path);
        $breadcrumb = [['name' => 'Home', 'path' => '']];
        $currentPath = '';

        foreach ($parts as $part) {
            $currentPath .= ($currentPath ? '/' : '') . $part;
            $breadcrumb[] = ['name' => $part, 'path' => $currentPath];
        }

        return $breadcrumb;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getDiskUsage()
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2)
        ];
    }

    public function delete(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $path = $request->get('path');
        $type = $request->get('type');

        $storage = Storage::disk($disk);

        try {
            if ($type === 'directory') {
                $storage->deleteDirectory($path);
            } else {
                $storage->delete($path);
            }

            return response()->json(['success' => true, 'message' => 'File/folder berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus file/folder: ' . $e->getMessage()], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'disk' => 'required|string',
            'path' => 'nullable|string'
        ]);

        $disk = $request->get('disk');
        $path = $request->get('path', '');
        $file = $request->file('file');

        try {
            $filename = $file->getClientOriginalName();
            $filePath = $path ? $path . '/' . $filename : $filename;

            Storage::disk($disk)->putFileAs($path, $file, $filename);

            return response()->json(['success' => true, 'message' => 'File berhasil diupload']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal upload file: ' . $e->getMessage()], 500);
        }
    }

    public function replace(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'disk' => 'required|string',
            'path' => 'required|string'
        ]);

        $disk = $request->get('disk');
        $path = $request->get('path');
        $file = $request->file('file');

        $storage = Storage::disk($disk);

        // Check if original file exists
        if (!$storage->exists($path)) {
            return response()->json(['success' => false, 'message' => 'File asli tidak ditemukan'], 404);
        }

        // Check if extensions match
        $originalExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $newExtension = strtolower($file->getClientOriginalExtension());

        if ($originalExtension !== $newExtension) {
            return response()->json(['success' => false, 'message' => 'Ekstensi file harus sama dengan file asli'], 400);
        }

        try {
            // Replace the file content
            $storage->put($path, file_get_contents($file->getRealPath()));

            return response()->json(['success' => true, 'message' => 'File berhasil diganti']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengganti file: ' . $e->getMessage()], 500);
        }
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'disk' => 'required|string',
            'path' => 'nullable|string'
        ]);

        $disk = $request->get('disk');
        $path = $request->get('path', '');
        $name = $request->get('name');

        $folderPath = $path ? $path . '/' . $name : $name;

        try {
            Storage::disk($disk)->makeDirectory($folderPath);
            return response()->json(['success' => true, 'message' => 'Folder berhasil dibuat']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat folder: ' . $e->getMessage()], 500);
        }
    }

    public function download(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $path = $request->get('path');

        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = basename($path);
        $content = $storage->get($path);
        $mimeType = $storage->mimeType($path);

        return Response::make($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}
