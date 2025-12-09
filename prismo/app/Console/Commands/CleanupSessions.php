<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired sessions from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up expired sessions...');

        $deleted = DB::table(config('session.table'))
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->delete();

        $this->info("Deleted {$deleted} expired sessions.");

        return Command::SUCCESS;
    }
}
