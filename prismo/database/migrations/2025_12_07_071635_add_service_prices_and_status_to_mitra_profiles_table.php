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
            // Service package prices
            $table->decimal('basic_price', 10, 2)->default(0)->after('balance');
            $table->decimal('premium_price', 10, 2)->default(0)->after('basic_price');
            $table->decimal('complete_price', 10, 2)->default(0)->after('premium_price');
            
            // Operational status
            $table->boolean('is_open')->default(true)->after('complete_price');
            $table->time('closing_time')->nullable()->after('is_open');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->dropColumn(['basic_price', 'premium_price', 'complete_price', 'is_open', 'closing_time']);
        });
    }
};
