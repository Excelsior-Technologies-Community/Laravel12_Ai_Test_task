<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display training performance dashboard.
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
        |
        | Completed and Reviewed tasks are considered completed.
        |
        */

        $completionRate = $totalTasks > 0
            ? round(
                (($completedTasks + $reviewedTasks) / $totalTasks) * 100,
                1
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Average Score
        |--------------------------------------------------------------------------
        */

        $averageScore = Task::whereNotNull('score')
            ->avg('score');

        $averageScore = $averageScore !== null
            ? round($averageScore, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Role Statistics
        |--------------------------------------------------------------------------
        */

        $roleStats = Task::select(
                'role',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('role')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Level Statistics
        |--------------------------------------------------------------------------
        */

        $levelStats = Task::select(
                'level',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('level')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Status Statistics
        |--------------------------------------------------------------------------
        */

        $statusStats = Task::select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Recent Reviewed Tasks
        |--------------------------------------------------------------------------
        */

        $recentReviewedTasks = Task::whereIn(
                'status',
                ['Completed', 'Reviewed']
            )
            ->latest('completed_at')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Top Performing Trainees
        |--------------------------------------------------------------------------
        |
        | Only reviewed tasks with a score are included.
        |
        */

        $topTrainees = Task::select(
                'trainee_name',
                'trainee_email',
                DB::raw('COUNT(*) as reviewed_tasks'),
                DB::raw('AVG(score) as average_score')
            )
            ->where('status', 'Reviewed')
            ->whereNotNull('score')
            ->groupBy(
                'trainee_name',
                'trainee_email'
            )
            ->orderByDesc('average_score')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

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
            'topTrainees'
        ));
    }
}