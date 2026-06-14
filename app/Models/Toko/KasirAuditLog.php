<?php

namespace App\Models\Toko;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\UserModel;

class KasirAuditLog extends Model
{
    protected  = 'kasir_audit_logs';

    protected  = [
        'kasir_id',
        'toko_id',
        'action',
        'status',
        'details',
        'ip_address',
        'user_agent',
    ];

    public function kasir()
    {
        return ->belongsTo(UserModel::class, 'kasir_id');
    }

    public function toko()
    {
        return ->belongsTo(TokoModel::class, 'toko_id');
    }
}
