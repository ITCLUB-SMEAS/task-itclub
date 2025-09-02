<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'leader_id',
        'class_group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the leader of the team
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the members of the team
     */
    public function members()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get all users who are members of this team
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get the assignments submitted by this team
     */
    public function assignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }
}
