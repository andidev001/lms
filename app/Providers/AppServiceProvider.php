<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $activeTahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
            $view->with('activeTahunAjaran', $activeTahunAjaran);
        });
        
        \Illuminate\Support\Facades\View::composer('layouts.guru', function ($view) {
            $guru = \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->guru : null;
            $guru_mapels = $guru ? $guru->mapels()->with('kelas')->get() : collect();
            $view->with('guru_mapels', $guru_mapels);
        });
    }
}
