<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinCuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe',
        'status',
        'keterangan',
        'dokumen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
