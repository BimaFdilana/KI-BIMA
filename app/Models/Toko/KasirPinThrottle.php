<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Model;

class KasirPinThrottle extends Model
{
    protected $table = 'kasir_pin_throttles';
    
    protected $fillable = [
        'kasir_id', 
        'toko_id', 
        'failed_attempts', 
        'last_attempt_at', 
        'locked_until'
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_until && now()->isBefore($this->locked_until);
    }

    public function reset(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_attempt_at' => now(),
        ]);
    }
}
