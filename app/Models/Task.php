<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignment_id',
        'nama_lengkap',
        'kelas',
        'email',
        'github_link',
        'file_uploads',
        'deskripsi_tugas',
        'category',
        'difficulty',
        'tanggal_mengumpulkan',
        'deadline',
        'status',
        'is_late',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_mengumpulkan' => 'date',
        'deadline' => 'datetime',
        'is_late' => 'boolean',
        'file_uploads' => 'array',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke TaskAssignment
     */
    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'assignment_id');
    }
}
