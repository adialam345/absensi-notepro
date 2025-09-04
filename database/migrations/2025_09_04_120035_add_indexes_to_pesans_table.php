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
        Schema::table('pesans', function (Blueprint $table) {
            $table->index(['penerima_id', 'dibaca']);
            $table->index(['pengirim_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesans', function (Blueprint $table) {
            $table->dropIndex(['penerima_id', 'dibaca']);
            $table->dropIndex(['pengirim_id', 'created_at']);
            $table->dropIndex(['created_at']);
        });
    }
};
