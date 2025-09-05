<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Absensi;
use App\Models\IzinCuti;
use App\Models\LokasiKantor;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $users = User::where('role', 'karyawan')->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No karyawan users found. Please create users first.');
            return;
        }

        // Get lokasi kantor
        $lokasiKantor = LokasiKantor::first();
        
        // Create dummy data from August to now
        $startDate = Carbon::create(2025, 8, 1); // August 1, 2025
        $endDate = Carbon::now();
        
        $this->command->info('Creating dummy attendance data from ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));
        
        // Create attendance data for each user
        foreach ($users as $user) {
            $this->createAttendanceData($user, $startDate, $endDate, $lokasiKantor);
            $this->createLeaveData($user, $startDate, $endDate);
        }
        
        $this->command->info('Dummy data created successfully!');
    }
    
    private function createAttendanceData($user, $startDate, $endDate, $lokasiKantor)
    {
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek != 0 && $currentDate->dayOfWeek != 6) {
                
                // Random status based on probability
                $statusRand = rand(1, 100);
                $status = 'hadir';
                $jamMasuk = '08:00:00';
                $jamPulang = '17:00:00';
                $dinasLuar = false;
                $alasanDinasLuar = null;
                $keterangan = null;
                
                if ($statusRand <= 5) {
                    // 5% chance of sakit
                    $status = 'sakit';
                    $jamMasuk = null;
                    $jamPulang = null;
                    $keterangan = 'Sakit demam';
                } elseif ($statusRand <= 10) {
                    // 5% chance of izin
                    $status = 'izin';
                    $jamMasuk = null;
                    $jamPulang = null;
                    $keterangan = 'Izin keperluan keluarga';
                } elseif ($statusRand <= 25) {
                    // 15% chance of terlambat
                    $status = 'terlambat';
                    $jamMasuk = Carbon::createFromTime(8, rand(15, 45), rand(0, 59))->format('H:i:s');
                    $keterangan = 'Terlambat karena macet';
                } elseif ($statusRand <= 30) {
                    // 5% chance of dinas luar
                    $status = 'hadir';
                    $dinasLuar = true;
                    $alasanDinasLuar = $this->getRandomDinasReason();
                    $jamMasuk = '08:00:00';
                    $jamPulang = '17:00:00';
                    $keterangan = 'Dinas luar kota';
                }
                
                // Create attendance record
                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $currentDate->format('Y-m-d'),
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'status' => $status,
                    'dinas_luar' => $dinasLuar,
                    'alasan_dinas_luar' => $alasanDinasLuar,
                    'keterangan' => $keterangan,
                    'lokasi_masuk' => $dinasLuar ? '-7.649831,111.5550397' : ($lokasiKantor ? $lokasiKantor->latitude . ',' . $lokasiKantor->longitude : null),
                    'lokasi_pulang' => $dinasLuar ? '-7.649831,111.5550397' : ($lokasiKantor ? $lokasiKantor->latitude . ',' . $lokasiKantor->longitude : null),
                ]);
            }
            
            $currentDate->addDay();
        }
    }
    
    private function createLeaveData($user, $startDate, $endDate)
    {
        // Create some leave requests
        $leaveTypes = ['cuti', 'izin', 'sakit'];
        $leaveStatuses = ['disetujui', 'ditolak', 'pending'];
        
        // Create 2-4 leave requests per user
        $numLeaves = rand(2, 4);
        
        for ($i = 0; $i < $numLeaves; $i++) {
            $leaveStart = $startDate->copy()->addDays(rand(0, 30));
            $leaveEnd = $leaveStart->copy()->addDays(rand(1, 3));
            
            // Make sure end date doesn't exceed current date
            if ($leaveEnd->gt($endDate)) {
                $leaveEnd = $endDate->copy();
            }
            
            $tipe = $leaveTypes[array_rand($leaveTypes)];
            $status = $leaveStatuses[array_rand($leaveStatuses)];
            
            IzinCuti::create([
                'user_id' => $user->id,
                'tanggal_mulai' => $leaveStart->format('Y-m-d'),
                'tanggal_selesai' => $leaveEnd->format('Y-m-d'),
                'tipe' => $tipe,
                'status' => $status,
                'keterangan' => $this->getRandomLeaveReason($tipe),
                'dokumen' => null,
            ]);
        }
    }
    
    private function getRandomDinasReason()
    {
        $reasons = [
            'Meeting dengan klien',
            'Survey lokasi proyek',
            'Kunjungan ke supplier',
            'Presentasi ke investor',
            'Koordinasi dengan tim lapangan',
            'Audit ke cabang',
            'Training eksternal',
            'Seminar industri',
            'Negosiasi kontrak',
            'Monitoring proyek'
        ];
        
        return $reasons[array_rand($reasons)];
    }
    
    private function getRandomLeaveReason($tipe)
    {
        $reasons = [
            'cuti' => [
                'Liburan keluarga',
                'Honeymoon',
                'Refreshing',
                'Kunjungan keluarga',
                'Wisata',
                'Istirahat panjang'
            ],
            'izin' => [
                'Keperluan keluarga',
                'Ke dokter',
                'Urusan pribadi',
                'Acara keluarga',
                'Ke bank',
                'Ke notaris',
                'Ke kantor pajak',
                'Ke sekolah anak'
            ],
            'sakit' => [
                'Demam tinggi',
                'Flu berat',
                'Sakit kepala',
                'Sakit perut',
                'Sakit gigi',
                'Kecelakaan ringan',
                'Kontrol rutin dokter',
                'Operasi kecil'
            ]
        ];
        
        $typeReasons = $reasons[$tipe] ?? $reasons['izin'];
        return $typeReasons[array_rand($typeReasons)];
    }
}
