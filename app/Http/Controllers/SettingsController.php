<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Native\Mobile\Facades\SecureStorage;

class SettingsController extends Controller
{
    public function index(): View
    {
        $apiUrl      = SecureStorage::get('tasks_api_url')      ?? config('tasks-api.base_url', '');
        $clientId    = SecureStorage::get('tasks_client_id')    ?? config('tasks-api.client_id', '');
        $hasSecret   = (bool) SecureStorage::get('tasks_client_secret');

        return view('settings.index', compact('apiUrl', 'clientId', 'hasSecret'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'api_url'       => 'required|url',
            'client_id'     => 'required|string',
            'client_secret' => 'nullable|string',
        ]);

        SecureStorage::put('tasks_api_url',   $request->api_url);
        SecureStorage::put('tasks_client_id', $request->client_id);

        if ($request->filled('client_secret')) {
            SecureStorage::put('tasks_client_secret', $request->client_secret);
        }

        // Update runtime config so the already-bound TasksApiClient picks up new values
        config([
            'tasks-api.base_url'      => $request->api_url,
            'tasks-api.client_id'     => $request->client_id,
        ]);

        if ($request->filled('client_secret')) {
            config(['tasks-api.client_secret' => $request->client_secret]);
        }

        session()->flash('success', 'Settings saved.');

        return redirect()->route('settings');
    }
}