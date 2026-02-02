<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AITaskGenerator;
use App\Services\TaskEmailService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function generateAndSend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'role' => 'required|in:Laravel,PHP,Frontend',
            'level' => 'required|in:Beginner,Intermediate,Advanced'
        ]);

        // Generate AI Task
        $aiGenerator = new AITaskGenerator();
        $taskData = $aiGenerator->generateTask($request->role, $request->level);
        
        // Save to database
        $task = Task::create([
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'role' => $request->role,
            'level' => $request->level,
            'duration_hours' => $taskData['duration_hours'],
            'test_instructions' => $taskData['test_instructions']
        ]);

        // Send Email
        $emailService = new TaskEmailService();
        $emailService->sendTaskEmail(
            $request->email,
            $request->name,
            $task,
            $request->role
        );

        return redirect()->back()
            ->with('success', "AI task generated and sent to {$request->email}");
    }
}