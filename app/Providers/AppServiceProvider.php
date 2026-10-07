<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Support\Facades\Mail;
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
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Mail::extend('gmail-api', function (): GmailApiTransport {
            return new GmailApiTransport(
                (string) config('services.gmail_api.client_id'),
                (string) config('services.gmail_api.client_secret'),
                (string) config('services.gmail_api.refresh_token'),
            );
        });
    }
}
