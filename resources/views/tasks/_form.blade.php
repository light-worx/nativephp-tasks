{{-- Shared form fields used by both Create and Edit views --}}
{{-- Usage: @include('tasks._form', ['task' => $task ?? null]) --}}

@php $task = $task ?? null; @endphp

{{-- Title --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
    <input type="text"
           name="title"
           value="{{ old('title', $task['title'] ?? '') }}"
           required
           placeholder="What needs to be done?"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand @error('title') border-red-400 @enderror">
    @error('title')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Description --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
    <textarea name="description"
              rows="3"
              placeholder="Add more details…"
              class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-brand resize-none">{{ old('description', $task['description'] ?? '') }}</textarea>
</div>

{{-- Priority --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
    <div class="flex gap-3">
        @foreach(['low' => ['🟢', 'Low', 'border-green-400 bg-green-50 text-green-700'],
                  'medium' => ['🟡', 'Medium', 'border-yellow-400 bg-yellow-50 text-yellow-700'],
                  'high' => ['🔴', 'High', 'border-red-400 bg-red-50 text-red-700']] as $val => [$icon, $label, $active])
            @php $selected = old('priority', $task['priority'] ?? 'medium') === $val; @endphp
            <label class="flex-1 cursor-pointer">
                <input type="radio" name="priority" value="{{ $val }}"
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

{{-- Category --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
    <input type="text"
           name="category"
           value="{{ old('category', $task['category'] ?? '') }}"
           placeholder="e.g. Work, Personal…"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand">
</div>

{{-- Due date --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
    <input type="date"
           name="due_date"
           value="{{ old('due_date', isset($task['due_date']) ? \Carbon\Carbon::parse($task['due_date'])->format('Y-m-d') : '') }}"
           class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm
                  focus:outline-none focus:ring-2 focus:ring-brand">
</div>

{{-- Completed toggle (edit only) --}}
@if($task !== null)
<div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 px-4 py-3">
    <span class="text-sm font-medium text-gray-700">Mark as completed</span>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="hidden"   name="completed" value="0">
        <input type="checkbox" name="completed" value="1"
               {{ old('completed', $task['completed'] ?? false) ? 'checked' : '' }}
               class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand rounded-full peer
                    peer-checked:after:translate-x-full peer-checked:bg-brand
                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
    </label>
</div>
@endif
