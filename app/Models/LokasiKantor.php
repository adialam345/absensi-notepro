<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kantors';

    protected $fillable = [
        'nama_lokasi',
        'alamat',
        'latitude',
        'longitude',
        'radius',
        'status',
    ];

    /**
     * Get the users for the lokasi kantor.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the absensi for the lokasi kantor.
     */
    public function absensi()
    {
        return $this->hasManyThrough(Absensi::class, User::class);
    }
}
