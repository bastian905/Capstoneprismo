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
        Schema::table('users', function (Blueprint $table) {
            // Index untuk query berdasarkan email (login, verification)
            $table->index('email');
            
            // Index untuk query berdasarkan role
            $table->index('role');
            
            // Index untuk query last activity (inactive accounts)
            $table->index('last_activity_at');
            
            // Composite index untuk query berdasarkan role dan approval_status
            $table->index(['role', 'approval_status']);
            
            // Index untuk deletion warning query
            $table->index(['deletion_warning_sent', 'last_activity_at']);
        });
        
        // Index untuk tabel lain jika ada (booking, payment, dll)
        // Schema::table('bookings', function (Blueprint $table) {
        //     $table->index('user_id');
        //     $table->index('status');
        //     $table->index('booking_date');
        //     $table->index(['user_id', 'status']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_email_index']);
            $table->dropIndex(['users_role_index']);
            $table->dropIndex(['users_last_activity_at_index']);
            $table->dropIndex(['users_role_approval_status_index']);
            $table->dropIndex(['users_deletion_warning_sent_last_activity_at_index']);
        });
    }
};
