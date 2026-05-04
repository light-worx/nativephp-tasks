<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Load saved API credentials from SecureStorage into the tasks-api config
        // so the SDK's singleton picks them up on first resolution.
        if (class_exists(\Native\Mobile\Facades\SecureStorage::class)) {
            try {
                $url    = \Native\Mobile\Facades\SecureStorage::get('tasks_api_url');
                $id     = \Native\Mobile\Facades\SecureStorage::get('tasks_client_id');
                $secret = \Native\Mobile\Facades\SecureStorage::get('tasks_client_secret');

                if ($url)    config(['tasks-api.base_url'      => $url]);
                if ($id)     config(['tasks-api.client_id'     => $id]);
                if ($secret) config(['tasks-api.client_secret' => $secret]);
            } catch (\Throwable) {
                // SecureStorage unavailable outside NativePHP context — safe to ignore.
            }
        }
    }
}