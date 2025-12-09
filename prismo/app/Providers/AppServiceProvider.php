<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register view composer for mitra badge counts
        View::composer([
            'mitra.antrian.antrian',
            'mitra.dashboard.dashboard',
            'mitra.saldo.saldo',
            'mitra.review.review'
        ], \App\View\Composers\MitraBadgeComposer::class);
    }
}
