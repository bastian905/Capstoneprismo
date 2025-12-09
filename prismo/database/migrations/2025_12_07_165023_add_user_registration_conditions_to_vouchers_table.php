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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('registration_condition', ['none', 'less_than', 'greater_than'])->default('none')->after('max_usage_per_user');
            $table->integer('registration_days')->nullable()->after('registration_condition');
            $table->string('color')->default('#1c98f5')->after('registration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['registration_condition', 'registration_days', 'color']);
        });
    }
};
