<?php

namespace App\Models\PakDul;

use App\Models\Toko\TokoModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayLatterConfig extends Model
{
    use HasFactory;

    protected $table = 'paylatter_configs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'toko_id',
        'default_limit',
        'min_limit',
        'max_limit',
        'grace_period_days',
        'interest_rate',
        'max_loan_days',
        'penalty_rate',
        'is_active'
    ];

    protected $casts = [
        'default_limit' => 'decimal:2',
        'min_limit' => 'decimal:2',
        'max_limit' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function toko()
    {
        return $this->belongsTo(TokoModel::class);
    }
}
