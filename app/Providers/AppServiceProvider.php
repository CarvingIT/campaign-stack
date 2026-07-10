<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\Transports\CustomApiTransport;
use Illuminate\Support\Facades\Mail;

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
    public function boot(): void
    {
        Mail::extend('custom_api', function (array $config) {
            return new CustomApiTransport($config ?? '');
        });
    }
}
