@extends('layouts.app')

@section('title', 'My Tasks')

@section('header-actions')
    <a href="{{ route('tasks.create') }}" style="padding:8px; border-radius:50%; display:flex;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
@endsection

@section('content')
<div style="padding:16px 16px 0;">

    {{-- Filter tabs --}}
    <div id="filter-tabs" style="display:flex; gap:8px; overflow-x:auto; padding-bottom:4px;">
        <a href="{{ route('tasks.index', ['filter' => 'all']) }}"
           style="flex-shrink:0; padding:6px 16px; border-radius:999px; font-size:14px; font-weight:500;
                  text-decoration:none;
                  {{ $filter === 'all' ? 'background:#6366f1; color:#fff;' : 'background:#fff; color:#374151; border:1px solid #e5e7eb;' }}">
            All
        </a>
        @foreach($statuses as $label => $status)
            <a href="{{ route('tasks.index', ['filter' => $label]) }}"
            style="flex-shrink:0; padding:6px 16px; border-radius:999px; font-size:14px; font-weight:500;
                    text-decoration:none;
                    {{ $filter === $label
                        ? 'background-color:' . $status['colour'] . '; color:#fff; border:2px solid ' . $status['colour'] . ';'
                        : 'background:#fff; color:#374151; border:2px solid ' . $status['colour'] . ';' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <p id="task-count" style="font-size:12px; color:#6b7280; margin:8px 0 4px;">&nbsp;</p>

    {{-- Task list - rendered by JS --}}
    <div id="task-list">
        {{-- Skeleton --}}
        @for($i = 0; $i < 5; $i++)
            <div style="margin-bottom:12px;">
                <div style="flex:1; background:#fff; border-radius:16px; padding:16px; border-left:4px solid #e5e7eb;">
                    <div style="height:14px; background:#e5e7eb; border-radius:4px; width:70%; margin-bottom:8px;"></div>
                    <div style="height:11px; background:#f3f4f6; border-radius:4px; width:45%;"></div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Pagination --}}
    <div id="pagination" style="display:flex; justify-content:center; gap:8px; padding:16px 0;"></div>

</div>

<script>
    var currentFilter = '{{ $filter }}';
    var currentPage   = {{ $page }};
    var statuses      = @json($statuses);

    function getColour(status) {
        return statuses[status] ? statuses[status].colour : '#e5e7eb';
    }

    function loadTasks() {
        fetch('/tasks/data?filter=' + encodeURIComponent(currentFilter) + '&page=' + currentPage)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) {
                    document.getElementById('task-list').innerHTML =
                        '<p style="color:#ef4444; padding:16px;">' + data.error + '</p>';
                    return;
                }

                document.getElementById('task-count').textContent =
                    data.total + ' task' + (data.total === 1 ? '' : 's');

                if (data.tasks.length === 0) {
                    document.getElementById('task-list').innerHTML =
                        '<div style="text-align:center; padding:64px 0; color:#9ca3af;">' +
                        '<p style="font-size:18px; font-weight:500; color:#6b7280;">No tasks here</p>' +
                        '<a href="/tasks/create" style="display:inline-block; margin-top:16px; background:#6366f1; color:#fff;' +
                        'border-radius:12px; padding:8px 24px; font-size:14px; font-weight:500; text-decoration:none;">Create your first task</a>' +
                        '</div>';
                    return;
                }

                var html = '';
                data.tasks.forEach(function(task) {
                    var colour = getColour(task.status);
                    var due = task.due_at
                        ? '<p style="font-size:12px; color:#6b7280; margin:6px 0 0;">📅 ' + formatDate(task.due_at) + '</p>'
                        : '';
                    var desc = task.description
                        ? '<p style="font-size:12px; color:#9ca3af; margin:4px 0 0; overflow:hidden;' +
                          'display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">' +
                          escHtml(task.description) + '</p>'
                        : '';

                    html +=
                        '<div style="display:flex; align-items:flex-start; margin-bottom:12px;">' +

                        // Card only - no toggle button
                        '<a href="/tasks/' + task.id + '/edit"' +
                        ' style="flex:1; background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08);' +
                        ' border-left:4px solid ' + colour + '; padding:16px; display:block; text-decoration:none; color:inherit;">' +
                        '<p style="font-weight:600; font-size:14px; color:#111827; margin:0;">' + escHtml(task.title) + '</p>' +
                        desc + due +
                        '</a>' +
                        '</div>';
                });

                document.getElementById('task-list').innerHTML = html;

                // Pagination
                var pag = '';
                if (data.lastPage > 1) {
                    if (currentPage > 1) {
                        pag += '<a href="/?filter=' + currentFilter + '&page=' + (currentPage - 1) +
                               '" style="padding:8px 16px; border-radius:12px; background:#fff; border:1px solid #e5e7eb; font-size:14px; color:#374151; text-decoration:none;">← Prev</a>';
                    }
                    pag += '<span style="padding:8px 16px; font-size:14px; color:#6b7280;">' + currentPage + ' / ' + data.lastPage + '</span>';
                    if (currentPage < data.lastPage) {
                        pag += '<a href="/?filter=' + currentFilter + '&page=' + (currentPage + 1) +
                               '" style="padding:8px 16px; border-radius:12px; background:#fff; border:1px solid #e5e7eb; font-size:14px; color:#374151; text-decoration:none;">Next →</a>';
                    }
                }
                document.getElementById('pagination').innerHTML = pag;
            })
            .catch(function(err) {
                document.getElementById('task-list').innerHTML =
                    '<p style="color:#ef4444; padding:16px;">Could not load tasks.</p>';
            });
    }

    function formatDate(str) {
        var d = new Date(str);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Load tasks as soon as page is visible
    loadTasks();
</script>
@endsection