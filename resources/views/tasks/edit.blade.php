@extends('layouts.app')

@section('title', 'Edit Task')
@section('back', true)

@section('content')
<form id="edit-form" method="POST" action="{{ route('tasks.update', $task->id) }}" class="px-4 py-5 space-y-4">
    @csrf
    @method('PUT')

    @include('tasks._form', ['task' => $task, 'statuses' => $statuses])

    <button type="submit"
            class="w-full bg-brand text-white font-semibold rounded-xl py-3.5 shadow
                   active:scale-95 transition-transform">
        Save Changes
    </button>
</form>

<div class="px-4 pb-8">
    <form method="POST" action="{{ route('tasks.destroy', $task->id) }}"
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

<script>
document.querySelectorAll('.status-label').forEach(function(label) {
    label.addEventListener('click', function() {
        const color = this.dataset.color;
        document.querySelectorAll('.status-label').forEach(function(l) {
            const c = l.dataset.color;
            l.querySelector('.status-pill').style.backgroundColor = 'transparent';
            l.querySelector('.status-pill').style.borderColor = c;
            l.querySelector('.status-pill').style.color = c;
        });
        this.querySelector('.status-pill').style.backgroundColor = color;
        this.querySelector('.status-pill').style.borderColor = color;
        this.querySelector('.status-pill').style.color = '#fff';
    });
});
</script>
@endsection