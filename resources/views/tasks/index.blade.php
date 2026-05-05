@extends('layouts.app')

@section('title', 'My Tasks')

@section('header-actions')
    <a href="{{ route('tasks.create') }}" class="p-2 rounded-full hover:bg-white/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
@endsection

@section('content')
<div class="px-4 pt-4 space-y-3">

    {{-- Filter tabs --}}
    <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:4px;">

        <a href="{{ route('tasks.index', ['filter' => 'all']) }}"
           style="flex-shrink:0; padding:6px 16px; border-radius:999px; font-size:14px; font-weight:500; text-decoration:none;
                  {{ $filter === 'all'
                      ? 'background-color:#6366f1; color:#fff;'
                      : 'background-color:#fff; color:#374151; border:1px solid #e5e7eb;' }}">
            All
        </a>

        @foreach($statuses as $label => $status)
            <a href="{{ route('tasks.index', ['filter' => $label]) }}"
               style="flex-shrink:0; padding:6px 16px; border-radius:999px; font-size:14px; font-weight:500; text-decoration:none;
                      {{ $filter === $label
                          ? 'background-color:' . $status['colour'] . '; color:#fff;'
                          : 'background-color:#fff; color:#374151; border:1px solid #e5e7eb;' }}">
                {{ $label }}
            </a>
        @endforeach

    </div>

    <p style="font-size:12px; color:#6b7280;">{{ $total }} task{{ $total === 1 ? '' : 's' }}</p>

    @forelse($tasks as $task)
        @php $statusColour = $statuses[$task->status]['colour'] ?? '#e5e7eb'; @endphp

        <div style="display:flex; align-items:flex-start; gap:12px;">

            {{-- Toggle --}}
            <form method="POST" action="{{ route('tasks.toggle', $task->id) }}" style="margin-top:16px; flex-shrink:0;">
                @csrf
                <button type="submit"
                        style="width:24px; height:24px; border-radius:50%; border:2px solid {{ $statusColour }};
                               background:transparent; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                    <span style="width:10px; height:10px; border-radius:50%; background-color:{{ $statusColour }};"></span>
                </button>
            </form>

            {{-- Card --}}
            <a href="{{ route('tasks.edit', $task->id) }}"
               style="flex:1; background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08);
                      border-left:4px solid {{ $statusColour }}; padding:16px; display:block; text-decoration:none; color:inherit;">

                <p style="font-weight:600; font-size:14px; color:#111827; margin:0;">{{ $task->title }}</p>

                @if($task->description)
                    <p style="font-size:12px; color:#9ca3af; margin:4px 0 0; overflow:hidden;
                               display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                        {{ $task->description }}
                    </p>
                @endif

                @if($task->due_at)
                    <p style="font-size:12px; color:#6b7280; margin:6px 0 0;">
                        📅 {{ \Carbon\Carbon::parse($task->due_at)->format('d M Y') }}
                    </p>
                @endif

            </a>

        </div>
    @empty
        <div style="text-align:center; padding:64px 0; color:#9ca3af;">
            <p style="font-size:18px; font-weight:500; color:#6b7280;">No tasks here</p>
            <a href="{{ route('tasks.create') }}"
               style="display:inline-block; margin-top:16px; background:#6366f1; color:#fff;
                      border-radius:12px; padding:8px 24px; font-size:14px; font-weight:500; text-decoration:none;">
                Create your first task
            </a>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($lastPage > 1)
        <div style="display:flex; justify-content:center; gap:8px; padding:16px 0;">
            @if($page > 1)
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                   style="padding:8px 16px; border-radius:12px; background:#fff; border:1px solid #e5e7eb;
                          font-size:14px; color:#374151; text-decoration:none;">← Prev</a>
            @endif
            <span style="padding:8px 16px; font-size:14px; color:#6b7280;">{{ $page }} / {{ $lastPage }}</span>
            @if($page < $lastPage)
                <a href="{{ route('tasks.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                   style="padding:8px 16px; border-radius:12px; background:#fff; border:1px solid #e5e7eb;
                          font-size:14px; color:#374151; text-decoration:none;">Next →</a>
            @endif
        </div>
    @endif

</div>
@endsection