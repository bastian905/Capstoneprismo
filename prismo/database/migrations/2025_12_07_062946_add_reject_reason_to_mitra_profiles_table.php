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
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->text('reject_reason')->nullable()->after('qris_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
    }
};
