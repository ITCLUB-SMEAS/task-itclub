<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Notification;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request, $taskId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Check if we're dealing with a TaskAssignment or a Task
        if ($taskId instanceof TaskAssignment) {
            // If it's a TaskAssignment, we need to create a comment for the assignment
            $task = $taskId;
            $taskId = $task->id;
        } else {
            // Otherwise, we're dealing with a Task ID
            $task = Task::findOrFail($taskId);
        }

        // Create the comment
        $comment = TaskComment::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        if ($task instanceof TaskAssignment) {
            // For TaskAssignment, notify the admin
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $this->createAssignmentCommentNotification($task, $admin);
            }
        } else {
            // For regular Task
            // If the comment author is not the task owner, create a notification
            if (Auth::id() !== $task->user_id) {
                // Create notification for the task owner
                $this->createCommentNotification($task, Auth::user());
            } elseif ($task->user_id === Auth::id() && Auth::user()->role === 'student') {
                // If student comments on their own task, notify admin
                // Find admin users who should be notified (simplest approach: all admins)
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $this->createAdminCommentNotification($task, $admin);
                }
            }
        }

        return back()->with('success', 'Komentar berhasil ditambahkan');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(TaskComment $comment)
    {
        // Check if the user can delete this comment
        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus komentar ini');
        }

        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus');
    }

    /**
     * Create notification for task owner
     */
    private function createCommentNotification($task, $commentAuthor)
    {
        Notification::create([
            'user_id' => $task->user_id,
            'type' => 'task_comment',
            'title' => 'Komentar Baru pada Tugas Anda',
            'message' => "{$commentAuthor->name} mengomentari tugas Anda: " . substr($task->judul_tugas, 0, 50) . '...',
            'data' => [
                'task_id' => $task->id,
                'assignment_id' => $task->assignment_id,
                'comment_author' => $commentAuthor->name,
            ],
        ]);
    }

    /**
     * Create notification for admin
     */
    private function createAdminCommentNotification($task, $admin)
    {
        Notification::create([
            'user_id' => $admin->id,
            'type' => 'task_comment',
            'title' => 'Komentar Baru dari Siswa',
            'message' => "{$task->user->name} mengomentari tugasnya: " . substr($task->judul_tugas, 0, 50) . '...',
            'data' => [
                'task_id' => $task->id,
                'assignment_id' => $task->assignment_id,
                'student_name' => $task->user->name,
            ],
        ]);
    }

    /**
     * Create notification for admin about assignment comment
     */
    private function createAssignmentCommentNotification($assignment, $admin)
    {
        $student = Auth::user();

        Notification::create([
            'user_id' => $admin->id,
            'type' => 'assignment_comment',
            'title' => 'Komentar Baru pada Tugas',
            'message' => "{$student->name} mengomentari tugas: " . substr($assignment->title, 0, 50) . '...',
            'data' => [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'student_name' => $student->name,
            ],
        ]);
    }
}
