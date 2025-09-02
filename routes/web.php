<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamAssignmentController as TeamAssignCtrl;
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
        Route::post('/assignments/{assignment}/submit', [TaskController::class, 'storeAssignmentSubmission'])->name('assignments.submit');

        // Notification routes for students
            // Komentar
    Route::post('/tasks/{task}/comments', [App\Http\Controllers\TaskCommentController::class, 'store'])->name('task.comments.store');
    Route::delete('/comments/{comment}', [App\Http\Controllers\TaskCommentController::class, 'destroy'])->name('task.comments.destroy');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });

    // Routes untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/tasks', [TaskController::class, 'index'])->name('admin.tasks');
        Route::patch('/admin/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('admin.tasks.status');
        Route::patch('/admin/tasks/{task}/grade', [TaskController::class, 'giveGrade'])->name('admin.tasks.grade');

        // Export routes
        Route::get('/admin/export/tasks', [App\Http\Controllers\ExportController::class, 'exportTasks'])->name('admin.export.tasks');
        Route::get('/admin/export/assignment-report', [App\Http\Controllers\ExportController::class, 'exportAssignmentReport'])->name('admin.export.assignment');

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

        // Team Management for Admin
        Route::prefix('admin/teams')->name('admin.teams.')->middleware('role:admin')->group(function () {
            Route::get('/', [TeamController::class, 'index'])->name('index');
            Route::get('/create', [TeamController::class, 'create'])->name('create');
            Route::post('/', [TeamController::class, 'store'])->name('store');
            Route::get('/{team}', [TeamController::class, 'show'])->name('show');
            Route::get('/{team}/edit', [TeamController::class, 'edit'])->name('edit');
            Route::put('/{team}', [TeamController::class, 'update'])->name('update');
            Route::delete('/{team}', [TeamController::class, 'destroy'])->name('destroy');
            Route::post('/{team}/regenerate-code', [TeamController::class, 'regenerateCode'])->name('regenerate-code');
            Route::post('/{team}/toggle-status', [TeamController::class, 'toggleStatus'])->name('toggle-status');

            // Team Members
            Route::get('/{team}/members', [TeamMemberController::class, 'index'])->name('members.index');
            Route::get('/{team}/members/create', [TeamMemberController::class, 'create'])->name('members.create');
            Route::post('/{team}/members', [TeamMemberController::class, 'store'])->name('members.store');
            Route::get('/{team}/members/{member}', [TeamMemberController::class, 'show'])->name('members.show');
            Route::get('/{team}/members/{member}/edit', [TeamMemberController::class, 'edit'])->name('members.edit');
            Route::put('/{team}/members/{member}', [TeamMemberController::class, 'update'])->name('members.update');
            Route::delete('/{team}/members/{member}', [TeamMemberController::class, 'destroy'])->name('members.destroy');

            // Team Assignments
            Route::get('/{team}/assignments', [TeamAssignCtrl::class, 'index'])->name('assignments.index');
            Route::get('/{team}/assignments/create', [TeamAssignCtrl::class, 'create'])->name('assignments.create');
            Route::post('/{team}/assignments', [TeamAssignCtrl::class, 'store'])->name('assignments.store');
            Route::get('/{team}/assignments/{assignment}', [TeamAssignCtrl::class, 'show'])->name('assignments.show');
            Route::get('/{team}/assignments/{assignment}/edit', [TeamAssignCtrl::class, 'edit'])->name('assignments.edit');
            Route::put('/{team}/assignments/{assignment}', [TeamAssignCtrl::class, 'update'])->name('assignments.update');
            Route::delete('/{team}/assignments/{assignment}', [TeamAssignCtrl::class, 'destroy'])->name('assignments.destroy');
            Route::post('/{team}/assignments/{assignment}/feedback', [TeamAssignCtrl::class, 'provideFeedback'])->name('assignments.feedback');
        });
    });

    // Team Management for Student
    Route::prefix('student/teams')->name('student.teams.')->middleware('role:student')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/create', [TeamController::class, 'create'])->name('create');
        Route::post('/', [TeamController::class, 'store'])->name('store');
        Route::get('/{team}', [TeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [TeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [TeamController::class, 'update'])->name('update');
        Route::delete('/{team}', [TeamController::class, 'destroy'])->name('destroy');
        Route::post('/join', [TeamController::class, 'joinByCode'])->name('join');
        Route::post('/{team}/regenerate-code', [TeamController::class, 'regenerateCode'])->name('regenerate-code');
        Route::post('/{team}/toggle-status', [TeamController::class, 'toggleStatus'])->name('toggle-status');

        // Team Members
        Route::get('/{team}/members', [TeamMemberController::class, 'index'])->name('members.index');
        Route::get('/{team}/members/create', [TeamMemberController::class, 'create'])->name('members.create');
        Route::post('/{team}/members', [TeamMemberController::class, 'store'])->name('members.store');
        Route::get('/{team}/members/{member}', [TeamMemberController::class, 'show'])->name('members.show');
        Route::delete('/{team}/members/{member}', [TeamMemberController::class, 'destroy'])->name('members.destroy');
        Route::post('/{team}/members/invite', [TeamMemberController::class, 'invite'])->name('members.invite');

        // Team Assignments
        Route::get('/{team}/assignments', [TeamAssignCtrl::class, 'index'])->name('assignments.index');
        Route::get('/{team}/assignments/{assignment}', [TeamAssignCtrl::class, 'show'])->name('assignments.show');
        Route::post('/{team}/assignments/{assignment}/submit', [TeamAssignCtrl::class, 'submitWork'])->name('assignments.submit');
    });
});
