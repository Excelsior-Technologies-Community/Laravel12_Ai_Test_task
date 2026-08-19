<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        AI Training System
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen">

    {{-- Navigation --}}
    <nav class="bg-purple-600 text-white shadow">

        <div class="container mx-auto px-4 py-4">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">

                {{-- Logo --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="text-xl font-bold"
                >
                    🤖 AI Training System
                </a>


                {{-- Navigation Links --}}
                <div class="flex flex-wrap items-center gap-2">

                    <a
                        href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded hover:bg-purple-700
                        {{ request()->routeIs('dashboard') ? 'bg-purple-700' : '' }}"
                    >
                        📊 Dashboard
                    </a>

                    <a
                        href="{{ route('tasks.index') }}"
                        class="px-4 py-2 rounded hover:bg-purple-700
                        {{ request()->routeIs('tasks.index') ? 'bg-purple-700' : '' }}"
                    >
                        📋 Task History
                    </a>

                    <a
                        href="{{ route('tasks.create') }}"
                        class="bg-white text-purple-600 px-4 py-2 rounded font-medium hover:bg-gray-100"
                    >
                        + Generate Task
                    </a>

                </div>

            </div>

        </div>

    </nav>


    {{-- Main Content --}}
    <main class="container mx-auto mt-8 px-4 pb-10">

        @yield('content')

    </main>

</body>

</html>