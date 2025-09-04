<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengirim_id'); // Admin yang mengirim
            $table->unsignedBigInteger('penerima_id'); // Karyawan yang menerima
            $table->string('judul');
            $table->text('pesan');
            $table->string('dokumen')->nullable(); // Path dokumen jika ada
            $table->enum('tipe', ['info', 'peringatan', 'pemberitahuan'])->default('info');
            $table->boolean('dibaca')->default(false);
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();
            
            $table->foreign('pengirim_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('penerima_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
