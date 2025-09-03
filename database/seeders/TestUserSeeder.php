<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LokasiKantor;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create office location
        $lokasiKantor = LokasiKantor::firstOrCreate(
            ['nama_lokasi' => 'Kantor Test - Radius Besar'],
            [
                'alamat' => 'Lokasi Test dengan Radius Besar',
                'latitude' => -7.6528390,
                'longitude' => 111.5339200,
                'radius' => 500,
                'status' => 'aktif',
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'User Test',
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'jabatan' => 'Karyawan Test',
                'jam_kerja' => '08:00-17:00',
                'role' => 'karyawan',
                'lokasi_kantor_id' => $lokasiKantor->id,
            ]
        );
    }
}
