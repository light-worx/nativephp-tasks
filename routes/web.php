<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Home → task list
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

// Task CRUD
Route::get('/tasks/data',              [TaskController::class, 'data'])->name('tasks.data');
Route::get('/tasks/create',            [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks',                  [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{id}/edit',         [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{id}',              [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{id}',           [TaskController::class, 'destroy'])->name('tasks.destroy');

// Quick toggle complete
Route::post('/tasks/{id}/toggle',      [TaskController::class, 'toggle'])->name('tasks.toggle');

Route::get('/projects',              [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{id}/tasks',   [ProjectController::class, 'tasks'])->name('projects.tasks');

// Settings (API credentials)
Route::get('/settings',                [SettingsController::class, 'index'])->name('settings');
Route::post('/settings',               [SettingsController::class, 'store'])->name('settings.store');
