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

        // Dynamic System Health & Telemetry for Sidebar Navigation
        view()->composer('layouts.sidebar', function ($view) {
            try {
                $failedCount = \App\Models\MailQueue::where('status', 'F')->count();
                $queuedCount = \App\Models\MailQueue::where('status', 'Q')->count();
                $activeRelays = \App\Models\OutboundMailAccount::where('status', 1)->count();
                $totalRelays = \App\Models\OutboundMailAccount::count();
            } catch (\Throwable $e) {
                $failedCount = 0;
                $queuedCount = 0;
                $activeRelays = 0;
                $totalRelays = 0;
            }

            $view->with([
                'sidebarFailedCount' => $failedCount,
                'sidebarQueuedCount' => $queuedCount,
                'sidebarActiveRelays' => $activeRelays,
                'sidebarTotalRelays' => $totalRelays,
            ]);
        });
    }
}
