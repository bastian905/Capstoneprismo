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
        Schema::table('bookings', function (Blueprint $table) {
            // Update status enum to include cek_transaksi
            $table->enum('status', ['cek_transaksi', 'menunggu', 'proses', 'selesai', 'dibatalkan'])->default('cek_transaksi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'dibatalkan'])->default('menunggu')->change();
        });
    }
};
