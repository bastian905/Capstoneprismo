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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['discount', 'cashback', 'free_service'])->default('discount');
            
            // Discount details
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('discount_fixed', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_transaction', 10, 2)->default(0);
            
            // Validity
            $table->date('start_date')->nullable();
            $table->date('end_date');
            $table->integer('max_usage')->nullable();
            $table->integer('current_usage')->default(0);
            $table->integer('max_usage_per_user')->default(1);
            
            // Terms (JSON array)
            $table->json('terms')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
