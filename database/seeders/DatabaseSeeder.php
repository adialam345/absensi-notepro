<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\LokasiKantor;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@absensi.com',
            'password' => Hash::make('admin123'),
            'jabatan' => 'Administrator',
            'jam_kerja' => '08:00-17:00',
            'role' => 'admin',
        ]);

        // Seeder lokasi kantor
        LokasiKantor::create([
            'nama' => 'Kantor Pusat',
            'alamat' => 'Jl. Contoh No.1',
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'radius' => 100,
        ]);
    }
}
