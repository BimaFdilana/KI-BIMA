<?php

namespace App\Models\Auth\Notification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationGroupModel extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notification_id',
        'group_type',
        'group_id',
    ];

    /**
     * Get the notification that this group belongs to.
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(NotificationModel::class, 'notification_id');
    }
}