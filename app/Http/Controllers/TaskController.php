<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Menampilkan form pengumpulan tugas (untuk student)
     */
    public function create()
    {
        return view('welcome');
    }

    /**
     * Menyimpan tugas yang dikumpulkan
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'github' => 'required|url|max:500',
            'deskripsi' => 'required|string|min:50|max:1000',
            'tanggal' => 'required|date',
        ], [
            'github.required' => 'Link GitHub wajib diisi.',
            'github.url' => 'Format URL GitHub tidak valid.',
            'deskripsi.required' => 'Deskripsi tugas wajib diisi.',
            'deskripsi.min' => 'Deskripsi tugas minimal 50 karakter.',
            'deskripsi.max' => 'Deskripsi tugas maksimal 1000 karakter.',
            'tanggal.required' => 'Tanggal mengumpulkan wajib diisi.',
        ]);

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Simpan tugas ke database
        Task::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'kelas' => $user->kelas ?? 'Tidak ada kelas',
            'email' => $user->email,
            'github_link' => $request->github,
            'deskripsi_tugas' => $request->deskripsi,
            'tanggal_mengumpulkan' => $request->tanggal,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan! Menunggu persetujuan admin.');
    }

    /**
     * Menampilkan semua tugas (untuk admin)
     */
    public function index()
    {
        $tasks = Task::with('user')->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Update status tugas (untuk admin)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        $task->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', 'Status tugas berhasil diupdate.');
    }

    /**
     * Menampilkan tugas milik user yang login (untuk student)
     */
    public function myTasks()
    {
        $tasks = Auth::user()->tasks()->latest()->get();
        return view('student.tasks.index', compact('tasks'));
    }
}
