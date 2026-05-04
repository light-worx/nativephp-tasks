{{-- Shared form fields. Usage: @include('tasks._form', ['task' => $task ?? null]) --}}
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

{{-- Assigned email --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
    <input type="email"
           name="assigned_email"
           value="{{ old('assigned_email', $task?->assigned_email ?? '') }}"
           placeholder="user@example.com"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand @error('assigned_email') border-red-400 @enderror">
    @error('assigned_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>

{{-- Status --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
    <div class="flex gap-3">
        @foreach(['pending' => ['⏳', 'Pending', 'border-yellow-400 bg-yellow-50 text-yellow-700'],
                  'completed' => ['✅', 'Completed', 'border-green-400 bg-green-50 text-green-700']] as $val => [$icon, $label, $active])
            @php $selected = old('status', $task?->status ?? 'pending') === $val; @endphp
            <label class="flex-1 cursor-pointer">
                <input type="radio" name="status" value="{{ $val }}"
                       {{ $selected ? 'checked' : '' }} class="sr-only">
                <div class="flex flex-col items-center justify-center rounded-xl border-2 py-2.5
                            {{ $selected ? $active : 'border-gray-200 bg-white text-gray-500' }}
                            transition-colors">
                    <span class="text-lg">{{ $icon }}</span>
                    <span class="text-xs font-medium mt-0.5">{{ $label }}</span>
                </div>
            </label>
        @endforeach
    </div>
</div>

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
