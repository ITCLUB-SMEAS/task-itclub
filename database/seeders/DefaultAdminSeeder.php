<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat admin default untuk sistem
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@task.clubit.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin default telah dibuat!');
        $this->command->info('Email: admin@task.clubit.id');
        $this->command->info('Password: admin123');
    }
}
