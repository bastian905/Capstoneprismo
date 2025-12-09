<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupInactiveAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:cleanup-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete customer accounts inactive for 3+ years and mitra accounts inactive for 3+ years';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threeYearsAgo = Carbon::now()->subYears(3);
        
        // Hapus customer yang tidak aktif selama 3 tahun
        $inactiveCustomers = User::where('role', 'customer')
            ->where(function($query) use ($threeYearsAgo) {
                $query->where('last_activity_at', '<', $threeYearsAgo)
                      ->orWhere(function($q) use ($threeYearsAgo) {
                          $q->whereNull('last_activity_at')
                            ->where('created_at', '<', $threeYearsAgo);
                      });
            })
            ->get();
        
        // Hapus mitra yang tidak aktif selama 3 tahun
        $inactiveMitra = User::where('role', 'mitra')
            ->where(function($query) use ($threeYearsAgo) {
                $query->where('last_activity_at', '<', $threeYearsAgo)
                      ->orWhere(function($q) use ($threeYearsAgo) {
                          $q->whereNull('last_activity_at')
                            ->where('created_at', '<', $threeYearsAgo);
                      });
            })
            ->get();
        
        $deletedCount = 0;
        
        foreach ($inactiveCustomers as $customer) {
            $this->info("Deleting customer: {$customer->email}");
            $customer->delete();
            $deletedCount++;
        }
        
        foreach ($inactiveMitra as $mitra) {
            $this->info("Deleting mitra: {$mitra->email}");
            $mitra->delete();
            $deletedCount++;
        }
        
        $this->info("Total deleted accounts: {$deletedCount}");
        
        return Command::SUCCESS;
    }
}
