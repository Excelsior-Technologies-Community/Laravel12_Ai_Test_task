@extends('layouts.app')

@section('content')

<div class="max-w-md mx-auto">

    <div class="bg-white p-8 rounded-lg shadow">

        <h2 class="text-2xl font-bold mb-2">
            Generate AI Task
        </h2>

        <p class="text-gray-500 mb-6">
            Create a role-based training task and send it directly to the trainee.
        </p>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.generate') }}">
            @csrf

            {{-- Trainee Name --}}
            <div class="mb-4">

                <label class="block text-gray-700 mb-2 font-medium">
                    Trainee Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-500"
                    placeholder="Enter trainee name"
                >

            </div>

            {{-- Email --}}
            <div class="mb-4">

                <label class="block text-gray-700 mb-2 font-medium">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-500"
                    placeholder="Enter trainee email"
                >

            </div>

            {{-- Role --}}
            <div class="mb-4">

                <label class="block text-gray-700 mb-2 font-medium">
                    Select Role
                </label>

                <select
                    name="role"
                    required
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-500"
                >

                    <option value="">
                        Choose Role
                    </option>

                    <option
                        value="Laravel"
                        {{ old('role') === 'Laravel' ? 'selected' : '' }}
                    >
                        Laravel Developer
                    </option>

                    <option
                        value="PHP"
                        {{ old('role') === 'PHP' ? 'selected' : '' }}
                    >
                        PHP Developer
                    </option>

                    <option
                        value="Frontend"
                        {{ old('role') === 'Frontend' ? 'selected' : '' }}
                    >
                        Frontend Developer
                    </option>

                </select>

            </div>

            {{-- Level --}}
            <div class="mb-6">

                <label class="block text-gray-700 mb-2 font-medium">
                    Experience Level
                </label>

                <select
                    name="level"
                    required
                    class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-purple-500"
                >

                    <option value="">
                        Select Level
                    </option>

                    <option
                        value="Beginner"
                        {{ old('level') === 'Beginner' ? 'selected' : '' }}
                    >
                        Beginner (0-1 year)
                    </option>

                    <option
                        value="Intermediate"
                        {{ old('level') === 'Intermediate' ? 'selected' : '' }}
                    >
                        Intermediate (1-3 years)
                    </option>

                    <option
                        value="Advanced"
                        {{ old('level') === 'Advanced' ? 'selected' : '' }}
                    >
                        Advanced (3+ years)
                    </option>

                </select>

            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded font-bold transition"
            >
                🚀 Generate & Send AI Task
            </button>

            <a
                href="{{ route('tasks.index') }}"
                class="block text-center mt-4 text-purple-600 hover:underline"
            >
                ← Back to Task History
            </a>

            <p class="mt-4 text-sm text-gray-600 text-center">
                AI will generate a role and level-based task and send the
                complete assignment details to the trainee.
            </p>

        </form>

    </div>

</div>

@endsection