<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskNeedsRevision extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Tugas Perlu Revisi: ' . ($this->task->assignment ? $this->task->assignment->title : 'Tugas'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = $this->task->assignment_id
            ? route('assignments.show', $this->task->assignment_id)
            : route('tasks.my');

        return new Content(
            view: 'emails.task-needs-revision',
            with: [
                'task' => $this->task,
                'url' => $url,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
