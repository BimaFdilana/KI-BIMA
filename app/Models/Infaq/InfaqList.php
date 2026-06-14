<?php

namespace App\Models\Infaq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class InfaqList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'infaq_lists';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'dana_dibutuhkan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constants untuk kategori
    const CATEGORY_OPERASIONAL = 'operasional';
    const CATEGORY_SOSIAL = 'sosial';
    const CATEGORY_PEMBANGUNAN = 'pembangunan';
    const CATEGORY_BENCANA = 'bencana';
    const CATEGORY_UMUM = 'umum';

    // Method untuk mendapatkan semua kategori
    public static function getCategories()
    {
        return [
            self::CATEGORY_OPERASIONAL => 'Operasional',
            self::CATEGORY_SOSIAL => 'Sosial',
            self::CATEGORY_PEMBANGUNAN => 'Pembangunan',
            self::CATEGORY_BENCANA => 'Bencana',
            self::CATEGORY_UMUM => 'Umum',
        ];
    }
    public function getInitialsAttribute(): string
    {
        // Hapus kata-kata umum yang tidak perlu
        $commonWords = ['dan', 'di', 'pada', 'untuk', 'yang'];
        $cleanName = str_ireplace($commonWords, '', $this->name);

        // Ambil setiap kata dan buat singkatan
        $words = explode(' ', $cleanName);
        $alias = '';
        foreach ($words as $word) {
            // Ambil huruf pertama dari setiap kata yang tidak kosong
            if (!empty($word)) {
                $alias .= strtoupper(substr($word, 0, 1));
            }
        }

        return $alias;
    }

    // Relasi ke infaq images
    public function infaqImages()
    {
        return $this->hasMany(InfaqImage::class);
    }

    // Relasi ke infaq histories
    public function infaqHistories()
    {
        return $this->hasMany(InfaqHistory::class);
    }

    // Scope untuk pos yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope berdasarkan kategori
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Method untuk mendapatkan total donasi
    public function getTotalDonationsAttribute()
    {
        return $this->infaqHistories()
            ->where('status', 'completed')
            ->sum('amount');
    }

    // Method untuk mendapatkan jumlah donatur
    public function getDonorsCountAttribute()
    {
        return $this->infaqHistories()
            ->where('status', 'completed')
            ->distinct('user_id')
            ->count('user_id');
    }

    // Method untuk mendapatkan label kategori
    public function getCategoryLabelAttribute()
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? ucfirst($this->category);
    }
}
