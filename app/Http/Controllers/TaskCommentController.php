<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Create the comment
        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

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
}
