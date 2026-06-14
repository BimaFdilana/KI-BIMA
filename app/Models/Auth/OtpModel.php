<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    use HasFactory;

    protected $table = "otp_auth";

    protected $fillable = [
        'user_id',
        'type',
        'identifier',
        'code',
        'expires_at',
        'attempts'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the OTP.
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    /**
     * Check if the OTP is expired.
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
