@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                📊 Training Performance Dashboard
            </h1>

            <p class="text-gray-500 mt-2">
                Monitor task generation, trainee progress and evaluation performance.
            </p>

        </div>

        <div class="flex gap-3">

            <a
                href="{{ route('tasks.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded"
            >
                Task History
            </a>

            <a
                href="{{ route('tasks.create') }}"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
            >
                + Generate Task
            </a>

        </div>

    </div>


    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">

        {{-- Total --}}
        <div class="bg-white rounded-lg shadow p-5">

            <p class="text-sm text-gray-500">
                Total Tasks
            </p>

            <p class="text-3xl font-bold text-gray-800 mt-2">
                {{ $totalTasks }}
            </p>

            <p class="text-sm text-gray-400 mt-2">
                All generated tasks
            </p>

        </div>


        {{-- Pending --}}
        <div class="bg-white rounded-lg shadow p-5">

            <p class="text-sm text-gray-500">
                Pending
            </p>

            <p class="text-3xl font-bold text-yellow-600 mt-2">
                {{ $pendingTasks }}
            </p>

            <p class="text-sm text-gray-400 mt-2">
                Awaiting work
            </p>

        </div>


        {{-- In Progress --}}
        <div class="bg-white rounded-lg shadow p-5">

            <p class="text-sm text-gray-500">
                In Progress
            </p>

            <p class="text-3xl font-bold text-blue-600 mt-2">
                {{ $inProgressTasks }}
            </p>

            <p class="text-sm text-gray-400 mt-2">
                Currently active
            </p>

        </div>


        {{-- Completed --}}
        <div class="bg-white rounded-lg shadow p-5">

            <p class="text-sm text-gray-500">
                Completed
            </p>

            <p class="text-3xl font-bold text-green-600 mt-2">
                {{ $completedTasks }}
            </p>

            <p class="text-sm text-gray-400 mt-2">
                Work completed
            </p>

        </div>


        {{-- Reviewed --}}
        <div class="bg-white rounded-lg shadow p-5">

            <p class="text-sm text-gray-500">
                Reviewed
            </p>

            <p class="text-3xl font-bold text-indigo-600 mt-2">
                {{ $reviewedTasks }}
            </p>

            <p class="text-sm text-gray-400 mt-2">
                Evaluated tasks
            </p>

        </div>

    </div>


    {{-- Performance Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Completion Rate --}}
        <div class="bg-white rounded-lg shadow p-6">

            <div class="flex justify-between items-center mb-4">

                <div>

                    <h2 class="text-lg font-bold text-gray-800">
                        Completion Rate
                    </h2>

                    <p class="text-sm text-gray-500">
                        Completed and reviewed tasks
                    </p>

                </div>

                <span class="text-2xl font-bold text-purple-600">
                    {{ $completionRate }}%
                </span>

            </div>


            <div class="w-full bg-gray-200 rounded-full h-4">

                <div
                    class="bg-purple-600 h-4 rounded-full transition-all"
                    style="width: {{ min($completionRate, 100) }}%"
                ></div>

            </div>

        </div>


        {{-- Average Score --}}
        <div class="bg-white rounded-lg shadow p-6">

            <div class="flex justify-between items-center mb-4">

                <div>

                    <h2 class="text-lg font-bold text-gray-800">
                        Average Evaluation Score
                    </h2>

                    <p class="text-sm text-gray-500">
                        Average score across evaluated tasks
                    </p>

                </div>

                <span class="text-2xl font-bold text-green-600">
                    {{ $averageScore }}/100
                </span>

            </div>


            <div class="w-full bg-gray-200 rounded-full h-4">

                <div
                    class="bg-green-500 h-4 rounded-full transition-all"
                    style="width: {{ min($averageScore, 100) }}%"
                ></div>

            </div>

        </div>

    </div>


    {{-- Role and Level Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Tasks by Role --}}
        <div class="bg-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">
                Tasks by Role
            </h2>

            @if($roleStats->count() > 0)

                @foreach($roleStats as $role)

                    @php
                        $percentage = $totalTasks > 0
                            ? round(($role->total / $totalTasks) * 100, 1)
                            : 0;
                    @endphp

                    <div class="mb-5">

                        <div class="flex justify-between mb-2">

                            <span class="font-medium text-gray-700">
                                {{ $role->role }}
                            </span>

                            <span class="text-sm text-gray-500">
                                {{ $role->total }} tasks
                                ({{ $percentage }}%)
                            </span>

                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-3">

                            <div
                                class="bg-blue-500 h-3 rounded-full"
                                style="width: {{ $percentage }}%"
                            ></div>

                        </div>

                    </div>

                @endforeach

            @else

                <p class="text-gray-500">
                    No task data available.
                </p>

            @endif

        </div>


        {{-- Tasks by Level --}}
        <div class="bg-white rounded-lg shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">
                Tasks by Experience Level
            </h2>

            @if($levelStats->count() > 0)

                @foreach($levelStats as $level)

                    @php
                        $percentage = $totalTasks > 0
                            ? round(($level->total / $totalTasks) * 100, 1)
                            : 0;
                    @endphp

                    <div class="mb-5">

                        <div class="flex justify-between mb-2">

                            <span class="font-medium text-gray-700">
                                {{ $level->level }}
                            </span>

                            <span class="text-sm text-gray-500">
                                {{ $level->total }} tasks
                                ({{ $percentage }}%)
                            </span>

                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-3">

                            <div
                                class="bg-purple-500 h-3 rounded-full"
                                style="width: {{ $percentage }}%"
                            ></div>

                        </div>

                    </div>

                @endforeach

            @else

                <p class="text-gray-500">
                    No task data available.
                </p>

            @endif

        </div>

    </div>


    {{-- Status Distribution --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">

        <h2 class="text-lg font-bold text-gray-800 mb-5">
            Task Status Distribution
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            @foreach($statusStats as $status)

                <div class="border rounded-lg p-4">

                    <p class="text-sm text-gray-500">
                        {{ $status->status }}
                    </p>

                    <p class="text-2xl font-bold text-gray-800 mt-2">
                        {{ $status->total }}
                    </p>

                    <p class="text-sm text-gray-400">
                        tasks
                    </p>

                </div>

            @endforeach

        </div>

    </div>


    {{-- Recent Evaluated Tasks --}}
    <div class="bg-white rounded-lg shadow mb-8">

        <div class="p-6 border-b">

            <h2 class="text-lg font-bold text-gray-800">
                📝 Recent Evaluated Tasks
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Recently completed and reviewed assignments.
            </p>

        </div>


        @if($recentReviewedTasks->count() > 0)

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Trainee
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Task
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Role
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Level
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Status
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Score
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @foreach($recentReviewedTasks as $task)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">

                                    <p class="font-medium text-gray-800">
                                        {{ $task->trainee_name }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        {{ $task->trainee_email }}
                                    </p>

                                </td>

                                <td class="px-6 py-4">

                                    <p class="font-medium">
                                        {{ $task->title }}
                                    </p>

                                    @if($task->completed_at)

                                        <p class="text-sm text-gray-500">
                                            {{ $task->completed_at->format('d M Y, h:i A') }}
                                        </p>

                                    @endif

                                </td>

                                <td class="px-6 py-4">
                                    {{ $task->role }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $task->level }}
                                </td>

                                <td class="px-6 py-4">

                                    @if($task->status === 'Completed')

                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm">
                                            Completed
                                        </span>

                                    @else

                                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded text-sm">
                                            Reviewed
                                        </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4">

                                    @if($task->score !== null)

                                        <span class="font-bold">
                                            {{ $task->score }}/100
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="p-8 text-center">

                <p class="text-gray-500">
                    No evaluated tasks yet.
                </p>

                <a
                    href="{{ route('tasks.index') }}"
                    class="inline-block mt-3 text-purple-600 hover:underline"
                >
                    Evaluate your first task →
                </a>

            </div>

        @endif

    </div>


    {{-- Top Performing Trainees --}}
    <div class="bg-white rounded-lg shadow">

        <div class="p-6 border-b">

            <h2 class="text-lg font-bold text-gray-800">
                🏆 Top Performing Trainees
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Highest average scores among reviewed trainees.
            </p>

        </div>


        @if($topTrainees->count() > 0)

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Rank
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Trainee
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Email
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Reviewed Tasks
                            </th>

                            <th class="text-left px-6 py-3 text-sm font-medium text-gray-500">
                                Average Score
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @foreach($topTrainees as $index => $trainee)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">

                                    @if($index === 0)

                                        🥇

                                    @elseif($index === 1)

                                        🥈

                                    @elseif($index === 2)

                                        🥉

                                    @else

                                        {{ $index + 1 }}

                                    @endif

                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ $trainee->trainee_name }}
                                </td>

                                <td class="px-6 py-4 text-gray-500">
                                    {{ $trainee->trainee_email }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $trainee->reviewed_tasks }}
                                </td>

                                <td class="px-6 py-4">

                                    <span class="font-bold text-green-600">
                                        {{ round($trainee->average_score, 1) }}/100
                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="p-8 text-center">

                <p class="text-gray-500">
                    No reviewed trainee scores available yet.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection