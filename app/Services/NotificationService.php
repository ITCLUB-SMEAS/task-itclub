<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\TaskAssignment;
use App\Models\Task;
use App\Models\User;
use App\Mail\AssignmentCreated;
use App\Mail\TaskNeedsRevision;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notification when new assignment is created
     */
    public function sendAssignmentNotification(TaskAssignment $assignment)
    {
        // Get target users
        $users = $this->getTargetUsers($assignment);

        foreach ($users as $user) {
            // Create in-app notification
            $this->createInAppNotification($user, $assignment);

            // Send email notification
            $this->sendEmailNotification($user, $assignment);
        }
    }

    /**
     * Get users who should receive notification
     */
    private function getTargetUsers(TaskAssignment $assignment)
    {
        $query = User::where('role', 'student');

        // If assignment is for specific class
        if ($assignment->target_class) {
            $query->where('kelas', $assignment->target_class);
        }

        return $query->get();
    }

    /**
     * Create in-app notification
     */
    private function createInAppNotification(User $user, TaskAssignment $assignment)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'assignment_created',
            'title' => '📝 Tugas Baru: ' . $assignment->title,
            'message' => "Assignment baru '{$assignment->title}' telah dibuat. Deadline: " . $assignment->deadline->format('d/m/Y H:i'),
            'data' => [
                'assignment_id' => $assignment->id,
                'deadline' => $assignment->deadline->toISOString(),
                'difficulty' => $assignment->difficulty,
                'category' => $assignment->category,
            ],
        ]);
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, TaskAssignment $assignment)
    {
        try {
            Mail::to($user->email)->send(new AssignmentCreated($assignment));
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Failed to send assignment email to ' . $user->email . ': ' . $e->getMessage());
        }
    }

    /**
     * Send deadline reminder notifications
     */
    public function sendDeadlineReminders()
    {
        // Get assignments with deadline in next 24 hours
        $assignments = TaskAssignment::where('is_active', true)
            ->where('deadline', '>', now())
            ->where('deadline', '<=', now()->addHours(24))
            ->get();

        foreach ($assignments as $assignment) {
            $users = $this->getTargetUsers($assignment);

            foreach ($users as $user) {
                // Check if user hasn't submitted yet
                $hasSubmitted = $user->tasks()
                    ->where('assignment_id', $assignment->id)
                    ->exists();

                if (!$hasSubmitted) {
                    $this->createDeadlineReminderNotification($user, $assignment);
                }
            }
        }
    }

    /**
     * Create deadline reminder notification
     */
    private function createDeadlineReminderNotification(User $user, TaskAssignment $assignment)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'deadline_reminder',
            'title' => '⏰ Reminder: ' . $assignment->title,
            'message' => "Deadline assignment '{$assignment->title}' tinggal " . $assignment->deadline->diffForHumans() . "!",
            'data' => [
                'assignment_id' => $assignment->id,
                'deadline' => $assignment->deadline->toISOString(),
            ],
        ]);
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(User $user)
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, User $user)
    {
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user)
    {
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Send notification when task needs revision
     */
    public function sendTaskNeedsRevisionNotification(Task $task)
    {
        $user = $task->user;

        // Skip if no user associated
        if (!$user) {
            return;
        }

        // Create in-app notification
        $title = $task->assignment
            ? "⚠️ Revisi: " . $task->assignment->title
            : "⚠️ Tugas perlu direvisi";

        $message = $task->assignment
            ? "Tugas '{$task->assignment->title}' perlu direvisi."
            : "Tugas yang Anda kumpulkan perlu direvisi.";

        if ($task->catatan_admin) {
            $message .= " Catatan admin: \"{$task->catatan_admin}\"";
        }

        Notification::create([
            'user_id' => $user->id,
            'type' => 'task_needs_revision',
            'title' => $title,
            'message' => $message,
            'data' => [
                'task_id' => $task->id,
                'assignment_id' => $task->assignment_id,
                'catatan_admin' => $task->catatan_admin,
            ],
        ]);

        // Send email notification
        try {
            Mail::to($user->email)->send(new TaskNeedsRevision($task));
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Failed to send task revision email to ' . $user->email . ': ' . $e->getMessage());
        }
    }
}
