<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    //ruta por defecto
    
    public const HOME = '/';

   //registrar las rutas de la aplicacion
    public function boot()
    {
        $this->routes(function () {

            // RUTAS API
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            // RUTAS WEB
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
