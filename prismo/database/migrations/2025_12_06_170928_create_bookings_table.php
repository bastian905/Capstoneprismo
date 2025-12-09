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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
            
            // Detail booking
            $table->string('service_type');
            $table->string('vehicle_type');
            $table->string('vehicle_plate');
            $table->date('booking_date');
            $table->time('booking_time');
            
            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->unsignedBigInteger('voucher_id')->nullable();
            
            // Payment
            $table->string('payment_method');
            $table->string('payment_proof')->nullable();
            $table->enum('payment_status', ['pending', 'confirmed', 'failed'])->default('pending');
            
            // Status workflow
            $table->enum('status', ['menunggu', 'proses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('booking_code');
            $table->index(['customer_id', 'status']);
            $table->index(['mitra_id', 'booking_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
