<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Articulo;
use App\Observers\ArticuloObserver;

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
         Articulo::observe(ArticuloObserver::class);
    }
}
