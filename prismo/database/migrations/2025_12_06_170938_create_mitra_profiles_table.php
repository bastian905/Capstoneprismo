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
        Schema::create('mitra_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('contact_person');
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            
            // Jam operasional (JSON)
            $table->json('operational_hours')->nullable();
            $table->json('break_times')->nullable();
            
            // Dokumen (paths)
            $table->string('ktp_photo')->nullable();
            $table->string('qris_photo')->nullable();
            $table->string('legal_doc')->nullable();
            
            // Galeri (JSON array)
            $table->json('facility_photos')->nullable();
            
            // Rating & Stats
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_profiles');
    }
};
