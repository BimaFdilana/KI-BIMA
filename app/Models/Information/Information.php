<?php

namespace App\Models\Information;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Information extends Model
{
    use HasFactory;

    protected $table = 'informations';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'category_id',
        'visibility',
        'shares_count',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'shares_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InformationCategory::class, 'category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(InformationMedia::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(InformationComment::class);
    }

    public function parentComments(): HasMany
    {
        return $this->hasMany(InformationComment::class)->whereNull('parent_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
