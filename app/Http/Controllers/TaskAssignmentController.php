<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Services\NotificationService;

class TaskAssignmentController extends Controller
{
    /**
     * Display task assignments list for admin
     */
    public function index()
    {
        $assignments = TaskAssignment::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Show form for creating new assignment
     */
    public function create()
    {
        $classes = User::where('role', 'student')
                      ->whereNotNull('kelas')
                      ->distinct()
                      ->pluck('kelas')
                      ->sort();

        return view('admin.assignments.create', compact('classes'));
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'deadline' => 'required|date|after:now',
            'requirements' => 'nullable|array',
            'target_class' => 'nullable|string',
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'description.required' => 'Deskripsi tugas wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'difficulty.required' => 'Tingkat kesulitan wajib dipilih.',
            'deadline.required' => 'Deadline wajib diisi.',
            'deadline.after' => 'Deadline harus di masa depan.',
        ]);

        $assignment = TaskAssignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'deadline' => $request->deadline,
            'requirements' => $request->requirements ?: [],
            'target_class' => $request->target_class,
            'is_active' => true,
        ]);

        // Send notifications to students
        $notificationService = new NotificationService();
        $notificationService->sendAssignmentNotification($assignment);

        return redirect()->route('admin.assignments.index')
                        ->with('success', 'Task assignment berhasil dibuat dan notifikasi telah dikirim!');
    }

    /**
     * Show assignment details
     */
    public function show(TaskAssignment $assignment)
    {
        $assignment->load('submissions.user');
        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Get available assignments for students
     */
    public function available()
    {
        $user = auth()->user();

        $assignments = TaskAssignment::where('is_active', true)
                                   ->where('deadline', '>', now())
                                   ->where(function($query) use ($user) {
                                       $query->whereNull('target_class')
                                            ->orWhere('target_class', $user->kelas);
                                   })
                                   ->orderBy('deadline', 'asc')
                                   ->paginate(12);

        return view('student.assignments.available', compact('assignments'));
    }

    /**
     * Show assignment details for students
     */
    public function showForStudent(TaskAssignment $assignment)
    {
        $user = auth()->user();

        // Check if student can access this assignment
        if (!$assignment->is_active ||
            ($assignment->target_class && $assignment->target_class !== $user->kelas)) {
            abort(404, 'Assignment tidak ditemukan atau tidak dapat diakses.');
        }

        $assignment->load('submissions');

        return view('student.assignments.show', compact('assignment'));
    }

    /**
     * Show form for editing assignment
     */
    public function edit(TaskAssignment $assignment)
    {
        $classes = User::where('role', 'student')
                      ->whereNotNull('kelas')
                      ->distinct()
                      ->pluck('kelas')
                      ->sort();

        return view('admin.assignments.edit', compact('assignment', 'classes'));
    }

    /**
     * Update assignment
     */
    public function update(Request $request, TaskAssignment $assignment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'deadline' => 'required|date',
            'requirements' => 'nullable|array',
            'target_class' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'deadline' => $request->deadline,
            'requirements' => $request->requirements ?: [],
            'target_class' => $request->target_class,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.assignments.show', $assignment)
                        ->with('success', 'Assignment berhasil diupdate!');
    }

    /**
     * Delete assignment
     */
    public function destroy(TaskAssignment $assignment)
    {
        // Delete related submissions first
        $assignment->submissions()->delete();

        // Delete the assignment
        $assignment->delete();

        return redirect()->route('admin.assignments.index')
                        ->with('success', 'Assignment dan semua submission terkait berhasil dihapus!');
    }
}
