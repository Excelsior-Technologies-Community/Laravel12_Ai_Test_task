<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssessmentController;

/*
|--------------------------------------------------------------------------
| Candidate Assessment Portal & In-Browser Code Sandbox (Public / Tokenized)
|--------------------------------------------------------------------------
*/
Route::get('/assessment/{token}', [AssessmentController::class, 'show'])
    ->name('assessment.show');

Route::post('/assessment/{token}/draft', [AssessmentController::class, 'saveDraft'])
    ->name('assessment.draft');

Route::post('/assessment/{token}/telemetry', [AssessmentController::class, 'recordTelemetry'])
    ->name('assessment.telemetry');

Route::post('/assessment/{token}/submit', [AssessmentController::class, 'submit'])
    ->name('assessment.submit');

/*
|--------------------------------------------------------------------------
| Task Management & Instructor Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::get('/create', [TaskController::class, 'create'])
    ->name('tasks.create');

Route::post('/generate-task', [TaskController::class, 'generateAndSend'])
    ->name('tasks.generate');

Route::get('/tasks/export', [TaskController::class, 'export'])
    ->name('tasks.export');

Route::post('/tasks/{task}/resend', [TaskController::class, 'resendEmail'])
    ->name('tasks.resend');

Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])
    ->name('tasks.duplicate');

Route::patch('/tasks/{task}/evaluation', [TaskController::class, 'updateEvaluation'])
    ->name('tasks.evaluation.update');

Route::post('/tasks/{task}/ai-evaluate', [TaskController::class, 'aiEvaluate'])
    ->name('tasks.ai_evaluate');

Route::get('/tasks/{task}/report', [TaskController::class, 'report'])
    ->name('tasks.report');

Route::get('/tasks/{task}', [TaskController::class, 'show'])
    ->name('tasks.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
