<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VisitanteController;
use App\Http\Controllers\RegistroVisitanteController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\CodigoQrController;

//rutas publicas
Route::post('/login', [AuthController::class, 'login']);



//rutas protegidas con sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/generar-qr', [QrController::class, 'generar']);
    Route::get('/validar-qr/{codigo}', [QrController::class, 'validar']);
    Route::middleware('auth:sanctum')->post('/ingreso/{codigo}', [QrController::class, 'registrarIngreso']);
    Route::get('/articulos-fuera', [ArticuloController::class, 'fuera']);

    
    

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('roles', RolController::class);

    });
   
    Route::middleware(['auth:sanctum', 'role:admin,aprendiz'])->group(function () {

        Route::apiResource('articulos', ArticuloController::class);

    });
    
    Route::middleware(['auth:sanctum', 'role:admin,vigilante'])->group(function () {

        Route::apiResource('vehiculos', VehiculoController::class);
        Route::apiResource('visitantes', VisitanteController::class);
        Route::apiResource('registro-visitantes', RegistroVisitanteController::class);
        Route::apiResource('incidentes', IncidenteController::class);

    });


});
