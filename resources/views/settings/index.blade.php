@extends('layouts.app')

@section('title', 'Settings')

@section('content')

@if(!$clientId)
<div style="margin:16px; padding:16px; background:#fef3c7; border-radius:12px; border-left:4px solid #f59e0b;">
    <p style="font-weight:600; font-size:14px; color:#92400e; margin:0 0 4px;">API credentials required</p>
    <p style="font-size:12px; color:#92400e; margin:0;">Enter your Tasks API URL, client ID and secret to get started.</p>
</div>
@endif

<form method="POST" action="{{ route('settings.store') }}" style="padding:16px 16px 0;">
    @csrf

    <div style="background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08); overflow:hidden; margin-bottom:16px;">

        <div style="padding:16px; border-bottom:1px solid #f3f4f6;">
            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                Tasks API URL
            </label>
            <input type="url"
                   name="api_url"
                   value="{{ old('api_url', $apiUrl) }}"
                   required
                   placeholder="https://tasks.example.com"
                   style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
                          font-size:14px; background:#f9fafb; box-sizing:border-box;">
            @error('api_url')
                <p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="padding:16px; border-bottom:1px solid #f3f4f6;">
            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                Client ID
            </label>
            <input type="text"
                   name="client_id"
                   value="{{ old('client_id', $clientId) }}"
                   required
                   placeholder="Your API client ID"
                   style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
                          font-size:14px; background:#f9fafb; box-sizing:border-box;">
            @error('client_id')
                <p style="font-size:12px; color:#ef4444; margin:4px 0 0;">{{ $message }}</p>
            @enderror
        </div>

        <div style="padding:16px;">
            <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                Client Secret
            </label>
            <input type="password"
                   name="client_secret"
                   placeholder="{{ $hasSecret ? '••••••••  (leave blank to keep)' : 'Enter your client secret' }}"
                   style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
                          font-size:14px; background:#f9fafb; box-sizing:border-box;">
            <p style="font-size:11px; color:#9ca3af; margin:6px 0 0;">Stored securely in the device keystore.</p>
        </div>

    </div>

    <button type="submit"
            style="width:100%; background:#6366f1; color:#fff; font-weight:600; border:none;
                   border-radius:12px; padding:14px; font-size:15px; cursor:pointer;">
        Save Settings
    </button>

</form>

<div style="text-align:center; padding:24px 16px; font-size:12px; color:#9ca3af;">
    Tasks for Android · Powered by NativePHP
</div>

@endsection