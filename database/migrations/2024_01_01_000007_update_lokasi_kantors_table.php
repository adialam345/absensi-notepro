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
        Schema::table('lokasi_kantors', function (Blueprint $table) {
            // Rename 'nama' to 'nama_lokasi'
            $table->renameColumn('nama', 'nama_lokasi');
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('lokasi_kantors', 'status')) {
                $table->string('status')->default('aktif')->after('radius');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lokasi_kantors', function (Blueprint $table) {
            // Rename back 'nama_lokasi' to 'nama'
            $table->renameColumn('nama_lokasi', 'nama');
            
            // Drop status column if it exists
            if (Schema::hasColumn('lokasi_kantors', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
