<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Toko\TokoModel;
use App\Models\Toko\JabatanModel;


class TokoPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();
        $tokoSlug = $request->route('tokoSlug');
        $toko = TokoModel::where('slug', $tokoSlug)->first();

        if (!$toko) {
            abort(404, 'Toko tidak ditemukan');
        }

        // Cek apakah user adalah anggota toko
        $userToko = $user->tokos()->where('toko.id', $toko->id)->first();
        if (!$userToko) {
            abort(403, 'Anda tidak memiliki akses ke toko ini');
        }

        // Ambil jabatan user di toko ini
        $jabatan = JabatanModel::find($userToko->pivot->jabatan_id);

        // Cek permission berdasarkan jabatan
        switch ($permission) {
            case 'invite':
                if (!$jabatan->can_invite_users) {
                    abort(403, 'Anda tidak memiliki izin untuk mengundang user');
                }
                break;
            case 'manage-inventory':
                if (!$jabatan->can_manage_inventory) {
                    abort(403, 'Anda tidak memiliki izin untuk mengelola inventaris');
                }
                break;
            case 'view-reports':
                if (!$jabatan->can_view_reports) {
                    abort(403, 'Anda tidak memiliki izin untuk melihat laporan');
                }
                break;
            case 'manage-orders':
                if (!$jabatan->can_manage_orders) {
                    abort(403, 'Anda tidak memiliki izin untuk mengelola pesanan');
                }
                break;
        }

        return $next($request);
    }
}
