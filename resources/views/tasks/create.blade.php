@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Generate AI Task</h2>
        
        <form method="POST" action="/generate-task">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Trainee Name</label>
                <input type="text" name="name" required 
                       class="w-full px-4 py-2 border rounded" 
                       placeholder="Enter name">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-2 border rounded" 
                       placeholder="Enter email">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Select Role</label>
                <select name="role" required class="w-full px-4 py-2 border rounded">
                    <option value="">Choose Role</option>
                    <option value="Laravel">Laravel Developer</option>
                    <option value="PHP">PHP Developer</option>
                    <option value="Frontend">Frontend Developer</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Experience Level</label>
                <select name="level" required class="w-full px-4 py-2 border rounded">
                    <option value="">Select Level</option>
                    <option value="Beginner">Beginner (0-1 year)</option>
                    <option value="Intermediate">Intermediate (1-3 years)</option>
                    <option value="Advanced">Advanced (3+ years)</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded font-bold">
                🚀 Generate & Send AI Task
            </button>
            
            <p class="mt-4 text-sm text-gray-600 text-center">
                AI will generate task and send test details to email
            </p>
        </form>
    </div>
</div>
@endsection