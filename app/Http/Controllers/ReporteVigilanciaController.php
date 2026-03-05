<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Articulo;
use Illuminate\Support\Facades\DB;

class ReporteVigilanciaController extends Controller
{
    // Listar reportes del vigilante autenticado
    public function index(Request $request)
    {
        $reportes = DB::table('reportes_vigilancia')
            ->where('id_vigilante', $request->user()->id_usuario)
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get();

        return response()->json($reportes);
    }

    // Ver detalle
    public function show(Request $request, $id)
    {
        $reporte = DB::table('reportes_vigilancia')
            ->where('id_reporte', $id)
            ->where('id_vigilante', $request->user()->id_usuario)
            ->first();

        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }

        return response()->json($reporte);
    }

    // Calcular totales automáticamente dado un rango de fechas
    public function calcularTotales(Request $request)
    {
        $request->validate([
            'fecha_hora_inicio' => 'required|date',
            'fecha_hora_fin'    => 'required|date|after:fecha_hora_inicio',
        ]);

        $inicio = $request->fecha_hora_inicio;
        $fin    = $request->fecha_hora_fin;

        $totalIngresos = DB::table('movimientos')
            ->where('tipo_movimiento', 'ingreso')
            ->whereBetween('fecha', [$inicio, $fin])
            ->count();

        $totalSalidas = DB::table('movimientos')
            ->where('tipo_movimiento', 'salida')
            ->whereBetween('fecha', [$inicio, $fin])
            ->count();

        $totalArticulos = DB::table('articulos')
            ->whereBetween('fecha_registro', [$inicio, $fin])
            ->count();

        return response()->json([
            'total_ingresos'               => $totalIngresos,
            'total_salidas'                => $totalSalidas,
            'total_articulos_registrados'  => $totalArticulos,
           
        ]);
    }

    // Crear reporte
    public function store(Request $request)
    {
        $request->validate([
            'fecha_hora_inicio'            => 'required|date',
            'fecha_hora_fin'               => 'required|date|after:fecha_hora_inicio',
            'total_ingresos'               => 'required|integer|min:0',
            'total_salidas'                => 'required|integer|min:0',
            'total_articulos_registrados'  => 'required|integer|min:0',
            
            'observaciones'                => 'nullable|string',
            'id_turno'                     => 'nullable|integer',
        ]);

        $id = DB::table('reportes_vigilancia')->insertGetId([
            'id_vigilante'                 => $request->user()->id_usuario,
            'id_turno'                     => $request->id_turno ?? null,
            'fecha_hora_inicio'            => $request->fecha_hora_inicio,
            'fecha_hora_fin'               => $request->fecha_hora_fin,
            'total_ingresos'               => $request->total_ingresos,
            'total_salidas'                => $request->total_salidas,
            'total_articulos_registrados'  => $request->total_articulos_registrados,
            'observaciones'                => $request->observaciones ?? null,
            'estado_reporte'               => 'enviado',
        ]);

        return response()->json([
            'message' => 'Reporte creado correctamente',
            'reporte' => DB::table('reportes_vigilancia')->where('id_reporte', $id)->first()
        ], 201);
    }
}