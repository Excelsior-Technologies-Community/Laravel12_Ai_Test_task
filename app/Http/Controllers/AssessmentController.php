<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\AICodeReviewerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display the secure in-browser candidate assessment portal & code sandbox.
     */
    public function show(string $token)
    {
        $task = Task::where('access_token', $token)->firstOrFail();

        // If candidate opened the test for the first time, initialize start timestamp
        if (!$task->started_at && $task->status === 'Pending') {
            $task->update([
                'started_at' => now(),
                'status' => 'In Progress',
            ]);
        }

        $remainingSeconds = $task->getRemainingSeconds();
        $isExpired = $task->isExpired();

        return view('assessment.portal', compact('task', 'remainingSeconds', 'isExpired'));
    }

    /**
     * Real-time auto-save candidate draft code.
     */
    public function saveDraft(Request $request, string $token): JsonResponse
    {
        $task = Task::where('access_token', $token)->firstOrFail();

        if ($task->status === 'Reviewed' || $task->submitted_at) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment has already been submitted.'
            ], 403);
        }

        $task->update([
            'draft_code' => $request->input('draft_code', ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft automatically synced',
            'saved_at' => now()->format('h:i:s A'),
        ]);
    }

    /**
     * Record proctoring anti-cheat telemetry events.
     */
    public function recordTelemetry(Request $request, string $token): JsonResponse
    {
        $task = Task::where('access_token', $token)->firstOrFail();

        $event = $request->input('event', 'UNKNOWN');
        $details = $request->input('details', '');

        $log = $task->proctoring_log ?? [];
        $logEntry = [
            'event' => $event,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
            'formatted_time' => now()->format('d M Y, h:i:s A'),
        ];
        $log[] = $logEntry;

        $tabSwitches = $task->tab_switch_count;
        $pastes = $task->paste_count;

        if ($event === 'TAB_SWITCH' || $event === 'WINDOW_BLUR') {
            $tabSwitches++;
        } elseif ($event === 'CLIPBOARD_PASTE') {
            $pastes++;
        }

        $task->update([
            'tab_switch_count' => $tabSwitches,
            'paste_count' => $pastes,
            'proctoring_log' => $log,
        ]);

        return response()->json([
            'success' => true,
            'tab_switch_count' => $tabSwitches,
            'paste_count' => $pastes,
            'risk' => $task->getProctoringRisk(),
        ]);
    }

    /**
     * Submit assessment solutions.
     */
    public function submit(Request $request, string $token)
    {
        $task = Task::where('access_token', $token)->firstOrFail();

        $validated = $request->validate([
            'submission_type' => 'required|in:code,github,both',
            'submitted_code' => 'nullable|string',
            'github_repo_url' => 'nullable|url|max:255',
        ]);

        $codeContent = $validated['submitted_code'] ?? $task->draft_code ?? '';

        $task->update([
            'submission_type' => $validated['submission_type'],
            'submitted_code' => $codeContent,
            'github_repo_url' => $validated['github_repo_url'] ?? null,
            'submitted_at' => now(),
            'completed_at' => now(),
            'status' => 'Completed',
        ]);

        // Auto trigger AI evaluation on candidate submission
        try {
            $reviewer = new AICodeReviewerService();
            $reviewer->reviewTaskSubmission($task);
        } catch (\Throwable $e) {
            // Log fallback if error
        }

        return redirect()
            ->route('assessment.show', ['token' => $token])
            ->with('success', 'Your assessment has been successfully submitted and reviewed by the AI engine!');
    }
}
