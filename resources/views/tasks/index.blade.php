@extends('layouts.app')

@section('title', 'My Tasks')

@section('header-actions')
    {{-- Filter dropdown --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="p-2 rounded-full hover:bg-white/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </button>
    </div>
@endsection

@section('content')
<div class="px-4 pt-4 space-y-3">

    {{-- Search bar --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-2">
        <input type="hidden" name="filter"   value="{{ $filter }}">
        <input type="hidden" name="priority" value="{{ $priority }}">
        <input type="text"
               name="search"
               value="{{ $search }}"
               placeholder="Search tasks…"
               class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-brand">
        <button type="submit"
                class="bg-brand text-white rounded-xl px-4 py-2 text-sm font-medium shadow-sm">
            Go
        </button>
    </form>

    {{-- Filter tabs --}}
    <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'completed' => 'Done'] as $key => $label)
            <a href="{{ route('tasks.index', array_merge(request()->except('filter', 'page'), ['filter' => $key])) }}"
               class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium
                      {{ $filter === $key
                           ? 'bg-brand text-white shadow'
                           : 'bg-white text-gray-600 border border-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach

        {{-- Priority filters --}}
        @foreach(['high' => '🔴 High', 'medium' => '🟡 Medium', 'low' => '🟢 Low'] as $p => $label)
            <a href="{{ route('tasks.index', array_merge(request()->except('priority', 'page'), ['priority' => $priority === $p ? null : $p])) }}"
               class="flex-shrink-0 px-4 py-1.5 rounded-full text-sm font-medium
                      {{ $priority === $p
                           ? 'bg-gray-800 text-white shadow'
                           : 'bg-white text-gray-600 border border-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Task count --}}
    <p class="text-xs text-gray-500">{{ $total }} task{{ $total === 1 ? '' : 's' }}</p>

    {{-- Task list --}}
    @forelse($tasks as $task)
        @php
            $priorityColor = match($task['priority'] ?? 'medium') {
                'high'   => 'border-l-red-400',
                'medium' => 'border-l-yellow-400',
                'low'    => 'border-l-green-400',
                default  => 'border-l-gray-300',
            };
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border-l-4 {{ $priorityColor }} flex items-start gap-3 p-4">

            {{-- Complete toggle --}}
            <form method="POST" action="{{ route('tasks.toggle', $task['id']) }}">
                @csrf
                <button type="submit"
                        class="mt-0.5 w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0
                               {{ $task['completed'] ? 'bg-green-500 border-green-500' : 'border-gray-300' }}">
                    @if($task['completed'])
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </button>
            </form>

            {{-- Task body --}}
            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm leading-snug {{ $task['completed'] ? 'line-through text-gray-400' : '' }}">
                    {{ $task['title'] }}
                </p>

                @if(!empty($task['description']))
                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $task['description'] }}</p>
                @endif

                <div class="flex flex-wrap gap-2 mt-1.5">
                    @if(!empty($task['due_date']))
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($task['due_date'])->format('d M') }}
                        </span>
                    @endif

                    @if(!empty($task['category']))
                        <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">
                            {{ $task['category'] }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Edit button --}}
            <a href="{{ route('tasks.edit', $task['id']) }}"
               class="text-gray-400 hover:text-brand p-1 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>

        </div>

    @empty
        <div class="text-center py-16 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg font-medium text-gray-500">No tasks found</p>
            <a href="{{ route('tasks.create') }}"
               class="mt-4 inline-block bg-brand text-white rounded-xl px-6 py-2 text-sm font-medium">
                Create your first task
            </a>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($lastPage > 1)
        <div class="flex justify-center gap-2 py-4">
            @if($page > 1)
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                   class="px-4 py-2 rounded-xl bg-white border text-sm text-gray-600">← Prev</a>
            @endif

            <span class="px-4 py-2 text-sm text-gray-500">{{ $page }} / {{ $lastPage }}</span>

            @if($page < $lastPage)
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                   class="px-4 py-2 rounded-xl bg-white border text-sm text-gray-600">Next →</a>
            @endif
        </div>
    @endif

</div>
@endsection
