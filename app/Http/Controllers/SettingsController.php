<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Native\Mobile\Facades\SecureStorage;

class SettingsController extends Controller
{
    public function index(): View
    {
        $apiUrl   = SecureStorage::get('tasks_api_url')   ?? config('tasks-api-client.base_url', '');
        $apiToken = SecureStorage::get('tasks_api_token') ? '••••••••' : '';

        return view('settings.index', compact('apiUrl', 'apiToken'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'api_url'   => 'required|url',
            'api_token' => 'nullable|string',
        ]);

        SecureStorage::put('tasks_api_url', $request->api_url);

        if ($request->filled('api_token')) {
            SecureStorage::put('tasks_api_token', $request->api_token);
        }

        // Refresh the runtime config so the SDK picks up new values immediately
        config(['tasks-api-client.base_url' => $request->api_url]);

        if ($request->filled('api_token')) {
            config(['tasks-api-client.token' => $request->api_token]);
        }

        session()->flash('success', 'Settings saved.');

        return redirect()->route('settings');
    }
}
