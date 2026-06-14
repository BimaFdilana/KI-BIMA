<?php

namespace App\Services;

use App\Models\Toko\KasirAuditLog;
use Illuminate\Http\Request;

class KasirAuditService
{
    public static function log(
        int $kasirId,
        int $tokoId,
        string $action,
        string $status,
        array $details = [],
        ?Request $request = null
    ) {
        KasirAuditLog::create([
            'kasir_id' => $kasirId,
            'toko_id' => $tokoId,
            'action' => $action,
            'status' => $status,
            'details' => json_encode($details),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
