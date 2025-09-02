<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'difficulty',
        'deadline',
        'is_active',
        'requirements',
        'target_class',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_active' => 'boolean',
        'requirements' => 'array',
    ];

    /**
     * Get submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(Task::class, 'assignment_id');
    }

    /**
     * Check if deadline is approaching (within 24 hours)
     */
    public function isDeadlineApproaching()
    {
        return $this->deadline && now()->diffInHours($this->deadline) <= 24;
    }

    /**
     * Check if assignment is overdue
     */
    public function isOverdue()
    {
        return $this->deadline && now()->gt($this->deadline);
    }

    /**
     * Get submissions count
     */
    public function getSubmissionsCount()
    {
        return $this->submissions()->count();
    }

    /**
     * Get category badge color
     */
    public function getCategoryColor()
    {
        return match($this->category) {
            'web' => 'bg-blue-100 text-blue-800',
            'mobile' => 'bg-green-100 text-green-800',
            'desktop' => 'bg-purple-100 text-purple-800',
            'database' => 'bg-yellow-100 text-yellow-800',
            'ui_ux' => 'bg-pink-100 text-pink-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get difficulty badge color
     */
    public function getDifficultyColor()
    {
        return match($this->difficulty) {
            'easy' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'hard' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
