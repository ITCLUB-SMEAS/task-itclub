<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        $user = $request->user();
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Email berhasil diverifikasi!');
        }
        return redirect('/student/dashboard')->with('success', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link verifikasi baru telah dikirim ke email Anda!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Routes yang memerlukan authentication DAN verifikasi email
Route::middleware(['auth', 'verified'])->group(function () {
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
        Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::get('/my-tasks', [TaskController::class, 'myTasks'])->name('tasks.my');

        // Assignment routes for students
        Route::get('/assignments', [TaskAssignmentController::class, 'available'])->name('assignments.available');
        Route::get('/assignments/{assignment}', [TaskAssignmentController::class, 'showForStudent'])->name('assignments.show');

        // Notification routes for students
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });

    // Routes untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/tasks', [TaskController::class, 'index'])->name('admin.tasks');
        Route::patch('/admin/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('admin.tasks.status');

        // Task Assignment Management
        Route::resource('admin/assignments', TaskAssignmentController::class)->names([
            'index' => 'admin.assignments.index',
            'create' => 'admin.assignments.create',
            'store' => 'admin.assignments.store',
            'show' => 'admin.assignments.show',
            'edit' => 'admin.assignments.edit',
            'update' => 'admin.assignments.update',
            'destroy' => 'admin.assignments.destroy',
        ]);
    });
});
