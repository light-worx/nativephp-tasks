{{-- Usage: @include('tasks._form', ['task' => $task ?? null, 'statuses' => $statuses]) --}}
@php $task = $task ?? null; @endphp

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
                $selected = old('status', $task?->status ?? array_key_first($statuses)) === $id;
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

{{-- Project ID --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Project ID</label>
    <input type="text"
           name="project_id"
           value="{{ old('project_id', $task?->project_id ?? '') }}"
           placeholder="Optional project identifier"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand">
</div>

{{-- Due date --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
    <input type="date"
           name="due_at"
           value="{{ old('due_at', $task?->due_at ? \Carbon\Carbon::parse($task->due_at)->format('Y-m-d') : '') }}"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand">
</div>