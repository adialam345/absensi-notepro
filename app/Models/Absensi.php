<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'foto_masuk',
        'foto_pulang',
        'status',
        'dinas_luar',
        'alasan_dinas_luar',
        'lokasi_masuk',
        'lokasi_pulang',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
