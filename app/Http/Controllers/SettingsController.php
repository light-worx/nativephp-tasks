<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Native\Mobile\Facades\SecureStorage;

class SettingsController extends Controller
{
    public function index(): View
    {
        $apiUrl       = SecureStorage::get('tasks_api_url')       ?? config('tasks-api.base_url', '');
        $clientId     = SecureStorage::get('tasks_client_id')     ?? config('tasks-api.client_id', '');
        $hasSecret    = (bool) SecureStorage::get('tasks_client_secret');
        $defaultEmail = SecureStorage::get('tasks_default_email') ?? env('TASKS_DEFAULT_EMAIL');

        return view('settings.index', compact('apiUrl', 'clientId', 'hasSecret', 'defaultEmail'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'api_url'       => 'required|url',
            'client_id'     => 'required|string',
            'client_secret' => 'nullable|string',
            'default_email' => 'nullable|email',
        ]);

        SecureStorage::set('tasks_api_url',       $request->api_url);
        SecureStorage::set('tasks_client_id',     $request->client_id);
        SecureStorage::set('tasks_default_email', $request->input('default_email', ''));

        if ($request->filled('client_secret')) {
            SecureStorage::set('tasks_client_secret', $request->client_secret);
        }

        // Update runtime config
        config([
            'tasks-api.base_url'      => $request->api_url,
            'tasks-api.client_id'     => $request->client_id,
            'tasks.default_email'     => $request->input('default_email', ''),
        ]);

        if ($request->filled('client_secret')) {
            config(['tasks-api.client_secret' => $request->client_secret]);
        }

        // Only clear the token cache for this client, not everything
        $cacheKey = 'tasks_api_token_' . md5($request->client_id);
        Cache::forget($cacheKey);
        Cache::forget('tasks_ui.status_map');

        // Rebind the singleton with fresh config
        app()->singleton(\Lightworx\TasksApiClient\TasksApiClient::class, function () {
            return new \Lightworx\TasksApiClient\TasksApiClient(config('tasks-api'));
        });
        // Force re-resolution by removing the existing instance
        app()->forgetInstance(\Lightworx\TasksApiClient\TasksApiClient::class);

        session()->flash('success', 'Settings saved.');
        return redirect()->route('tasks.index');
    }
}