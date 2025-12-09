<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\AccountDeletionWarning;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WarnInactiveAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:warn-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send warning emails to accounts that will be deleted in 1 month (inactive for 2 years 11 months)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 2 tahun 11 bulan yang lalu (1 bulan sebelum 3 tahun)
        $warningThreshold = Carbon::now()->subYears(2)->subMonths(11);
        
        // Cari user yang belum dikirim warning dan akan dihapus dalam 1 bulan
        $usersToWarn = User::whereIn('role', ['customer', 'mitra'])
            ->where('deletion_warning_sent', false)
            ->where(function($query) use ($warningThreshold) {
                $query->where('last_activity_at', '<', $warningThreshold)
                      ->orWhere(function($q) use ($warningThreshold) {
                          $q->whereNull('last_activity_at')
                            ->where('created_at', '<', $warningThreshold);
                      });
            })
            ->get();
        
        $sentCount = 0;
        
        foreach ($usersToWarn as $user) {
            try {
                Mail::to($user->email)->send(new AccountDeletionWarning($user));
                
                $user->deletion_warning_sent = true;
                $user->save();
                
                $this->info("Warning sent to: {$user->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send warning to {$user->email}: {$e->getMessage()}");
            }
        }
        
        $this->info("Total warnings sent: {$sentCount}");
        
        return Command::SUCCESS;
    }
}
