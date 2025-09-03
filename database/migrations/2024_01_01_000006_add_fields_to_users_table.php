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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('aktif')->after('role');
            $table->foreignId('lokasi_kantor_id')->nullable()->after('status')->constrained('lokasi_kantors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lokasi_kantor_id']);
            $table->dropColumn(['status', 'lokasi_kantor_id']);
        });
    }
};
