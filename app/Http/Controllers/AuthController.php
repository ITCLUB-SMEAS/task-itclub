<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Menampilkan halaman registrasi
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Menangani proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember-me');

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek apakah email sudah diverifikasi
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/student/dashboard');
            }
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak sesuai dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses registrasi
     */
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'kelas' => 'required|string|max:100',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'kelas.required' => 'Kelas wajib diisi.',
        ]);

        // Buat user baru dengan role student (default)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'kelas' => $request->kelas,
            'role' => 'student', // Default role untuk registrasi
            // email_verified_at akan di-set setelah verifikasi
        ]);

        // Kirim email verifikasi
        event(new \Illuminate\Auth\Events\Registered($user));

        // Login otomatis setelah registrasi
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    /**
     * Menangani logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Dashboard Admin
     */
    public function adminDashboard()
    {
        // Hitung statistik
        $pendingTasks = Task::where('status', 'pending')->count();
        $approvedTasks = Task::where('status', 'approved')->count();
        $rejectedTasks = Task::where('status', 'rejected')->count();

        // Cari siswa paling rajin berdasarkan tugas yang disetujui
        $topStudent = User::where('role', 'student')
            ->withCount(['tasks as approved_tasks_count' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('approved_tasks_count', 'desc')
            ->first();

        // Jika tidak ada siswa atau tidak ada tugas yang disetujui
        if (!$topStudent || $topStudent->approved_tasks_count == 0) {
            $topStudent = (object) [
                'name' => 'Belum ada data',
                'approved_tasks_count' => 0
            ];
        }

        // Ambil data tugas terbaru untuk tabel (maksimal 10 tugas terbaru)
        $recentTasks = Task::with('user')->latest()->limit(10)->get();

        return view('admin_dashboard', compact(
            'pendingTasks',
            'approvedTasks',
            'rejectedTasks',
            'topStudent',
            'recentTasks'
        ));
    }

    /**
     * Dashboard Student
     */
    public function studentDashboard()
    {
        $user = Auth::user();

        // Ambil statistik tugas untuk user yang login
        $pendingTasks = $user->tasks()->where('status', 'pending')->count();
        $approvedTasks = $user->tasks()->where('status', 'approved')->count();
        $rejectedTasks = $user->tasks()->where('status', 'rejected')->count();

        // Ambil riwayat tugas user dengan urutan terbaru
        $recentTasks = $user->tasks()->latest()->limit(5)->get();

        return view('student_dashboard', compact(
            'pendingTasks',
            'approvedTasks',
            'rejectedTasks',
            'recentTasks'
        ));
    }
}
