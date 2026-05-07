@extends('layouts.app')

@section('title', 'Edit Task')
@section('back', true)

@section('content')
<form id="edit-form" method="POST" action="{{ route('tasks.update', $task->id) }}" class="px-4 py-5 space-y-4">
    @csrf
    @method('PUT')

    @include('tasks._form', ['task' => $task, 'statuses' => $statuses])

    <button type="submit"
            style="width:100%; background:#6366f1; color:#fff; font-weight:600; border:none;
                   border-radius:12px; padding:14px; font-size:15px; cursor:pointer;">
        Save Changes
    </button>
</form>

{{-- Delete section --}}
<div style="padding:0 16px 40px;">

    {{-- Confirm panel (hidden by default) --}}
    <div id="confirm-delete" style="display:none; background:#fff1f2; border-radius:16px;
                                     border:1px solid #fecdd3; padding:16px; margin-bottom:12px;">
        <p style="font-weight:600; font-size:14px; color:#be123c; margin:0 0 4px;">Delete this task?</p>
        <p style="font-size:13px; color:#9f1239; margin:0 0 12px;">This cannot be undone.</p>
        <div style="display:flex; gap:8px;">
            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit"
                        style="width:100%; background:#e11d48; color:#fff; font-weight:600; border:none;
                               border-radius:10px; padding:10px; font-size:14px; cursor:pointer;">
                    Yes, delete
                </button>
            </form>
            <button onclick="document.getElementById('confirm-delete').style.display='none';"
                    style="flex:1; background:#fff; color:#374151; font-weight:500; border:1px solid #e5e7eb;
                           border-radius:10px; padding:10px; font-size:14px; cursor:pointer;">
                Cancel
            </button>
        </div>
    </div>

    <button onclick="document.getElementById('confirm-delete').style.display='block';"
            style="width:100%; background:transparent; color:#e11d48; font-weight:500;
                   border:1px solid #fecdd3; border-radius:12px; padding:12px;
                   font-size:14px; cursor:pointer;">
        Delete Task
    </button>

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