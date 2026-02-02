<!DOCTYPE html>
<html>
<head>
    <title>AI Task System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-purple-600 text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-xl font-bold">🤖 AI Task Generator</h1>
        </div>
    </nav>

    <main class="container mx-auto mt-8 p-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>