<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'kelas',
        'email',
        'github_link',
        'deskripsi_tugas',
        'tanggal_mengumpulkan',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_mengumpulkan' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
