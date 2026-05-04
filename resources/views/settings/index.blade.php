@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<form method="POST" action="{{ route('settings.store') }}" class="px-4 py-5 space-y-5">
    @csrf

    <div class="bg-white rounded-2xl shadow-sm divide-y divide-gray-100">

        <div class="p-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tasks API URL</label>
            <input type="url"
                   name="api_url"
                   value="{{ old('api_url', $apiUrl) }}"
                   required
                   placeholder="https://tasks.example.com"
                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-brand @error('api_url') border-red-400 @enderror">
            @error('api_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="p-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
            <input type="text"
                   name="client_id"
                   value="{{ old('client_id', $clientId) }}"
                   required
                   placeholder="Your API client ID"
                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-brand @error('client_id') border-red-400 @enderror">
            @error('client_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="p-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
            <input type="password"
                   name="client_secret"
                   placeholder="{{ $hasSecret ? '••••••••  (leave blank to keep)' : 'Enter your client secret' }}"
                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                          focus:outline-none focus:ring-2 focus:ring-brand">
            <p class="text-xs text-gray-400 mt-1.5">Stored securely in the device keystore.</p>
        </div>

    </div>

    <button type="submit"
            class="w-full bg-brand text-white font-semibold rounded-xl py-3.5 shadow
                   active:scale-95 transition-transform">
        Save Settings
    </button>

</form>

<div class="px-4 mt-4 text-center text-xs text-gray-400 space-y-0.5">
    <p>Tasks for Android · Powered by NativePHP</p>
</div>
@endsection
