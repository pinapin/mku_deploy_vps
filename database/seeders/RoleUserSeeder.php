<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('1'),
            'role' => 'admin',
        ]);

        // Dosen user
        User::create([
            'name' => 'Dosen User',
            'email' => 'dosen@example.com',
            'username' => 'dosen',
            'password' => Hash::make('1'),
            'role' => 'dosen',
        ]);

        // Mahasiswa user
        User::create([
            'name' => 'Yanto Adi Pratama',
            'email' => 'yanto@example.com',
            'username' => '202001',
            'password' => Hash::make('1'),
            'role' => 'mahasiswa',
        ]);
    }
}
