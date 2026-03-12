<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitanteController extends Controller
{
    // Listar visitantes activos y recientes
    public function index()
    {
        $visitantes = DB::table('visitantes')
            ->orderBy('fecha_registro', 'desc')
            ->get();

        // Actualizar expirados automáticamente
        DB::table('visitantes')
            ->where('estado', 'activo')
            ->where('fecha_expiracion', '<', now())
            ->update(['estado' => 'expirado']);

        return response()->json($visitantes);
    }

    // Ver detalle de un visitante con su último registro y objetos
    public function show($id)
    {
        $visitante = DB::table('visitantes')->where('id_visitante', $id)->first();

        if (!$visitante) {
            return response()->json(['message' => 'Visitante no encontrado'], 404);
        }

        $registro = DB::table('registro_visitantes')
            ->where('id_visitante', $id)
            ->orderBy('fecha_hora_ingreso', 'desc')
            ->first();

        $objetos = $registro
            ? DB::table('objetos_visitante')
                ->where('id_registro', $registro->id_registro_visitante)
                ->get()
            : [];

        return response()->json([
            'visitante' => $visitante,
            'registro'  => $registro,
            'objetos'   => $objetos,
        ]);
    }

    // Registrar nuevo visitante + crear registro de visita
    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento'   => 'required|string|max:10',
            'numero_documento' => 'required|string|max:20',
            'nombres'          => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'telefono'         => 'nullable|string|max:20',
            'empresa'          => 'nullable|string|max:100',
            'motivo_visita'    => 'required|string|max:255',
            'persona_visitar'  => 'nullable|string|max:100',
            'duracion_horas'   => 'required|numeric|min:0.5|max:24',
            'objetos'          => 'nullable|array',
            'objetos.*.nombre' => 'required|string|max:100',
            'objetos.*.descripcion' => 'nullable|string|max:255',
        ]);

        $fechaExpiracion = now()->addHours($request->duracion_horas);

        // Crear o actualizar visitante por documento
        $visitante = DB::table('visitantes')
            ->where('numero_documento', $request->numero_documento)
            ->first();

        if ($visitante) {
            DB::table('visitantes')
                ->where('id_visitante', $visitante->id_visitante)
                ->update([
                    'nombres'          => $request->nombres,
                    'apellidos'        => $request->apellidos,
                    'telefono'         => $request->telefono,
                    'empresa'          => $request->empresa,
                    'motivo_visita'    => $request->motivo_visita,
                    'persona_visitar'  => $request->persona_visitar,
                    'estado'           => 'activo',
                    'fecha_expiracion' => $fechaExpiracion,
                ]);
            $idVisitante = $visitante->id_visitante;
        } else {
            $idVisitante = DB::table('visitantes')->insertGetId([
                'tipo_documento'   => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'nombres'          => $request->nombres,
                'apellidos'        => $request->apellidos,
                'telefono'         => $request->telefono,
                'empresa'          => $request->empresa,
                'motivo_visita'    => $request->motivo_visita,
                'persona_visitar'  => $request->persona_visitar,
                'fecha_registro'   => now(),
                'estado'           => 'activo',
                'fecha_expiracion' => $fechaExpiracion,
            ]);
        }

        // Crear registro de visita
        $idRegistro = DB::table('registro_visitantes')->insertGetId([
            'id_visitante'          => $idVisitante,
            'id_vigilante_ingreso'  => $request->user()->id_usuario,
            'fecha_hora_ingreso'    => now(),
            'estado_visita'         => 'en_sede',
            'observaciones_ingreso' => $request->observaciones ?? null,
            'articulos_ingresados'  => null,
        ]);

        // Registrar objetos
        if ($request->objetos && count($request->objetos) > 0) {
            $objetos = array_map(fn($o) => [
                'id_registro'   => $idRegistro,
                'nombre'        => $o['nombre'],
                'descripcion'   => $o['descripcion'] ?? null,
                'fecha_registro' => now(),
            ], $request->objetos);

            DB::table('objetos_visitante')->insert($objetos);
        }

        return response()->json([
            'message'    => 'Visitante registrado correctamente',
            'visitante'  => DB::table('visitantes')->where('id_visitante', $idVisitante)->first(),
            'id_registro' => $idRegistro,
        ], 201);
    }

    // Registrar salida del visitante
    public function registrarSalida(Request $request, $id)
    {
        $registro = DB::table('registro_visitantes')
            ->where('id_visitante', $id)
            ->where('estado_visita', 'en_sede')
            ->orderBy('fecha_hora_ingreso', 'desc')
            ->first();

        if (!$registro) {
            return response()->json(['message' => 'No hay visita activa para este visitante'], 404);
        }

        DB::table('registro_visitantes')
            ->where('id_registro_visitante', $registro->id_registro_visitante)
            ->update([
                'fecha_hora_salida'      => now(),
                'estado_visita'          => 'finalizada',
                'id_vigilante_salida'    => $request->user()->id_usuario,
                'observaciones_salida'   => $request->observaciones ?? null,
            ]);

        DB::table('visitantes')
            ->where('id_visitante', $id)
            ->update(['estado' => 'inactivo']);

        return response()->json(['message' => 'Salida registrada correctamente']);
    }

    // Agregar objetos a visita activa
    public function agregarObjetos(Request $request, $id)
    {
        $request->validate([
            'objetos'          => 'required|array|min:1',
            'objetos.*.nombre' => 'required|string|max:100',
            'objetos.*.descripcion' => 'nullable|string|max:255',
        ]);

        $registro = DB::table('registro_visitantes')
            ->where('id_visitante', $id)
            ->where('estado_visita', 'activo')
            ->orderBy('fecha_hora_ingreso', 'desc')
            ->first();

        if (!$registro) {
            return response()->json(['message' => 'No hay visita activa para este visitante'], 404);
        }

        $objetos = array_map(fn($o) => [
            'id_registro'    => $registro->id_registro_visitante,
            'nombre'         => $o['nombre'],
            'descripcion'    => $o['descripcion'] ?? null,
            'fecha_registro' => now(),
        ], $request->objetos);

        DB::table('objetos_visitante')->insert($objetos);

        return response()->json([
            'message' => 'Objetos agregados correctamente',
            'objetos' => DB::table('objetos_visitante')
                ->where('id_registro', $registro->id_registro_visitante)
                ->get()
        ]);
    }
}