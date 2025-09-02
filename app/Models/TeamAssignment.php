<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'assignment_id',
        'title',
        'description',
        'file_path',
        'status',
        'feedback',
        'nilai',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai' => 'integer',
    ];

    /**
     * Get the team that submitted this assignment
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the original assignment this is a submission for
     */
    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'assignment_id');
    }

    /**
     * Get the user who submitted this assignment
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Scope for pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved assignments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected assignments
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
