<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiKantor;

class LokasiKantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lokasiKantor = [
            [
                'nama_lokasi' => 'Kantor Pusat Jakarta',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'radius' => 100,
                'status' => 'aktif',
            ],
            [
                'nama_lokasi' => 'Kantor Cabang Surabaya',
                'alamat' => 'Jl. Tunjungan No. 45, Surabaya',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'radius' => 150,
                'status' => 'aktif',
            ],
            [
                'nama_lokasi' => 'Kantor Cabang Bandung',
                'alamat' => 'Jl. Asia Afrika No. 67, Bandung',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'radius' => 120,
                'status' => 'aktif',
            ],
        ];

        foreach ($lokasiKantor as $lokasi) {
            LokasiKantor::create($lokasi);
        }
    }
}
