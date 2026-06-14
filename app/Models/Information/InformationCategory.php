<?php

namespace App\Models\Information;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InformationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
    ];

    /**
     * Get all informations in this category
     */
    public function informations(): HasMany
    {
        return $this->hasMany(Information::class, 'category_id');
    }

    /**
     * Get published informations count
     */
    public function publishedInformationsCount(): int
    {
        return $this->informations()->where('is_published', true)->count();
    }
}
