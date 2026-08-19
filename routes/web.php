<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;

Route::get('/', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::get('/create', [TaskController::class, 'create'])
    ->name('tasks.create');

Route::post('/generate-task', [TaskController::class, 'generateAndSend'])
    ->name('tasks.generate');

Route::patch('/tasks/{task}/evaluation', [TaskController::class, 'updateEvaluation'])
    ->name('tasks.evaluation.update');

/*
|--------------------------------------------------------------------------
| Training Performance Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');