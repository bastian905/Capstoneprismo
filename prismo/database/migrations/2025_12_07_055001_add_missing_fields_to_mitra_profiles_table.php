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
            $table->year('establishment_year')->nullable()->after('business_name');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('map_location')->nullable()->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_profiles', function (Blueprint $table) {
            $table->dropColumn(['establishment_year', 'postal_code', 'map_location']);
        });
    }
};
