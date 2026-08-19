<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AITaskGenerator;
use App\Services\TaskEmailService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display task history with search, filters,
     * sorting and pagination.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('trainee_name', 'like', "%{$search}%")
                    ->orWhere('trainee_email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Role Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        /*
        |--------------------------------------------------------------------------
        | Level Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        /*
        |--------------------------------------------------------------------------
        | Duration Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('duration')) {
            $query->where(
                'duration_hours',
                $request->duration
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'newest');

        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $tasks = $query
            ->paginate(6)
            ->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show task generation form.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Generate and email a new AI task.
     */
    public function generateAndSend(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'role' => [
                'required',
                'in:Laravel,PHP,Frontend',
            ],

            'level' => [
                'required',
                'in:Beginner,Intermediate,Advanced',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate AI Task
        |--------------------------------------------------------------------------
        */

        $aiGenerator = new AITaskGenerator();

        $taskData = $aiGenerator->generateTask(
            $validated['role'],
            $validated['level']
        );

        /*
        |--------------------------------------------------------------------------
        | Save Task
        |--------------------------------------------------------------------------
        */

        $task = Task::create([
            'trainee_name' => $validated['name'],
            'trainee_email' => $validated['email'],

            'title' => $taskData['title'],
            'description' => $taskData['description'],

            'role' => $validated['role'],
            'level' => $validated['level'],

            'duration_hours' => $taskData['duration_hours'],

            'test_instructions' => $taskData['test_instructions'],

            'status' => 'Pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send Email
        |--------------------------------------------------------------------------
        */

        $emailService = new TaskEmailService();

        $emailService->sendTaskEmail(
            $validated['email'],
            $validated['name'],
            $task,
            $validated['role']
        );

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                "AI task generated and sent to {$validated['email']}"
            );
    }

    /**
     * Update task evaluation.
     */
    public function updateEvaluation(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:Pending,In Progress,Completed,Reviewed',
            ],

            'score' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'review_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Automatically manage completed_at
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $validated['status'],
                ['Completed', 'Reviewed']
            )
        ) {
            $validated['completed_at'] = $task->completed_at ?? now();
        } else {
            $validated['completed_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Score handling
        |--------------------------------------------------------------------------
        |
        | A score is mainly meaningful for Reviewed tasks.
        | If the task isn't reviewed yet, we allow it to remain empty.
        |
        */

        if (
            $validated['status'] !== 'Reviewed'
            && empty($validated['score'])
        ) {
            $validated['score'] = null;
        }

        $task->update([
            'status' => $validated['status'],
            'score' => $validated['score'] ?? null,
            'review_notes' => $validated['review_notes'] ?? null,
            'completed_at' => $validated['completed_at'],
        ]);

        return redirect()
            ->route('tasks.index', $request->query())
            ->with(
                'success',
                'Task evaluation updated successfully.'
            );
    }
}