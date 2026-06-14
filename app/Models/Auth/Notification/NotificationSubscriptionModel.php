<?php

namespace App\Models\Auth\Notification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationSubscriptionModel extends Model
{
    use HasUuids;
    
    protected $table = 'notification_subscriptions';
    
    protected $fillable = [
        'user_type', 
        'user_id', 
        'subscribable_type', 
        'subscribable_id', 
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the user that owns the subscription.
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the subscribable entity.
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
}