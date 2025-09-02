<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamAssignment;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamAssignmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $teamId)
    {
        $team = Team::with(['assignments.task'])->findOrFail($teamId);

        // Cek apakah user adalah anggota tim atau admin
        if (auth()->user()->role !== 'admin' && !$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk melihat tugas tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.team_assignments.index', compact('team'));
        } else {
            return view('student.team_assignments.index', compact('team'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Hanya admin yang dapat membuat tugas tim
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk membuat tugas tim.');
        }

        // Dapatkan tugas yang tersedia untuk kelas tim
        $availableTasks = Task::whereJsonContains('kelas', $team->class_group)
            ->whereDoesntHave('teamAssignments', function($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->get();

        return view('admin.team_assignments.create', compact('team', 'availableTasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Hanya admin yang dapat membuat tugas tim
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk membuat tugas tim.');
        }

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'due_date' => 'required|date|after:today',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek apakah tugas sudah ada untuk tim ini
        if (TeamAssignment::where('team_id', $team->id)->where('task_id', $request->task_id)->exists()) {
            return back()->with('error', 'Tim ini sudah memiliki tugas tersebut.');
        }

        // Buat tugas tim
        $assignment = new TeamAssignment();
        $assignment->team_id = $team->id;
        $assignment->task_id = $request->task_id;
        $assignment->due_date = $request->due_date;
        $assignment->instructions = $request->instructions;
        $assignment->status = 'assigned';
        $assignment->created_by = auth()->id();
        $assignment->save();

        // Kirim notifikasi ke semua anggota tim
        foreach ($team->members as $member) {
            $this->notificationService->createNotification(
                $member->user_id,
                'Tugas Tim Baru',
                "Tim {$team->name} telah diberi tugas baru.",
                'team_assignment',
                route('student.teams.assignments.show', [$team->id, $assignment->id])
            );
        }

        return redirect()->route('admin.teams.assignments.index', $team->id)->with('success', 'Tugas tim berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::with(['task', 'team.members.user'])->findOrFail($assignmentId);

        // Cek apakah user adalah anggota tim atau admin
        if (auth()->user()->role !== 'admin' && !$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk melihat detail tugas tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.team_assignments.show', compact('team', 'assignment'));
        } else {
            return view('student.team_assignments.show', compact('team', 'assignment'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::findOrFail($assignmentId);

        // Hanya admin yang dapat mengedit tugas tim
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk mengedit tugas tim.');
        }

        return view('admin.team_assignments.edit', compact('team', 'assignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::findOrFail($assignmentId);

        // Cek apakah user adalah admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk mengedit tugas tim.');
        }

        $validator = Validator::make($request->all(), [
            'due_date' => 'required|date',
            'instructions' => 'nullable|string',
            'status' => 'required|in:assigned,in_review,completed,late',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $assignment->due_date = $request->due_date;
        $assignment->instructions = $request->instructions;
        $assignment->status = $request->status;
        $assignment->save();

        // Jika status berubah, kirim notifikasi
        if ($assignment->wasChanged('status')) {
            $statusLabels = [
                'assigned' => 'Ditugaskan',
                'in_review' => 'Sedang Direview',
                'completed' => 'Selesai',
                'late' => 'Terlambat',
            ];

            foreach ($team->members as $member) {
                $this->notificationService->createNotification(
                    $member->user_id,
                    'Status Tugas Tim Diperbarui',
                    "Status tugas tim {$team->name} diubah menjadi: {$statusLabels[$assignment->status]}",
                    'team_assignment_status',
                    route('student.teams.assignments.show', [$team->id, $assignment->id])
                );
            }
        }

        return redirect()->route('admin.teams.assignments.show', [$team->id, $assignment->id])->with('success', 'Tugas tim berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::findOrFail($assignmentId);

        // Cek apakah user adalah admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk menghapus tugas tim.');
        }

        $assignment->delete();

        return redirect()->route('admin.teams.assignments.index', $team->id)->with('success', 'Tugas tim berhasil dihapus.');
    }

    /**
     * Submit work for a team assignment
     */
    public function submitWork(Request $request, string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::findOrFail($assignmentId);

        // Cek apakah user adalah anggota tim
        if (!$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk mengumpulkan tugas tim ini.');
        }

        // Cek apakah tugas masih berlaku
        if ($assignment->status === 'completed') {
            return back()->with('error', 'Tugas ini sudah selesai.');
        }

        $validator = Validator::make($request->all(), [
            'submission_notes' => 'required|string',
            'submission_file' => 'required|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Simpan file
        $filePath = $request->file('submission_file')->store('team-submissions', 'public');

        // Update tugas
        $assignment->submission_file = $filePath;
        $assignment->submission_notes = $request->submission_notes;
        $assignment->submitted_by = auth()->id();
        $assignment->submitted_at = now();

        // Cek apakah terlambat
        if (now() > $assignment->due_date) {
            $assignment->status = 'late';
        } else {
            $assignment->status = 'in_review';
        }

        $assignment->save();

        // Kirim notifikasi ke admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->createNotification(
                $admin->id,
                'Pengumpulan Tugas Tim',
                "Tim {$team->name} telah mengumpulkan tugasnya: {$assignment->task->judul_tugas}",
                'team_assignment_submission',
                route('admin.teams.assignments.show', [$team->id, $assignment->id])
            );
        }

        return redirect()->route('student.teams.assignments.show', [$team->id, $assignment->id])->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Provide feedback for a team assignment
     */
    public function provideFeedback(Request $request, string $teamId, string $assignmentId)
    {
        $team = Team::findOrFail($teamId);
        $assignment = TeamAssignment::findOrFail($assignmentId);

        // Cek apakah user adalah admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan untuk memberikan feedback.');
        }

        $validator = Validator::make($request->all(), [
            'feedback' => 'required|string',
            'grade' => 'required|integer|min:0|max:100',
            'status' => 'required|in:in_review,completed,needs_revision',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $assignment->feedback = $request->feedback;
        $assignment->grade = $request->grade;
        $assignment->status = $request->status;
        $assignment->reviewed_at = now();
        $assignment->reviewed_by = auth()->id();
        $assignment->save();

        // Kirim notifikasi ke anggota tim
        foreach ($team->members as $member) {
            $this->notificationService->createNotification(
                $member->user_id,
                'Feedback Tugas Tim',
                "Tugas tim {$team->name} telah diberi feedback oleh admin.",
                'team_assignment_feedback',
                route('student.teams.assignments.show', [$team->id, $assignment->id])
            );
        }

        return redirect()->route('admin.teams.assignments.show', [$team->id, $assignment->id])->with('success', 'Feedback berhasil diberikan.');
    }
}
