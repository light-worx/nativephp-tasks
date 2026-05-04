@extends('layouts.app')

@section('title', 'Edit Task')
@section('back', true)

@section('content')
<form method="POST" action="{{ route('tasks.update', $task['id']) }}" class="px-4 py-5 space-y-4">
    @csrf
    @method('PUT')

    @include('tasks._form', ['task' => $task])

    <button type="submit"
            class="w-full bg-brand text-white font-semibold rounded-xl py-3.5 shadow
                   active:scale-95 transition-transform">
        Save Changes
    </button>
</form>

{{-- Delete --}}
<div class="px-4 pb-8">
    <form method="POST" action="{{ route('tasks.destroy', $task['id']) }}"
          onsubmit="return confirm('Delete this task?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="w-full border border-red-300 text-red-500 font-medium rounded-xl py-3 text-sm
                       active:scale-95 transition-transform">
            Delete Task
        </button>
    </form>
</div>
@endsection
