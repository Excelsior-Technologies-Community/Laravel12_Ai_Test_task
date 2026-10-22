<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display training performance dashboard with skill radar & anti-cheat insights.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Basic Task Statistics
        |--------------------------------------------------------------------------
        */

        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $reviewedTasks = Task::where('status', 'Reviewed')->count();

        /*
        |--------------------------------------------------------------------------
        | Completion Rate
        |--------------------------------------------------------------------------
        */
        $completionRate = $totalTasks > 0
            ? round((($completedTasks + $reviewedTasks) / $totalTasks) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Average Score
        |--------------------------------------------------------------------------
        */
        $averageScore = Task::whereNotNull('score')->avg('score');
        $averageScore = $averageScore !== null ? round($averageScore, 1) : 0;

        /*
        |--------------------------------------------------------------------------
        | Role & Level Statistics
        |--------------------------------------------------------------------------
        */
        $roleStats = Task::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderByDesc('total')
            ->get();

        $levelStats = Task::select('level', DB::raw('COUNT(*) as total'))
            ->groupBy('level')
            ->orderByDesc('total')
            ->get();

        $statusStats = Task::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Proctoring & Anti-Cheat Telemetry Statistics
        |--------------------------------------------------------------------------
        */
        $totalTabSwitches = Task::sum('tab_switch_count') ?? 0;
        $totalPasteEvents = Task::sum('paste_count') ?? 0;
        $flaggedCandidatesCount = Task::where('tab_switch_count', '>=', 3)
            ->orWhere('paste_count', '>=', 4)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Cohort Skill Radar Averages across 5 Dimensions
        |--------------------------------------------------------------------------
        */
        $reviewedWithRubric = Task::whereNotNull('ai_rubric_scores')->get();
        
        $archScores = [];
        $secScores = [];
        $dbScores = [];
        $cleanScores = [];
        $psScores = [];

        foreach ($reviewedWithRubric as $t) {
            $scores = $t->ai_rubric_scores;
            if (is_array($scores)) {
                if (isset($scores['architecture'])) $archScores[] = $scores['architecture'];
                if (isset($scores['security'])) $secScores[] = $scores['security'];
                if (isset($scores['database_orm'])) $dbScores[] = $scores['database_orm'];
                if (isset($scores['clean_code'])) $cleanScores[] = $scores['clean_code'];
                if (isset($scores['problem_solving'])) $psScores[] = $scores['problem_solving'];
            }
        }

        $radarCohort = [
            'architecture' => count($archScores) ? round(array_sum($archScores) / count($archScores)) : 74,
            'security' => count($secScores) ? round(array_sum($secScores) / count($secScores)) : 78,
            'database_orm' => count($dbScores) ? round(array_sum($dbScores) / count($dbScores)) : 72,
            'clean_code' => count($cleanScores) ? round(array_sum($cleanScores) / count($cleanScores)) : 79,
            'problem_solving' => count($psScores) ? round(array_sum($psScores) / count($psScores)) : 76,
        ];

        /*
        |--------------------------------------------------------------------------
        | Recent Reviewed & Top Trainees
        |--------------------------------------------------------------------------
        */
        $recentReviewedTasks = Task::whereIn('status', ['Completed', 'Reviewed'])
            ->latest('completed_at')
            ->take(6)
            ->get();

        $topTrainees = Task::select(
                'trainee_name',
                'trainee_email',
                DB::raw('COUNT(*) as reviewed_tasks'),
                DB::raw('AVG(score) as average_score')
            )
            ->where('status', 'Reviewed')
            ->whereNotNull('score')
            ->groupBy('trainee_name', 'trainee_email')
            ->orderByDesc('average_score')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'reviewedTasks',
            'completionRate',
            'averageScore',
            'roleStats',
            'levelStats',
            'statusStats',
            'recentReviewedTasks',
            'topTrainees',
            'totalTabSwitches',
            'totalPasteEvents',
            'flaggedCandidatesCount',
            'radarCohort'
        ));
    }
}
