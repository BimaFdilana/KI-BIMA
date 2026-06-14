<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class FileManagerService
{
    /**
     * Get file icon based on extension
     */
    public static function getFileIcon($extension)
    {
        $icons = [
            // Images
            'jpg' => '🖼️',
            'jpeg' => '🖼️',
            'png' => '🖼️',
            'gif' => '🖼️',
            'bmp' => '🖼️',
            'svg' => '🖼️',
            'webp' => '🖼️',

            // Documents
            'pdf' => '📄',
            'doc' => '📝',
            'docx' => '📝',
            'xls' => '📊',
            'xlsx' => '📊',
            'ppt' => '📊',
            'pptx' => '📊',

            // Text files
            'txt' => '📄',
            'csv' => '📊',
            'json' => '📄',
            'xml' => '📄',

            // Archives
            'zip' => '📦',
            'rar' => '📦',
            '7z' => '📦',

            // Videos
            'mp4' => '🎬',
            'avi' => '🎬',
            'mov' => '🎬',
            'wmv' => '🎬',
            'flv' => '🎬',

            // Audio
            'mp3' => '🎵',
            'wav' => '🎵',
            'ogg' => '🎵',
            'flac' => '🎵',
        ];

        return $icons[strtolower($extension)] ?? '📄';
    }

    /**
     * Get file color class based on extension
     */
    public static function getFileColorClass($extension)
    {
        $colors = [
            // Images - Blue
            'jpg' => 'text-blue-600 bg-blue-50',
            'jpeg' => 'text-blue-600 bg-blue-50',
            'png' => 'text-blue-600 bg-blue-50',
            'gif' => 'text-blue-600 bg-blue-50',
            'bmp' => 'text-blue-600 bg-blue-50',
            'svg' => 'text-blue-600 bg-blue-50',
            'webp' => 'text-blue-600 bg-blue-50',

            // Documents - Green
            'pdf' => 'text-green-600 bg-green-50',
            'doc' => 'text-green-600 bg-green-50',
            'docx' => 'text-green-600 bg-green-50',
            'xls' => 'text-green-600 bg-green-50',
            'xlsx' => 'text-green-600 bg-green-50',
            'ppt' => 'text-green-600 bg-green-50',
            'pptx' => 'text-green-600 bg-green-50',

            // Text files - Yellow
            'txt' => 'text-yellow-600 bg-yellow-50',
            'csv' => 'text-yellow-600 bg-yellow-50',
            'json' => 'text-yellow-600 bg-yellow-50',
            'xml' => 'text-yellow-600 bg-yellow-50',

            // Archives - Purple
            'zip' => 'text-purple-600 bg-purple-50',
            'rar' => 'text-purple-600 bg-purple-50',
            '7z' => 'text-purple-600 bg-purple-50',

            // Videos - Red
            'mp4' => 'text-red-600 bg-red-50',
            'avi' => 'text-red-600 bg-red-50',
            'mov' => 'text-red-600 bg-red-50',
            'wmv' => 'text-red-600 bg-red-50',
            'flv' => 'text-red-600 bg-red-50',

            // Audio - Indigo
            'mp3' => 'text-indigo-600 bg-indigo-50',
            'wav' => 'text-indigo-600 bg-indigo-50',
            'ogg' => 'text-indigo-600 bg-indigo-50',
            'flac' => 'text-indigo-600 bg-indigo-50',
        ];

        return $colors[strtolower($extension)] ?? 'text-gray-600 bg-gray-50';
    }

    /**
     * Check if file is an image
     */
    public static function isImage($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        return in_array(strtolower($extension), $imageExtensions);
    }

    /**
     * Check if file is a document
     */
    public static function isDocument($extension)
    {
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];
        return in_array(strtolower($extension), $documentExtensions);
    }

    /**
     * Check if file is a video
     */
    public static function isVideo($extension)
    {
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
        return in_array(strtolower($extension), $videoExtensions);
    }

    /**
     * Check if file is an audio
     */
    public static function isAudio($extension)
    {
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac'];
        return in_array(strtolower($extension), $audioExtensions);
    }

    /**
     * Get preview URL for supported files
     */
    public static function getPreviewUrl($disk, $path, $extension)
    {
        if (self::isImage($extension)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }

    /**
     * Sanitize filename
     */
    public static function sanitizeFilename($filename)
    {
        // Remove special characters and replace spaces with underscores
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);

        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        // Remove leading/trailing underscores
        $filename = trim($filename, '_');

        return $filename;
    }

    /**
     * Generate unique filename if file already exists
     */
    public static function generateUniqueFilename($disk, $path, $filename)
    {
        $storage = Storage::disk($disk);
        $fullPath = $path ? $path . '/' . $filename : $filename;

        if (!$storage->exists($fullPath)) {
            return $filename;
        }

        $info = pathinfo($filename);
        $name = $info['filename'];
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';

        $counter = 1;
        do {
            $newFilename = $name . '_' . $counter . $extension;
            $newFullPath = $path ? $path . '/' . $newFilename : $newFilename;
            $counter++;
        } while ($storage->exists($newFullPath));

        return $newFilename;
    }

    /**
     * Get human readable file size
     */
    public static function formatBytes($size, $precision = 2)
    {
        if ($size == 0) return '0 B';

        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        return round(1024 ** $base, $precision) . ' ' . $suffixes[(int) $base];
    }
}
