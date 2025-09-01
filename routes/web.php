<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// Redirect root ke login
Route::get('/', function () {
    return redirect('/login');
});

// Routes untuk authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes yang memerlukan authentication
Route::middleware('auth')->group(function () {
    // Dashboard Admin - hanya bisa diakses oleh admin
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    // Dashboard Student - hanya bisa diakses oleh student
    Route::get('/student/dashboard', [AuthController::class, 'studentDashboard'])
        ->middleware('role:student')
        ->name('student.dashboard');

    // Task Management Routes

    // Routes untuk Student
    Route::middleware('role:student')->group(function () {
        Route::get('/submit-task', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/submit-task', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my');
    });

    // Routes untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/tasks', [TaskController::class, 'index'])->name('admin.tasks');
        Route::patch('/admin/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('admin.tasks.status');
    });
});
