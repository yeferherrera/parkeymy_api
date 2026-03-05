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
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ReporteAprendizController;
use App\Http\Controllers\ReporteVigilanciaController;


//rutas publicas
Route::post('/login', [AuthController::class, 'login']);



//rutas protegidas con sanctum

Route::middleware('auth:sanctum')->group(function () {
    
    
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::get('/notificaciones/sin-leer', [NotificacionController::class, 'sinLeer']);
    Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::post('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);
    Route::post('/solicitar-cambio-password', [AuthController::class, 'solicitarCambioPassword']);
    Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword']);
    
    
   //solo admin puede gestionar usuarios y roles
    Route::middleware('role:Administrador')->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('roles', RolController::class);
    });

    //admin y aprendiz 
    Route::middleware('role:Administrador,Aprendiz')->group(function () {
        Route::post('/generar-qr', [QrController::class, 'generar']);
        Route::apiResource('articulos', ArticuloController::class);
        Route::get('/mis-articulos', [ArticuloController::class, 'misArticulos']);
        Route::get('/mis-movimientos', [MovimientoController::class, 'misMovimientos']);
        Route::apiResource('movimientos', MovimientoController::class);
        Route::get('/mi-auditoria', [ArticuloController::class, 'miAuditoria']);
        Route::get('/mis-reportes', [ReporteAprendizController::class, 'misReportes']);
        Route::get('/reportes/{id}', [ReporteAprendizController::class, 'show']);
        Route::post('/reportes', [ReporteAprendizController::class, 'store']);
    });

   //admin y vigilante
     
    Route::middleware('role:Administrador,Vigilante')->group(function () {
        Route::post('/ingreso/{codigo}', [QrController::class, 'registrarIngreso']);
        Route::get('/qr/{codigo}/preview', [QrController::class, 'preview']);
        Route::get('/validar-qr/{codigo}', [QrController::class, 'validar']);
        Route::get('/articulos-fuera', [QrController::class, 'fuera']);
        Route::put('/reportes/{id}/responder', [ReporteAprendizController::class, 'responder']);
        Route::apiResource('registro-visitantes', RegistroVisitanteController::class);
        Route::get('/mis-reportes-vigilancia', [ReporteVigilanciaController::class, 'index']);
        Route::get('/reportes-vigilancia/{id}', [ReporteVigilanciaController::class, 'show']);
        Route::post('/reportes-vigilancia/calcular', [ReporteVigilanciaController::class, 'calcularTotales']);
        Route::post('/reportes-vigilancia', [ReporteVigilanciaController::class, 'store']);
});
        

        Route::apiResource('vehiculos', VehiculoController::class);
        Route::apiResource('visitantes', VisitanteController::class);
        Route::apiResource('incidentes', IncidenteController::class);
    });

