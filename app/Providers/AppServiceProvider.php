<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Define the 'api' rate limiter that's referenced in your UserModel
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // If you have a specific UserModel rate limiter, define it
        RateLimiter::for('App\\Models\\Auth\\UserModel::api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Or define a more specific user-based rate limiter
        RateLimiter::for('user-api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(1000)->by($request->user()->id)
                : Limit::perMinute(100)->by($request->ip());
        });

        Carbon::setLocale('id');
        require_once app_path('Helpers/ToastHelper.php');

        Relation::morphMap([
            'user' => 'App\Models\Auth\UserModel',
        ]);

        // if (config('app.env') == 'local') {
        //     URL::forceScheme('https');
        // }
    }
}