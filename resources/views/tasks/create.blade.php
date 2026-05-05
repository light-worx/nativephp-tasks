@extends('layouts.app')

@section('title', 'New Task')
@section('back', true)

@section('content')
<form method="POST" action="{{ route('tasks.store') }}" class="px-4 py-5 space-y-4">
    @csrf

    @include('tasks._form')

    <button type="submit"
            class="w-full bg-brand text-white font-semibold rounded-xl py-3.5 shadow
                   active:scale-95 transition-transform">
        Create Task
    </button>
</form>
@endsection
