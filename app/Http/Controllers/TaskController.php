<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AICodeReviewerService;
use App\Services\AITaskGenerator;
use App\Services\TaskEmailService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller
{
    /**
     * Display task history with search, filters,
     * sorting and pagination.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('trainee_name', 'like', "%{$search}%")
                    ->orWhere('trainee_email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Level Filter
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Duration Filter
        if ($request->filled('duration')) {
            $query->where('duration_hours', $request->duration);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');

        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        // Pagination
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

        // Generate AI Task
        $aiGenerator = new AITaskGenerator();

        $taskData = $aiGenerator->generateTask(
            $validated['role'],
            $validated['level']
        );

        // Save Task
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

        // Send Email
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
     * Show complete task details.
     */
    public function show(Task $task)
    {
        // Compute radar benchmarks against cohort averages
        $cohortAverages = [
            'architecture' => (int) round(Task::whereNotNull('score')->avg('score') ?? 70),
            'security' => (int) round(Task::whereNotNull('score')->avg('score') ?? 75),
            'database_orm' => (int) round(Task::whereNotNull('score')->avg('score') ?? 72),
            'clean_code' => (int) round(Task::whereNotNull('score')->avg('score') ?? 76),
            'problem_solving' => (int) round(Task::whereNotNull('score')->avg('score') ?? 74),
        ];

        return view('tasks.show', compact('task', 'cohortAverages'));
    }

    /**
     * 1-Click AI Code Review & Scoring trigger.
     */
    public function aiEvaluate(Task $task)
    {
        $reviewer = new AICodeReviewerService();
        $result = $reviewer->reviewTaskSubmission($task);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', "AI Evaluation completed! Score calculated: {$result['overall_score']}/100 with radar rubric updated.");
    }

    /**
     * Printable / Exportable Candidate Evaluation Report Card.
     */
    public function report(Task $task)
    {
        $cohortAvg = (int) round(Task::whereNotNull('score')->avg('score') ?? 72);

        // Calculate percentile ranking
        $totalCandidates = Task::whereNotNull('score')->count();
        $belowCount = Task::whereNotNull('score')->where('score', '<=', $task->score ?? 0)->count();
        $percentile = $totalCandidates > 0 ? round(($belowCount / $totalCandidates) * 100, 1) : 100;

        return view('tasks.report', compact('task', 'cohortAvg', 'percentile'));
    }

    /**
     * Export filtered tasks as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Task::query();

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('trainee_name', 'like', "%{$search}%")
                    ->orWhere('trainee_email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('duration')) {
            $query->where('duration_hours', $request->duration);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        if ($request->get('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $tasks = $query->get();

        $fileName =
            'ai-training-tasks-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(function () use ($tasks) {

            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'ID',
                'Trainee Name',
                'Trainee Email',
                'Title',
                'Role',
                'Level',
                'Duration Hours',
                'Status',
                'Score',
                'Assessment URL',
                'Tab Switches',
                'Paste Count',
                'Review Notes',
                'Completed At',
                'Created At',
            ]);

            // CSV Rows
            foreach ($tasks as $task) {
                fputcsv($handle, [
                    $task->id,
                    $task->trainee_name,
                    $task->trainee_email,
                    $task->title,
                    $task->role,
                    $task->level,
                    $task->duration_hours,
                    $task->status,
                    $task->score,
                    $task->assessment_url,
                    $task->tab_switch_count ?? 0,
                    $task->paste_count ?? 0,
                    $task->review_notes,
                    $task->completed_at?->format('Y-m-d H:i:s'),
                    $task->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Resend task email to trainee.
     */
    public function resendEmail(Task $task)
    {
        $emailService = new TaskEmailService();

        $emailService->sendTaskEmail(
            $task->trainee_email,
            $task->trainee_name,
            $task,
            $task->role
        );

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                "Task email resent successfully to {$task->trainee_email}."
            );
    }

    /**
     * Duplicate an existing task.
     */
    public function duplicate(Task $task)
    {
        $duplicate = Task::create([
            'trainee_name' => $task->trainee_name,
            'trainee_email' => $task->trainee_email,

            'title' => $task->title . ' - Copy',

            'description' => $task->description,

            'role' => $task->role,
            'level' => $task->level,

            'duration_hours' => $task->duration_hours,

            'test_instructions' => $task->test_instructions,

            'status' => 'Pending',

            'score' => null,

            'review_notes' => null,

            'completed_at' => null,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                "Task #{$task->id} duplicated successfully as Task #{$duplicate->id}."
            );
    }

    /**
     * Update task evaluation.
     */
    public function updateEvaluation(
        Request $request,
        Task $task
    ) {
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

        // Automatically manage completed_at
        if (
            in_array(
                $validated['status'],
                ['Completed', 'Reviewed']
            )
        ) {
            $validated['completed_at'] =
                $task->completed_at ?? now();
        } else {
            $validated['completed_at'] = null;
        }

        // Score handling
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
