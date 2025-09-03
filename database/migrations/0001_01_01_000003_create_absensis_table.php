<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'terlambat'])->default('hadir');
            $table->string('lokasi_masuk')->nullable();
            $table->string('lokasi_pulang')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
