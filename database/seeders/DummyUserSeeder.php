<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LokasiKantor;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create lokasi kantor
        $lokasiKantor = LokasiKantor::first();
        if (!$lokasiKantor) {
            $lokasiKantor = LokasiKantor::create([
                'nama' => 'Kantor Pusat',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius' => 100,
            ]);
        }

        // Create dummy karyawan users
        $karyawanData = [
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@company.com',
                'nip' => 'EMP001',
                'jabatan' => 'Software Developer',
                'divisi' => 'IT',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@company.com',
                'nip' => 'EMP002',
                'jabatan' => 'HR Manager',
                'divisi' => 'Human Resources',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@company.com',
                'nip' => 'EMP003',
                'jabatan' => 'Marketing Executive',
                'divisi' => 'Marketing',
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'dewi.kartika@company.com',
                'nip' => 'EMP004',
                'jabatan' => 'Finance Analyst',
                'divisi' => 'Finance',
            ],
            [
                'name' => 'Rizki Pratama',
                'email' => 'rizki.pratama@company.com',
                'nip' => 'EMP005',
                'jabatan' => 'Sales Manager',
                'divisi' => 'Sales',
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@company.com',
                'nip' => 'EMP006',
                'jabatan' => 'Graphic Designer',
                'divisi' => 'Creative',
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@company.com',
                'nip' => 'EMP007',
                'jabatan' => 'Project Manager',
                'divisi' => 'Operations',
            ],
            [
                'name' => 'Indah Permata',
                'email' => 'indah.permata@company.com',
                'nip' => 'EMP008',
                'jabatan' => 'Customer Service',
                'divisi' => 'Customer Care',
            ],
        ];

        foreach ($karyawanData as $data) {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'status' => 'aktif',
                'nip' => $data['nip'],
                'jabatan' => $data['jabatan'],
                'divisi' => $data['divisi'],
                'lokasi_kantor_id' => $lokasiKantor->id,
                'foto' => null,
            ]);
        }

        $this->command->info('Dummy users created successfully!');
    }
}
