<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\TaskAssignment;
use App\Models\Task;
use App\Models\User;
use App\Mail\AssignmentCreated;
use App\Mail\DeadlineReminder;
use App\Mail\TaskNeedsRevision;
use App\Events\NewNotification;
use App\Events\TaskUpdated;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
            $notification = $this->createInAppNotification($user, $assignment);

            // Broadcast notification
            event(new NewNotification($notification));

            // Send email notification
            $this->sendEmailNotification($user, $assignment);
        }

        // Broadcast task update
        event(new TaskUpdated($assignment->task, 'assignment_created'));
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
        return Notification::create([
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
     * @param int $hoursBeforeDeadline Jam sebelum deadline untuk mengirim pengingat
     */
    public function sendDeadlineReminders($hoursBeforeDeadline = 24)
    {
        // Get assignments with deadline in next X hours
        $assignments = TaskAssignment::where('is_active', true)
            ->where('deadline', '>', now())
            ->where('deadline', '<=', now()->addHours($hoursBeforeDeadline))
            ->get();

        $notificationCount = 0;

        foreach ($assignments as $assignment) {
            $users = $this->getTargetUsers($assignment);

            foreach ($users as $user) {
                // Check if user hasn't submitted yet
                $hasSubmitted = $user->tasks()
                    ->where('assignment_id', $assignment->id)
                    ->exists();

                if (!$hasSubmitted) {
                    $this->createDeadlineReminderNotification($user, $assignment);
                    $notificationCount++;

                    // Convert hours to days for the email template
                    $daysLeft = ceil($hoursBeforeDeadline / 24);

                    // Create a virtual task for the email template
                    $virtualTask = new Task([
                        'judul_tugas' => $assignment->title,
                        'deskripsi_tugas' => $assignment->description,
                        'deadline' => $assignment->deadline,
                        'assignment_id' => $assignment->id,
                    ]);

                    // Send email notification
                    try {
                        Mail::to($user->email)->send(new DeadlineReminder($virtualTask, $user, $daysLeft));
                    } catch (\Exception $e) {
                        // Log error but don't stop the process
                        \Log::error('Failed to send deadline reminder email to ' . $user->email . ': ' . $e->getMessage());
                    }
                }
            }
        }

        return $notificationCount;
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

        $notification = Notification::create([
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

        // Broadcast notification
        event(new NewNotification($notification));

        // Broadcast task update event
        event(new TaskUpdated($task, 'needs_revision'));

        // Send email notification
        try {
            Mail::to($user->email)->send(new TaskNeedsRevision($task));
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Failed to send task revision email to ' . $user->email . ': ' . $e->getMessage());
        }
    }

    /**
     * Send a general notification to a user
     *
     * @param int $userId ID of the user to notify
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Type of notification
     * @param string|null $url URL to redirect when notification is clicked
     * @param array $data Additional data for the notification
     * @return Notification
     */
    public function createNotification($userId, $title, $message, $type = 'general', $url = null, $data = [])
    {
        $data = $data ?: [];
        if ($url) {
            $data['url'] = $url;
        }

        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'read_at' => null,
        ]);

        // Broadcast the notification
        event(new NewNotification($notification));

        return $notification;
    }
}
