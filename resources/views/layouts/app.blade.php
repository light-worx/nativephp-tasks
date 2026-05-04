<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tasks')</title>

    {{-- Tailwind via CDN (swap for a compiled build in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#6366f1', dark: '#4f46e5' }
                    }
                }
            }
        }
    </script>

    <style>
        /* Safe-area insets for notch / home indicator */
        body { padding-top: env(safe-area-inset-top); padding-bottom: env(safe-area-inset-bottom); }
        .bottom-nav { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- ── Top bar ─────────────────────────────────────────────────────── --}}
    <header class="bg-brand text-white shadow-md sticky top-0 z-20">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-2">
                @hasSection('back')
                    <a href="{{ url()->previous() }}" class="mr-1 p-1 rounded-full hover:bg-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif
                <h1 class="text-lg font-semibold tracking-tight">@yield('title', 'Tasks')</h1>
            </div>

            <div class="flex items-center gap-2">
                @yield('header-actions')
            </div>
        </div>
    </header>

    {{-- ── Flash messages ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mx-4 mt-3 p-3 rounded-lg bg-green-100 text-green-800 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mx-4 mt-3 p-3 rounded-lg bg-red-100 text-red-800 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Main content ─────────────────────────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto pb-20">
        @yield('content')
    </main>

    {{-- ── Bottom navigation ────────────────────────────────────────────── --}}
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 bottom-nav z-20">
        <div class="flex">

            <a href="{{ route('tasks.index') }}"
               class="flex-1 flex flex-col items-center py-2 text-xs
                      {{ request()->routeIs('tasks.*') ? 'text-brand' : 'text-gray-500' }}">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Tasks
            </a>

            <a href="{{ route('tasks.create') }}"
               class="flex-1 flex flex-col items-center py-2 text-xs text-gray-500">
                <div class="w-10 h-10 -mt-5 bg-brand rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span class="mt-1">New</span>
            </a>

            <a href="{{ route('settings') }}"
               class="flex-1 flex flex-col items-center py-2 text-xs
                      {{ request()->routeIs('settings*') ? 'text-brand' : 'text-gray-500' }}">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

        </div>
    </nav>

</body>
</html>
