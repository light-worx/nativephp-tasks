@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div style="padding:16px;">

    @if(empty($projects))
        <div style="text-align:center; padding:64px 0; color:#9ca3af;">
            <p style="font-size:18px; font-weight:500; color:#6b7280;">No projects found</p>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($projects as $project)
                <a href="{{ route('projects.tasks', $project['id']) }}"
                   style="background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,0.08);
                          border-left:4px solid #6366f1; padding:16px; display:block;
                          text-decoration:none; color:inherit;">

                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <p style="font-weight:600; font-size:15px; color:#111827; margin:0;">
                            {{ $project['name'] }}
                        </p>
                        <span style="background:#ede9fe; color:#6366f1; font-size:12px; font-weight:600;
                                     border-radius:999px; padding:2px 10px; flex-shrink:0; margin-left:8px;">
                            {{ $project['task_count'] ?? 0 }} task{{ ($project['task_count'] ?? 0) === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if(!empty($project['description']))
                        <p style="font-size:13px; color:#9ca3af; margin:4px 0 0;">
                            {{ $project['description'] }}
                        </p>
                    @endif

                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection