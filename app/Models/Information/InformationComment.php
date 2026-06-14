<?php

namespace App\Models\Information;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InformationComment extends Model
{
    use HasFactory;

    protected $table = 'information_comments';

    protected $fillable = [
        'information_id',
        'user_id',
        'device_id',
        'parent_id',
        'content',
        'replies_count',
    ];

    protected $casts = [
        'replies_count' => 'integer',
    ];

    public function information(): BelongsTo
    {
        return $this->belongsTo(Information::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InformationComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(InformationComment::class, 'parent_id');
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
