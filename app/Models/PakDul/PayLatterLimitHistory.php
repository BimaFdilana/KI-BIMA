<?php

namespace App\Models\PakDul;

use App\Models\Auth\UserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Model PayLatterLimitHistory
class PayLatterLimitHistory extends Model
{
    use HasFactory;

    protected $table = 'paylatter_limit_histories';
    protected $fillable = [
        'paylatter_account_id',
        'old_limit',
        'new_limit',
        'increase_amount',
        'reason',
        'notes',
        'approved_by'
    ];

    protected $casts = [
        'old_limit' => 'decimal:2',
        'new_limit' => 'decimal:2',
        'increase_amount' => 'decimal:2'
    ];

    public function account()
    {
        return $this->belongsTo(PayLatterAccount::class, 'paylatter_account_id');
    }

    public function approver()
    {
        return $this->belongsTo(UserModel::class, 'approved_by');
    }
}
