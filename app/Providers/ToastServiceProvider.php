<?php

namespace App\Providers;

use App\View\Components\Alert\Toast as AlertToast;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ToastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::component('toast', AlertToast::class);
    }
}