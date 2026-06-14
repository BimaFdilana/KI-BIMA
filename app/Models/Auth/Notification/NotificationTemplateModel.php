<?php

namespace App\Models\Auth\Notification;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplateModel extends Model
{
    use HasUuids;

    protected $table = 'notification_templates';

    protected $fillable = [
        'type',
        'title_template',
        'message_template',
        'path_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
