<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WorkspaceController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return redirect()->route('workspaces.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('workspaces', WorkspaceController::class)
        ->except(['create', 'edit']); 

    Route::prefix('workspaces/{workspace}')->name('workspaces.')->group(function () {

        Route::resource('tasks', TaskController::class)
            ->except(['create', 'edit'])
            ->names('tasks');

        Route::patch('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])
            ->name('tasks.toggle-complete');

    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
