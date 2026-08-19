@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <div>

            <h2 class="text-2xl font-bold">
                AI Training Task History
            </h2>

            <p class="text-gray-500 mt-1">
                Search, filter, evaluate and manage generated training tasks.
            </p>

        </div>

        <div class="flex flex-wrap gap-2">

            {{-- Export CSV --}}
            <a
                href="{{ route('tasks.export', request()->query()) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                📥 Export CSV
            </a>

            {{-- Generate Task --}}
            <a
                href="{{ route('tasks.create') }}"
                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                + Generate New Task
            </a>

        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">

        {{ session('success') }}

    </div>

    @endif


    {{-- Validation Errors --}}
    @if($errors->any())

    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">

        <ul class="list-disc list-inside">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- Search & Filters --}}
    <div class="bg-white p-6 rounded-lg shadow mb-6">

        <h3 class="font-bold text-lg mb-4">
            🔎 Search & Filter Tasks
        </h3>

        <form
            method="GET"
            action="{{ route('tasks.index') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

                {{-- Search --}}
                <div class="lg:col-span-2">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Trainee, email, title..."
                        class="w-full px-4 py-2 border rounded">

                </div>


                {{-- Role --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Role
                    </label>

                    <select
                        name="role"
                        class="w-full px-3 py-2 border rounded">

                        <option value="">
                            All Roles
                        </option>

                        <option
                            value="Laravel"
                            {{ request('role') === 'Laravel' ? 'selected' : '' }}>
                            Laravel
                        </option>

                        <option
                            value="PHP"
                            {{ request('role') === 'PHP' ? 'selected' : '' }}>
                            PHP
                        </option>

                        <option
                            value="Frontend"
                            {{ request('role') === 'Frontend' ? 'selected' : '' }}>
                            Frontend
                        </option>

                    </select>

                </div>


                {{-- Level --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Level
                    </label>

                    <select
                        name="level"
                        class="w-full px-3 py-2 border rounded">

                        <option value="">
                            All Levels
                        </option>

                        <option
                            value="Beginner"
                            {{ request('level') === 'Beginner' ? 'selected' : '' }}>
                            Beginner
                        </option>

                        <option
                            value="Intermediate"
                            {{ request('level') === 'Intermediate' ? 'selected' : '' }}>
                            Intermediate
                        </option>

                        <option
                            value="Advanced"
                            {{ request('level') === 'Advanced' ? 'selected' : '' }}>
                            Advanced
                        </option>

                    </select>

                </div>


                {{-- Duration --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Duration
                    </label>

                    <select
                        name="duration"
                        class="w-full px-3 py-2 border rounded">

                        <option value="">
                            All Durations
                        </option>

                        <option
                            value="4"
                            {{ request('duration') == '4' ? 'selected' : '' }}>
                            4 Hours
                        </option>

                        <option
                            value="8"
                            {{ request('duration') == '8' ? 'selected' : '' }}>
                            8 Hours
                        </option>

                        <option
                            value="16"
                            {{ request('duration') == '16' ? 'selected' : '' }}>
                            16 Hours
                        </option>

                    </select>

                </div>


                {{-- Status --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full px-3 py-2 border rounded">

                        <option value="">
                            All Statuses
                        </option>

                        <option
                            value="Pending"
                            {{ request('status') === 'Pending' ? 'selected' : '' }}>
                            Pending
                        </option>

                        <option
                            value="In Progress"
                            {{ request('status') === 'In Progress' ? 'selected' : '' }}>
                            In Progress
                        </option>

                        <option
                            value="Completed"
                            {{ request('status') === 'Completed' ? 'selected' : '' }}>
                            Completed
                        </option>

                        <option
                            value="Reviewed"
                            {{ request('status') === 'Reviewed' ? 'selected' : '' }}>
                            Reviewed
                        </option>

                    </select>

                </div>

            </div>


            {{-- Filter Buttons --}}
            <div class="flex flex-wrap gap-3 mt-5">

                <button
                    type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded">
                    🔎 Apply Filters
                </button>

                <a
                    href="{{ route('tasks.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded">
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- Result Count & Sort --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">

        <p class="text-gray-600">

            Showing

            <strong>
                {{ $tasks->firstItem() ?? 0 }}
            </strong>

            -

            <strong>
                {{ $tasks->lastItem() ?? 0 }}
            </strong>

            of

            <strong>
                {{ $tasks->total() }}
            </strong>

            tasks

        </p>


        {{-- Sorting --}}
        <form
            method="GET"
            action="{{ route('tasks.index') }}">

            <input
                type="hidden"
                name="search"
                value="{{ request('search') }}">

            <input
                type="hidden"
                name="role"
                value="{{ request('role') }}">

            <input
                type="hidden"
                name="level"
                value="{{ request('level') }}">

            <input
                type="hidden"
                name="duration"
                value="{{ request('duration') }}">

            <input
                type="hidden"
                name="status"
                value="{{ request('status') }}">

            <select
                name="sort"
                onchange="this.form.submit()"
                class="px-3 py-2 border rounded">

                <option
                    value="newest"
                    {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                    Newest First
                </option>

                <option
                    value="oldest"
                    {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                    Oldest First
                </option>

            </select>

        </form>

    </div>


    {{-- Tasks --}}
    @if($tasks->count() > 0)

    <div class="grid gap-6">

        @foreach($tasks as $task)

        <div class="bg-white rounded-lg shadow overflow-hidden">

            {{-- Task Header --}}
            <div class="p-6 border-b">

                <div class="flex flex-col md:flex-row md:justify-between gap-4">

                    <div>

                        <h3 class="font-bold text-xl">
                            {{ $task->title }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">

                            Task #{{ $task->id }}

                            <span class="mx-1">
                                •
                            </span>

                            Generated
                            {{ $task->created_at->format('d M Y, h:i A') }}

                        </p>

                    </div>


                    <div class="flex flex-wrap gap-2 items-center">

                        {{-- Role --}}
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm">
                            {{ $task->role }}
                        </span>

                        {{-- Level --}}
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded text-sm">
                            {{ $task->level }}
                        </span>


                        {{-- Status --}}
                        @if($task->status === 'Pending')

                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm">
                            Pending
                        </span>

                        @elseif($task->status === 'In Progress')

                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm">
                            In Progress
                        </span>

                        @elseif($task->status === 'Completed')

                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm">
                            Completed
                        </span>

                        @else

                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded text-sm">
                            Reviewed
                        </span>

                        @endif

                    </div>

                </div>

            </div>


            {{-- Trainee Information --}}
            <div class="p-6 bg-gray-50 border-b">

                <h4 class="font-bold mb-3">
                    👤 Trainee Information
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <span class="text-sm text-gray-500">
                            Name
                        </span>

                        <p class="font-medium">
                            {{ $task->trainee_name }}
                        </p>

                    </div>

                    <div>

                        <span class="text-sm text-gray-500">
                            Email
                        </span>

                        <p class="font-medium break-all">
                            {{ $task->trainee_email }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- Task Details --}}
            <div class="p-6">

                <div class="mb-5">

                    <h4 class="font-bold mb-2">
                        Task Description
                    </h4>

                    <p class="text-gray-600">
                        {{ $task->description }}
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">

                    <div class="bg-gray-50 p-4 rounded">

                        <p class="text-sm text-gray-500">
                            Duration
                        </p>

                        <p class="font-bold">
                            {{ $task->duration_hours }} hours
                        </p>

                    </div>


                    <div class="bg-gray-50 p-4 rounded">

                        <p class="text-sm text-gray-500">
                            Test Instructions
                        </p>

                        <p class="font-medium">
                            {{ $task->test_instructions }}
                        </p>

                    </div>

                </div>


                {{-- Existing Evaluation --}}
                @if(
                $task->status === 'Completed' ||
                $task->status === 'Reviewed'
                )

                <div class="bg-green-50 border border-green-200 p-4 rounded mb-5">

                    <h4 class="font-bold text-green-800 mb-3">
                        Evaluation Summary
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>

                            <p class="text-sm text-gray-500">
                                Status
                            </p>

                            <p class="font-bold">
                                {{ $task->status }}
                            </p>

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Score
                            </p>

                            <p class="font-bold">

                                @if($task->score !== null)

                                {{ $task->score }}/100

                                @else

                                Not scored

                                @endif

                            </p>

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Completed
                            </p>

                            <p class="font-bold">

                                {{ $task->completed_at?->format('d M Y, h:i A') ?? 'Not available' }}

                            </p>

                        </div>

                    </div>


                    @if($task->review_notes)

                    <div class="mt-4">

                        <p class="text-sm text-gray-500">
                            Review Notes
                        </p>

                        <p class="mt-1 text-gray-700">
                            {{ $task->review_notes }}
                        </p>

                    </div>

                    @endif

                </div>

                @endif


                {{-- Management Actions --}}
                <div class="border-t pt-5 mb-5">

                    <h4 class="font-bold text-lg mb-4">
                        ⚙️ Task Actions
                    </h4>

                    <div class="flex flex-wrap gap-2">

                        {{-- View --}}
                        <a
                            href="{{ route('tasks.show', $task) }}"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
                            👁 View Details
                        </a>


                        {{-- Resend Email --}}
                        <form
                            method="POST"
                            action="{{ route('tasks.resend', $task) }}">

                            @csrf

                            <button
                                type="submit"
                                onclick="return confirm('Resend this task email to {{ $task->trainee_email }}?')"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                                📧 Resend Email
                            </button>

                        </form>


                        {{-- Duplicate --}}
                        <form
                            method="POST"
                            action="{{ route('tasks.duplicate', $task) }}">

                            @csrf

                            <button
                                type="submit"
                                onclick="return confirm('Duplicate this task?')"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                📑 Duplicate
                            </button>

                        </form>

                    </div>

                </div>


                {{-- Evaluation Form --}}
                <div class="border-t pt-5">

                    <h4 class="font-bold text-lg mb-4">
                        📝 Task Evaluation
                    </h4>

                    <form
                        method="POST"
                        action="{{ route('tasks.evaluation.update', $task) }}">

                        @csrf

                        @method('PATCH')


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Status --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Task Status
                                </label>

                                <select
                                    name="status"
                                    required
                                    class="w-full px-3 py-2 border rounded">

                                    <option
                                        value="Pending"
                                        {{ $task->status === 'Pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option
                                        value="In Progress"
                                        {{ $task->status === 'In Progress' ? 'selected' : '' }}>
                                        In Progress
                                    </option>

                                    <option
                                        value="Completed"
                                        {{ $task->status === 'Completed' ? 'selected' : '' }}>
                                        Completed
                                    </option>

                                    <option
                                        value="Reviewed"
                                        {{ $task->status === 'Reviewed' ? 'selected' : '' }}>
                                        Reviewed
                                    </option>

                                </select>

                            </div>


                            {{-- Score --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Score
                                </label>

                                <input
                                    type="number"
                                    name="score"
                                    min="0"
                                    max="100"
                                    value="{{ old('score', $task->score) }}"
                                    placeholder="0 - 100"
                                    class="w-full px-3 py-2 border rounded">

                            </div>

                        </div>


                        {{-- Review Notes --}}
                        <div class="mt-4">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Review Notes
                            </label>

                            <textarea
                                name="review_notes"
                                rows="3"
                                maxlength="5000"
                                placeholder="Enter trainer feedback or evaluation notes..."
                                class="w-full px-3 py-2 border rounded">{{ old('review_notes', $task->review_notes) }}</textarea>

                        </div>


                        <button
                            type="submit"
                            class="mt-4 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded">
                            💾 Save Evaluation
                        </button>

                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>


    {{-- Pagination --}}
    <div class="mt-6">

        {{ $tasks->links() }}

    </div>

    @else

    {{-- Empty State --}}
    <div class="bg-white rounded-lg shadow p-10 text-center">

        <div class="text-5xl mb-4">
            🔍
        </div>

        <h3 class="text-lg font-bold">
            No matching tasks found
        </h3>

        <p class="text-gray-500 mt-2">
            Try changing your search or filter criteria.
        </p>

        <a
            href="{{ route('tasks.index') }}"
            class="inline-block mt-4 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded">
            Clear Filters
        </a>

    </div>

    @endif

</div>

@endsection