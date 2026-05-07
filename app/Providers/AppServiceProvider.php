<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Native\Mobile\Facades\SecureStorage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Log::info('boot start', ['time' => microtime(true)]);
        if (class_exists(SecureStorage::class)) {
            try {
                $url    = SecureStorage::get('tasks_api_url');
                $id     = SecureStorage::get('tasks_client_id');
                $secret = SecureStorage::get('tasks_client_secret');
                $email  = SecureStorage::get('tasks_default_email') ?? env('TASKS_DEFAULT_EMAIL');

                if ($url)    config(['tasks-api.base_url'      => $url]);
                if ($id)     config(['tasks-api.client_id'     => $id]);
                if ($secret) config(['tasks-api.client_secret' => $secret]);
                if ($email)  config(['tasks.default_email'     => $email]);

            } catch (\Throwable) {
                // SecureStorage unavailable outside NativePHP context — safe to ignore.
            }
        }
        Log::info('boot end', ['time' => microtime(true)]);
    }
}