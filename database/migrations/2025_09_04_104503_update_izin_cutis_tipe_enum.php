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
        Schema::table('izin_cutis', function (Blueprint $table) {
            $table->enum('tipe', ['izin', 'cuti', 'sakit'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_cutis', function (Blueprint $table) {
            $table->enum('tipe', ['izin', 'cuti'])->change();
        });
    }
};
