<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteAprendiz;
use App\Models\Notificacion;

class ReporteAprendizController extends Controller
{
    // Listar reportes del aprendiz autenticado
    public function misReportes(Request $request)
    {
        $reportes = ReporteAprendiz::with('respondidoPor')
            ->where('id_usuario', $request->user()->id_usuario)
            ->orderBy('fecha_reporte', 'desc')
            ->get();

        return response()->json($reportes);
    }

    // Ver detalle de un reporte
    public function show(Request $request, $id)
    {
        $reporte = ReporteAprendiz::with(['usuario', 'respondidoPor'])
            ->where('id_reporte', $id)
            ->where('id_usuario', $request->user()->id_usuario)
            ->firstOrFail();

        return response()->json($reporte);
    }

    // Crear reporte
    public function store(Request $request)
    {
        $request->validate([
            'tipo_reporte' => 'required|in:daño_articulo,perdida_articulo,incidente_sede',
            'titulo'       => 'required|string|max:150',
            'descripcion'  => 'required|string',
            'foto_url'     => 'nullable|string',
        ]);

        $reporte = ReporteAprendiz::create([
            'id_usuario'   => $request->user()->id_usuario,
            'tipo_reporte' => $request->tipo_reporte,
            'titulo'       => $request->titulo,
            'descripcion'  => $request->descripcion,
            'foto_url'     => $request->foto_url ?? null,
            'estado'       => 'pendiente',
            'fecha_reporte' => now(),
        ]);

        return response()->json([
            'message' => 'Reporte creado correctamente',
            'reporte' => $reporte
        ], 201);
    }

    // Responder reporte (admin o vigilante)
    public function responder(Request $request, $id)
    {
        $request->validate([
            'respuesta' => 'required|string',
            'estado'    => 'required|in:en_revision,resuelto',
        ]);

        $reporte = ReporteAprendiz::findOrFail($id);

        $reporte->update([
            'respuesta'         => $request->respuesta,
            'estado'            => $request->estado,
            'id_respondido_por' => $request->user()->id_usuario,
            'fecha_respuesta'   => now(),
        ]);

        // Notificar al aprendiz
        Notificacion::create([
            'id_usuario'         => $reporte->id_usuario,
            'tipo_notificacion'  => 'sistema',
            'titulo'             => '📋 Tu reporte fue respondido',
            'mensaje'            => 'Tu reporte "' . $reporte->titulo . '" tiene una nueva respuesta.',
            'fecha_envio'        => now(),
            'leida'              => 0,
        ]);

        return response()->json([
            'message' => 'Reporte respondido correctamente',
            'reporte' => $reporte->fresh()
        ]);
    }
}