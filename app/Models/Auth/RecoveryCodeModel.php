<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class RecoveryCodeModel extends Model
{
    use HasFactory;
    protected $table = 'recovery_code';
    protected $fillable = [
        'user_id',
        'code',
        'last_used',
        'hasUsed',
        'last_used_device'
    ];
    protected $casts = [
        'last_used' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    public function isUsed()
    {
        return $this->hasUsed == 1;
    }

    /**
     * Verify a recovery code
     * 
     * @param int $userId
     * @param string $codeToVerify
     * @return RecoveryCodeModel|null
     */
    public static function verifyCode($userId, $codeToVerify)
    {
        $recoveryCodes = self::where('user_id', $userId)
            ->where('hasUsed', 0)
            ->get();

        foreach ($recoveryCodes as $savedCode) {
            if (Hash::check($codeToVerify, $savedCode->code)) {
                return $savedCode;
            }
        }

        return null;
    }

    /**
     * Mark a recovery code as used
     * 
     * @param string $device Optional device info
     * @return void
     */
    public function markAsUsed($device = null)
    {
        $this->hasUsed = 1;
        $this->last_used = now();
        $this->last_used_device = $device;
        $this->save();
    }
}