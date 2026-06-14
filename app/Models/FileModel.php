<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileModel extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'path',
        'filename',
        'mime_type',
        'size',
        'disk',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the attachable model that owns the file.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the URL for the file.
     */
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
