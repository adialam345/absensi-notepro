<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('tipe', ['izin', 'cuti']);
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_cutis');
    }
};
