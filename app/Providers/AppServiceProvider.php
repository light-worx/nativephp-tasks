<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // When running inside NativePHP, load saved API credentials from
        // SecureStorage so the SDK uses them without needing a .env edit.
        if (class_exists(\Native\Mobile\Facades\SecureStorage::class)) {
            try {
                $url   = \Native\Mobile\Facades\SecureStorage::get('tasks_api_url');
                $token = \Native\Mobile\Facades\SecureStorage::get('tasks_api_token');

                if ($url) {
                    config(['tasks-api-client.base_url' => $url]);
                }
                if ($token) {
                    config(['tasks-api-client.token' => $token]);
                }
            } catch (\Throwable) {
                // SecureStorage may not be available in all environments; safe to ignore.
            }
        }
    }
}
