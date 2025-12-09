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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
            
            // Review content
            $table->integer('rating');
            $table->text('comment');
            $table->json('review_photos')->nullable();
            
            // Mitra response
            $table->text('mitra_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('mitra_id');
            $table->index('rating');
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
