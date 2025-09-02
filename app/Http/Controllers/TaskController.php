<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Menampilkan form pengumpulan tugas (untuk student)
     */
    public function create()
    {
        return view('student.tasks.create');
    }

    /**
     * Menyimpan tugas yang dikumpulkan (support assignment-based submission)
     */
    public function store(Request $request)
    {
        // Check if this is assignment-based submission
        if ($request->has('assignment_id')) {
            return $this->storeAssignmentSubmission($request);
        }

        // Legacy submission (existing system)
        return $this->storeLegacySubmission($request);
    }

    /**
     * Store assignment-based submission
     */
    protected function storeAssignmentSubmission(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:task_assignments,id',
            'github_repo' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,rar,png,jpg,jpeg',
        ], [
            'assignment_id.required' => 'Assignment tidak valid.',
            'github_repo.required' => 'Link GitHub wajib diisi.',
            'github_repo.url' => 'Format URL GitHub tidak valid.',
            'files.*.max' => 'Ukuran file maksimal 10MB.',
            'files.*.mimes' => 'File harus berformat PDF, DOC, DOCX, ZIP, RAR, PNG, JPG, atau JPEG.',
        ]);

        $user = Auth::user();
        $assignment = TaskAssignment::findOrFail($request->assignment_id);

        // Check if user already submitted this assignment
        $existingSubmission = Task::where('user_id', $user->id)
                                   ->where('assignment_id', $assignment->id)
                                   ->first();

        if ($existingSubmission) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan assignment ini. Gunakan tombol Update jika ingin mengubah submission.');
        }

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions/' . $user->id . '/' . $assignment->id, 'public');
                $uploadedFiles[] = $path;
            }
        }

        // Check if submission is late
        $isLate = now()->isAfter($assignment->deadline);

        // Create submission
        Task::create([
            'user_id' => $user->id,
            'assignment_id' => $assignment->id,
            'nama_lengkap' => $user->name,
            'kelas' => $user->kelas ?? 'Tidak ada kelas',
            'email' => $user->email,
            'github_link' => $request->github_repo,
            'deskripsi_tugas' => $request->description,
            'file_uploads' => $uploadedFiles,
            'category' => $assignment->category,
            'difficulty' => $assignment->difficulty,
            'deadline' => $assignment->deadline,
            'tanggal_mengumpulkan' => now(),
            'is_late' => $isLate,
            'status' => 'pending'
        ]);

        $message = $isLate
            ? 'Assignment berhasil dikumpulkan (terlambat)! Menunggu review admin.'
            : 'Assignment berhasil dikumpulkan! Menunggu review admin.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Legacy submission (existing system)
     */
    protected function storeLegacySubmission(Request $request)
    {
        // Validasi input
        $request->validate([
            'github' => 'required|url|max:500',
            'deskripsi' => 'required|string|min:50|max:1000',
            'tanggal' => 'required|date',
        ], [
            'github.required' => 'Link GitHub wajib diisi.',
            'github.url' => 'Format URL GitHub tidak valid.',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi.',
            'deskripsi.min' => 'Deskripsi tugas minimal 50 karakter.',
            'deskripsi.max' => 'Deskripsi tugas maksimal 1000 karakter.',
            'tanggal.required' => 'Tanggal mengumpulkan wajib diisi.',
        ]);

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Simpan tugas ke database
        Task::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'kelas' => $user->kelas ?? 'Tidak ada kelas',
            'email' => $user->email,
            'github_link' => $request->github,
            'deskripsi_tugas' => $request->deskripsi,
            'tanggal_mengumpulkan' => $request->tanggal,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan! Menunggu persetujuan admin.');
    }

    /**
     * Update existing task submission
     */
    public function update(Request $request, Task $task)
    {
        // Check if user owns this task
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if task is assignment-based
        if (!$task->assignment_id) {
            return redirect()->back()->with('error', 'Task ini tidak dapat diupdate.');
        }

        $assignment = $task->assignment;

        // Check if assignment is still active (not overdue for too long)
        if (now()->isAfter($assignment->deadline->addDays(7))) {
            return redirect()->back()->with('error', 'Assignment sudah terlalu lama melewati deadline untuk diupdate.');
        }

        $request->validate([
            'github_repo' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,rar,png,jpg,jpeg',
        ]);

        // Handle file uploads
        $uploadedFiles = $task->file_uploads ?? [];
        if ($request->hasFile('files')) {
            // Delete old files
            if (!empty($task->file_uploads)) {
                foreach ($task->file_uploads as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            // Upload new files
            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions/' . Auth::id() . '/' . $assignment->id, 'public');
                $uploadedFiles[] = $path;
            }
        }

                // Update submission
        $task->update([
            'github_link' => $request->github_repo,
            'deskripsi_tugas' => $request->description,
            'file_uploads' => $uploadedFiles,
            'is_late' => now()->isAfter($assignment->deadline),
            'status' => 'pending', // Reset to pending for re-review
        ]);

        return redirect()->back()->with('success', 'Submission berhasil diupdate! Menunggu review ulang admin.');
    }

    /**
     * Menampilkan semua tugas (untuk admin)
     */
    public function index()
    {
        $tasks = Task::with(['user', 'assignment'])->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Update status tugas (untuk admin)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $task->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', 'Status tugas berhasil diupdate.');
    }

    /**
     * Menampilkan tugas milik user yang login (untuk student)
     */
    public function myTasks()
    {
        $tasks = Auth::user()->tasks()->with('assignment')->latest()->get();
        return view('student.tasks.index', compact('tasks'));
    }
}
