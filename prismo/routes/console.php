<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule untuk mengirim warning email setiap hari pukul 01:00
Schedule::command('accounts:warn-inactive')->dailyAt('01:00');

// Schedule untuk cleanup akun tidak aktif setiap hari Senin pukul 02:00
Schedule::command('accounts:cleanup-inactive')->weekly()->mondays()->at('02:00');
