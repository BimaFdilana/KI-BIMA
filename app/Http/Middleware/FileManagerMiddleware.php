<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add security checks here if needed
        // For example, check if user is authenticated
        // if (!auth()->check()) {
        //     return redirect()->route('login');
        // }

        // Check if user has permission to access file manager
        // if (!auth()->user()->can('access-file-manager')) {
        //     abort(403, 'Unauthorized access to file manager');
        // }

        // Validate allowed file extensions for uploads
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'bmp',
                'svg',
                'webp', // Images
                'pdf',
                'doc',
                'docx',
                'xls',
                'xlsx',
                'ppt',
                'pptx', // Documents  
                'txt',
                'csv',
                'json',
                'xml', // Text files
                'zip',
                'rar',
                '7z', // Archives
                'mp4',
                'avi',
                'mov',
                'wmv',
                'flv', // Videos
                'mp3',
                'wav',
                'ogg',
                'flac', // Audio
            ];

            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe file tidak diizinkan. Tipe file yang diizinkan: ' . implode(', ', $allowedExtensions)
                ], 422);
            }

            // Check file size (max 10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Ukuran maksimum: 10MB'
                ], 422);
            }
        }

        // Validate disk parameter
        $allowedDisks = ['public', 'local'];
        if ($request->has('disk') && !in_array($request->get('disk'), $allowedDisks)) {
            return response()->json([
                'success' => false,
                'message' => 'Disk tidak valid'
            ], 422);
        }

        // Prevent directory traversal attacks
        if ($request->has('path')) {
            $path = $request->get('path');
            if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Path tidak valid'
                ], 422);
            }
        }

        return $next($request);
    }
}
