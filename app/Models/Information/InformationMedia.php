<?php

namespace App\Models\Information;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class InformationMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'information_id',
        'type',
        'media_path',
        'thumbnail_path',
        'alt_text',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the information that owns the media
     */
    public function information(): BelongsTo
    {
        return $this->belongsTo(Information::class);
    }

    /**
     * Get the full URL of the media
     */
    public function getMediaUrlAttribute(): string
    {
        return Storage::url($this->media_path);
    }

    /**
     * Get the full URL of the thumbnail
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }

    /**
     * Check if media is an image
     */
    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * Check if media is a video
     */
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    /**
     * Scope for ordering media
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
