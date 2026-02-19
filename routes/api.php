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


//rutas publicas
Route::post('/login', [AuthController::class, 'login']);



//rutas protegidas con sanctum

Route::middleware('auth:sanctum')->group(function () {
    
    
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    
   //solo admin puede gestionar usuarios y roles
    Route::middleware('role:Administrador')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('roles', RolController::class);
    });

    //admin y aprendiz 
    Route::middleware('role:Administrador,Aprendiz')->group(function () {
        Route::post('/generar-qr', [QrController::class, 'generar']);
        Route::apiResource('articulos', ArticuloController::class);
    });

   //admin y vigilante
     
    Route::middleware('role:Administrador,Vigilante')->group(function () {
        Route::post('/ingreso/{codigo}', [QrController::class, 'registrarIngreso']);
        Route::get('/validar-qr/{codigo}', [QrController::class, 'validar']);
        Route::get('/articulos-fuera', [ArticuloController::class, 'fuera']);

        Route::apiResource('vehiculos', VehiculoController::class);
        Route::apiResource('visitantes', VisitanteController::class);
        Route::apiResource('registro-visitantes', RegistroVisitanteController::class);
        Route::apiResource('incidentes', IncidenteController::class);
    });

});