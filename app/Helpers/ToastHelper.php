<?php
namespace App\Helpers;
use Carbon\Carbon;

class ToastHelper
{
    /**
     * Show success toast
     */
    public static function success($message, $title = 'Berhasil!')
    {
        session()->flash('success', $message);
        return redirect()->back();
    }
    
    /**
     * Show error toast
     */
    public static function error($message, $title = 'Error!')
    {
        session()->flash('error', $message);
        return redirect()->back();
    }
    
    /**
     * Show warning toast
     */
    public static function warning($message, $title = 'Peringatan!')
    {
        session()->flash('warning', $message);
        return redirect()->back();
    }
    
    /**
     * Show info toast
     */
    public static function info($message, $title = 'Informasi')
    {
        session()->flash('info', $message);
        return redirect()->back();
    }
    
    /**
     * Show toast without redirect (for AJAX)
     */
    public static function json($type, $message, $title = null)
    {
        return response()->json([
            'toast' => [
                'type' => $type,
                'message' => $message,
                'title' => $title
            ]
        ]);
    }
}

// Tambahkan ke app/Providers/AppServiceProvider.php di method boot():
/*
public function boot()
{
    // Load helper
    require_once app_path('Helpers/ToastHelper.php');
}
*/

// Atau buat alias di config/app.php:
/*
'aliases' => [
    'Toast' => App\Helpers\ToastHelper::class,
]
*/