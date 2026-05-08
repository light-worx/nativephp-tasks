@extends('layouts.app')

@section('title', $project['name'])
@section('back', true)

@section('header-actions')
    <a href="{{ route('tasks.create', ['project_id' => $project['id']]) }}"
       style="padding:8px; border-radius:50%; display:flex;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
@endsection

@section('content')
<div style="padding:16px 16px 0;">

    <p id="task-count" style="font-size:12px; color:#6b7280; margin:0 0 12px;">&nbsp;</p>

    <div id="task-list">
        @for($i = 0; $i < 3; $i++)
        <div style="margin-bottom:12px;">
            <div style="background:#fff; border-radius:16px; padding:16px; border-left:4px solid #e5e7eb;">
                <div style="height:14px; background:#e5e7eb; border-radius:4px; width:70%; margin-bottom:8px;"></div>
                <div style="height:11px; background:#f3f4f6; border-radius:4px; width:45%;"></div>
            </div>
        </div>
        @endfor
    </div>

</div>

<script>
    var statuses   = @json($statuses);
    var projectId  = {{ $project['id'] }};

    function getColour(status) {
        return statuses[status] ? statuses[status].colour : '#e5e7eb';
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatDate(str) {
        var d = new Date(str);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function loadTasks() {
        fetch('/tasks/data?project_id=' + projectId)
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
                        '<div style="text-align:center; padding:64px 0;">' +
                        '<p style="font-size:16px; font-weight:500; color:#6b7280;">No tasks in this project</p>' +
                        '<a href="/tasks/create?project_id=' + projectId + '" style="display:inline-block; margin-top:16px;' +
                        'background:#6366f1; color:#fff; border-radius:12px; padding:8px 24px; font-size:14px; text-decoration:none;">Add a task</a>' +
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
                        '<div style="margin-bottom:12px;">' +
                        '<a href="/tasks/' + task.id + '/edit"' +
                        ' style="flex:1; background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08);' +
                        ' border-left:4px solid ' + colour + '; padding:16px; display:block; text-decoration:none; color:inherit;">' +
                        '<p style="font-weight:600; font-size:14px; color:#111827; margin:0;">' + escHtml(task.title) + '</p>' +
                        desc + due +
                        '</a>' +
                        '</div>';
                });

                document.getElementById('task-list').innerHTML = html;
            })
            .catch(function() {
                document.getElementById('task-list').innerHTML =
                    '<p style="color:#ef4444; padding:16px;">Could not load tasks.</p>';
            });
    }

    loadTasks();
</script>
@endsection