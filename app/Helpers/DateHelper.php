<?php
namespace App\Helpers;
use Carbon\Carbon;

class DateHelper
{
    public static function formatCreatedAt($dateTime)
    {
        $createdAt = Carbon::parse($dateTime)->setTimezone('Asia/Jakarta');
        $now = Carbon::now('Asia/Jakarta');
       
        Carbon::setLocale('id');
       
        // Jika tanggal di masa depan
        if ($createdAt->greaterThan($now)) {
            return 'Baru saja';
        }
       
        $diffInMinutes = $createdAt->diffInMinutes($now);
        $diffInHours = $createdAt->diffInHours($now);
        $diffInDays = $createdAt->diffInDays($now);
       
        if ($diffInMinutes < 1) {
            return 'Baru saja';
        } elseif ($diffInMinutes < 60) {
            return floor($diffInMinutes) . ' menit yang lalu';
        } elseif ($diffInHours < 24) {
            return floor($diffInHours) . ' jam yang lalu';
        } elseif ($diffInDays < 7) {
            // Untuk hari dengan jam yang lebih detail
            if ($diffInDays >= 1) {
                $remainingHours = $diffInHours % 24;
                if ($remainingHours > 0) {
                    return floor($diffInDays) . ' hari ' . $remainingHours . ' jam yang lalu';
                } else {
                    return floor($diffInDays) . ' hari yang lalu';
                }
            } else {
                return floor($diffInHours) . ' jam yang lalu';
            }
        } else {
            return $createdAt->isoFormat('dddd, D MMMM YYYY');
        }
    }
}

// Penggunaan dalam DataTable
// ->editColumn('created_at', function ($row) {
//     return DateHelper::formatCreatedAt($row->created_at);
// })