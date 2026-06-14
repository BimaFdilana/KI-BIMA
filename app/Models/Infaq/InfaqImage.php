<?php

namespace App\Models\Infaq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfaqImage extends Model
{
    use HasFactory;

    protected $table = 'infaq_image';

    protected $fillable = [
        'infaq_list_id',
        'is_main',
        'image_path',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke infaq list
    public function infaqList()
    {
        return $this->belongsTo(InfaqList::class);
    }
}
