{{-- Usage: @include('tasks._form', ['task' => $task ?? null, 'statuses' => $statuses]) --}}
@php 
    $task = $task ?? null;
    $preselectedProject = $preselectedProject ?? null;
@endphp

{{-- Title --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Title <span class="text-red-500">*</span>
    </label>
    <input type="text"
           name="title"
           value="{{ old('title', $task?->title ?? '') }}"
           required
           placeholder="What needs to be done?"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand @error('title') border-red-400 @enderror">
    @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

{{-- Description --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <textarea name="description"
              rows="3"
              placeholder="Add more details…"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-brand resize-none">{{ old('description', $task?->description ?? '') }}</textarea>
</div>

{{-- Status — dynamic from API --}}
@if(!empty($statuses))
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
    <div class="flex flex-wrap gap-2" id="status-options">
        @foreach($statuses as $id => $status)
            @php
                $currentStatus = old('status', $task?->status ?? array_key_first($statuses ?? []));
                $selected = $currentStatus === $id;
                $color    = $status['colour'] ?? '#6366f1';
            @endphp
            <label class="cursor-pointer status-label" data-color="{{ $color }}">
                <input type="radio" name="status" value="{{ $id }}"
                       {{ $selected ? 'checked' : '' }} class="sr-only status-radio">
                <div class="px-4 py-2 rounded-full border-2 text-sm font-medium transition-all status-pill"
                     style="{{ $selected
                         ? "background-color:{$color}; border-color:{$color}; color:#fff;"
                         : "background-color:transparent; border-color:{$color}; color:{$color};" }}">
                    {{ $status['label'] }}
                </div>
            </label>
        @endforeach
    </div>
</div>
@endif

{{-- Project --}}
@if(!empty($projects))
<div>
    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
        Project
    </label>
    @php
        $selectedProject = old('project_id', $task?->project_id ?? $preselectedProject ?? '');
    @endphp
    <select name="project_id"
            style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
                   font-size:14px; background:#fff; box-sizing:border-box; color:#374151;
                   appearance:none; -webkit-appearance:none;
                   background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%236b7280%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M6 8l4 4 4-4%22/></svg>');
                   background-repeat:no-repeat; background-position:right 12px center; background-size:20px;
                   padding-right:40px;">
        <option value="">— No project —</option>
        @foreach($projects as $projectId => $projectName)
            <option value="{{ $projectId }}"
                    {{ (string) $selectedProject === (string) $projectId ? 'selected' : '' }}>
                {{ $projectName }}
            </option>
        @endforeach
    </select>
</div>
@elseif(!empty($task?->project_id))
{{-- Fallback if projects failed to load but task has one set --}}
<div>
    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
        Project ID
    </label>
    <input type="text"
           name="project_id"
           value="{{ old('project_id', $task?->project_id ?? '') }}"
           style="width:100%; border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px;
                  font-size:14px; background:#f9fafb; box-sizing:border-box;">
</div>
@endif

{{-- Due date --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
    <input type="date"
           name="due_at"
           value="{{ old('due_at', $task?->due_at ? \Carbon\Carbon::parse($task->due_at)->format('Y-m-d') : '') }}"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand">
</div>