<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pesan;
use App\Models\User;

class PesanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing pesan first
        Pesan::truncate();
        
        // Get admin user (pengirim)
        $admin = User::where('role', 'admin')->first();
        
        // Get karyawan users (penerima)
        $karyawan = User::where('role', 'karyawan')->get();
        
        if ($admin && $karyawan->count() > 0) {
            // Create test messages
            $pesanData = [
                [
                    'pengirim_id' => $admin->id,
                    'penerima_id' => $karyawan->first()->id,
                    'judul' => 'Pemberitahuan Meeting',
                    'pesan' => 'Halo, ada meeting penting besok jam 09:00 di ruang rapat. Mohon hadir tepat waktu.',
                    'tipe' => 'pemberitahuan',
                    'dibaca' => false,
                ],
                [
                    'pengirim_id' => $admin->id,
                    'penerima_id' => $karyawan->first()->id,
                    'judul' => 'Peringatan Absensi',
                    'pesan' => 'Mohon perhatikan jam kerja dan pastikan absensi tepat waktu. Terima kasih.',
                    'tipe' => 'peringatan',
                    'dibaca' => false,
                ],
                [
                    'pengirim_id' => $admin->id,
                    'penerima_id' => $karyawan->first()->id,
                    'judul' => 'Informasi Update Sistem',
                    'pesan' => 'Sistem absensi telah diupdate dengan fitur baru. Silakan coba fitur-fitur terbaru.',
                    'tipe' => 'info',
                    'dibaca' => true,
                    'dibaca_at' => now()->subHours(2),
                ]
            ];
            
            foreach ($pesanData as $data) {
                Pesan::create($data);
            }
            
            $this->command->info('Test pesan berhasil dibuat!');
            $this->command->info('Karyawan: ' . $karyawan->first()->name . ' (ID: ' . $karyawan->first()->id . ')');
            $this->command->info('Total pesan: ' . Pesan::count());
            $this->command->info('Pesan belum dibaca: ' . Pesan::where('penerima_id', $karyawan->first()->id)->where('dibaca', false)->count());
        } else {
            $this->command->error('Admin atau karyawan tidak ditemukan. Pastikan seeder User sudah dijalankan.');
        }
    }
}