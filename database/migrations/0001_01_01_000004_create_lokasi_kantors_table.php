<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_kantors', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->float('radius'); // dalam meter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_kantors');
    }
};
