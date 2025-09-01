<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user student
        $students = User::where('role', 'student')->get();

        if ($students->count() > 0) {
            // Contoh tugas untuk Budi Santoso
            $budi = $students->where('email', 'student@task.clubit.id')->first();
            if ($budi) {
                Task::create([
                    'user_id' => $budi->id,
                    'nama_lengkap' => $budi->name,
                    'kelas' => '12 RPL 1',
                    'email' => $budi->email,
                    'github_link' => 'https://github.com/budisantoso/project-laravel',
                    'tanggal_mengumpulkan' => now()->subDays(3),
                    'status' => 'approved'
                ]);

                Task::create([
                    'user_id' => $budi->id,
                    'nama_lengkap' => $budi->name,
                    'kelas' => '12 RPL 1',
                    'email' => $budi->email,
                    'github_link' => 'https://github.com/budisantoso/project-react',
                    'tanggal_mengumpulkan' => now()->subDays(1),
                    'status' => 'pending'
                ]);
            }

            // Contoh tugas untuk Siti Nurhaliza
            $siti = $students->where('email', 'siti@student.com')->first();
            if ($siti) {
                Task::create([
                    'user_id' => $siti->id,
                    'nama_lengkap' => $siti->name,
                    'kelas' => '12 RPL 2',
                    'email' => $siti->email,
                    'github_link' => 'https://github.com/sitinur/web-portfolio',
                    'tanggal_mengumpulkan' => now()->subDays(5),
                    'status' => 'approved'
                ]);

                Task::create([
                    'user_id' => $siti->id,
                    'nama_lengkap' => $siti->name,
                    'kelas' => '12 RPL 2',
                    'email' => $siti->email,
                    'github_link' => 'https://github.com/sitinur/crud-php',
                    'tanggal_mengumpulkan' => now()->subDays(4),
                    'status' => 'approved'
                ]);

                Task::create([
                    'user_id' => $siti->id,
                    'nama_lengkap' => $siti->name,
                    'kelas' => '12 RPL 2',
                    'email' => $siti->email,
                    'github_link' => 'https://github.com/sitinur/mobile-app',
                    'tanggal_mengumpulkan' => now()->subDays(2),
                    'status' => 'approved'
                ]);

                Task::create([
                    'user_id' => $siti->id,
                    'nama_lengkap' => $siti->name,
                    'kelas' => '12 RPL 2',
                    'email' => $siti->email,
                    'github_link' => 'https://github.com/sitinur/api-rest',
                    'tanggal_mengumpulkan' => now(),
                    'status' => 'rejected',
                    'catatan_admin' => 'API documentation tidak lengkap. Mohon diperbaiki.'
                ]);
            }

            // Contoh tugas untuk Ahmad Rizki
            $ahmad = $students->where('email', 'ahmad@student.com')->first();
            if ($ahmad) {
                Task::create([
                    'user_id' => $ahmad->id,
                    'nama_lengkap' => $ahmad->name,
                    'kelas' => '12 TKJ 1',
                    'email' => $ahmad->email,
                    'github_link' => 'https://github.com/ahmadrizki/network-config',
                    'tanggal_mengumpulkan' => now()->subDays(2),
                    'status' => 'pending'
                ]);
            }
        }
    }
}
