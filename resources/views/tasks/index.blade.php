@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">All Generated Tasks</h2>
        <a href="/create" class="bg-purple-600 text-white px-4 py-2 rounded">
            + Generate New Task
        </a>
    </div>

    @if($tasks->count() > 0)
        <div class="grid gap-4">
            @foreach($tasks as $task)
            <div class="bg-white p-6 rounded shadow">
                <div class="flex justify-between">
                    <h3 class="font-bold text-lg">{{ $task->title }}</h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded">
                        {{ $task->role }} • {{ $task->level }}
                    </span>
                </div>
                <p class="mt-2 text-gray-600">{{ $task->description }}</p>
                <div class="mt-4">
                    <p><strong>Duration:</strong> {{ $task->duration_hours }} hours</p>
                    <p class="mt-2"><strong>Test:</strong> {{ $task->test_instructions }}</p>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center py-8">No tasks generated yet.</p>
    @endif
</div>
@endsection