@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Task Details
            </h1>

            <p class="text-gray-500 mt-1">
                Complete information about this AI training task.
            </p>

        </div>

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('tasks.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                ← Back
            </a>

            <form
                method="POST"
                action="{{ route('tasks.duplicate', $task) }}">

                @csrf

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    📑 Duplicate
                </button>

            </form>

            <form
                method="POST"
                action="{{ route('tasks.resend', $task) }}">

                @csrf

                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                    📧 Resend Email
                </button>

            </form>

        </div>

    </div>


    {{-- Main Task Card --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">

        {{-- Task Header --}}
        <div class="bg-purple-600 text-white p-6">

            <div class="flex flex-col md:flex-row md:justify-between gap-4">

                <div>

                    <p class="text-purple-200 text-sm">
                        Task #{{ $task->id }}
                    </p>

                    <h2 class="text-2xl font-bold mt-1">
                        {{ $task->title }}
                    </h2>

                </div>

                <div class="flex flex-wrap gap-2">

                    <span class="bg-white/20 px-3 py-1 rounded">
                        {{ $task->role }}
                    </span>

                    <span class="bg-white/20 px-3 py-1 rounded">
                        {{ $task->level }}
                    </span>

                    <span class="bg-white/20 px-3 py-1 rounded">
                        {{ $task->status }}
                    </span>

                </div>

            </div>

        </div>


        {{-- Trainee Information --}}
        <div class="p-6 border-b">

            <h3 class="text-lg font-bold text-gray-800 mb-4">
                👤 Trainee Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>

                    <p class="text-sm text-gray-500">
                        Name
                    </p>

                    <p class="font-semibold text-gray-800">
                        {{ $task->trainee_name }}
                    </p>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Email
                    </p>

                    <p class="font-semibold text-gray-800">
                        {{ $task->trainee_email }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Task Information --}}
        <div class="p-6 border-b">

            <h3 class="text-lg font-bold text-gray-800 mb-4">
                📋 Task Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <div class="bg-blue-50 p-4 rounded">

                    <p class="text-sm text-gray-500">
                        Role
                    </p>

                    <p class="font-bold text-blue-700">
                        {{ $task->role }}
                    </p>

                </div>

                <div class="bg-purple-50 p-4 rounded">

                    <p class="text-sm text-gray-500">
                        Level
                    </p>

                    <p class="font-bold text-purple-700">
                        {{ $task->level }}
                    </p>

                </div>

                <div class="bg-green-50 p-4 rounded">

                    <p class="text-sm text-gray-500">
                        Duration
                    </p>

                    <p class="font-bold text-green-700">
                        {{ $task->duration_hours }} hours
                    </p>

                </div>

            </div>


            <div class="mb-6">

                <h4 class="font-bold text-gray-800 mb-2">
                    Description
                </h4>

                <div class="bg-gray-50 p-4 rounded text-gray-700">
                    {{ $task->description }}
                </div>

            </div>


            <div>

                <h4 class="font-bold text-gray-800 mb-2">
                    Test Instructions
                </h4>

                <div class="bg-gray-50 p-4 rounded text-gray-700">
                    {{ $task->test_instructions }}
                </div>

            </div>

        </div>


        {{-- Evaluation --}}
        <div class="p-6 border-b">

            <h3 class="text-lg font-bold text-gray-800 mb-4">
                📝 Evaluation
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="border rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="font-bold mt-1">
                        {{ $task->status }}
                    </p>

                </div>

                <div class="border rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Score
                    </p>

                    <p class="font-bold text-green-600 mt-1">

                        @if($task->score !== null)

                        {{ $task->score }}/100

                        @else

                        Not scored

                        @endif

                    </p>

                </div>

                <div class="border rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        Completed At
                    </p>

                    <p class="font-bold mt-1">

                        {{ $task->completed_at?->format('d M Y, h:i A') ?? 'Not completed' }}

                    </p>

                </div>

            </div>


            @if($task->review_notes)

            <div class="mt-5">

                <p class="text-sm text-gray-500">
                    Review Notes
                </p>

                <div class="bg-green-50 border border-green-200 p-4 rounded mt-2">
                    {{ $task->review_notes }}
                </div>

            </div>

            @endif

        </div>


        {{-- Metadata --}}
        <div class="p-6 bg-gray-50">

            <h3 class="font-bold text-gray-800 mb-3">
                System Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>

                    <span class="text-gray-500">
                        Created:
                    </span>

                    <span class="font-medium">
                        {{ $task->created_at?->format('d M Y, h:i A') }}
                    </span>

                </div>

                <div>

                    <span class="text-gray-500">
                        Last Updated:
                    </span>

                    <span class="font-medium">
                        {{ $task->updated_at?->format('d M Y, h:i A') }}
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection