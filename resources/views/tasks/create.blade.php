@extends('layouts.app')

@section('title', 'New Task')
@section('back', true)

@section('content')
<form method="POST" action="{{ route('tasks.store') }}" class="px-4 py-5 space-y-4">
    @csrf

    @include('tasks._form', ['task' => null, 'statuses' => $statuses])

    <button type="submit"
            style="width:100%; background:#6366f1; color:#fff; font-weight:600; border:none;
                   border-radius:12px; padding:14px; font-size:15px; cursor:pointer;">
        Create Task
    </button>
</form>

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