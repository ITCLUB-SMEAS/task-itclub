<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the team this member belongs to
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who is a member
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for members with leader role
     */
    public function scopeLeaders($query)
    {
        return $query->where('role', 'leader');
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
