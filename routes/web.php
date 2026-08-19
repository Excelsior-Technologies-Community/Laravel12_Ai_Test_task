<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Task Management Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Task List
|--------------------------------------------------------------------------
*/

Route::get('/', [TaskController::class, 'index'])
    ->name('tasks.index');


/*
|--------------------------------------------------------------------------
| Create Task
|--------------------------------------------------------------------------
*/
Route::get('/create', [TaskController::class, 'create'])
    ->name('tasks.create');


/*
|--------------------------------------------------------------------------
| Generate AI Task and Send Email
|--------------------------------------------------------------------------
*/
Route::post('/generate-task', [TaskController::class, 'generateAndSend'])
    ->name('tasks.generate');


/*
|--------------------------------------------------------------------------
| Export Tasks as CSV
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Keep this route BEFORE /tasks/{task}
| if you add a dynamic task route later.
|
*/
Route::get('/tasks/export', [TaskController::class, 'export'])
    ->name('tasks.export');


/*
|--------------------------------------------------------------------------
| Resend Task Email
|--------------------------------------------------------------------------
*/
Route::post('/tasks/{task}/resend', [TaskController::class, 'resendEmail'])
    ->name('tasks.resend');


/*
|--------------------------------------------------------------------------
| Duplicate Task
|--------------------------------------------------------------------------
*/
Route::post('/tasks/{task}/duplicate', [TaskController::class, 'duplicate'])
    ->name('tasks.duplicate');


/*
|--------------------------------------------------------------------------
| Update Task Evaluation
|--------------------------------------------------------------------------
*/
Route::patch('/tasks/{task}/evaluation', [TaskController::class, 'updateEvaluation'])
    ->name('tasks.evaluation.update');


/*
|--------------------------------------------------------------------------
| Task Details
|--------------------------------------------------------------------------
*/
Route::get('/tasks/{task}', [TaskController::class, 'show'])
    ->name('tasks.show');


/*
|--------------------------------------------------------------------------
| Training Performance Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
