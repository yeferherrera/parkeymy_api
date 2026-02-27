<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    // 📋 Listar todos los movimientos
    public function index()
    {
        $movimientos = Movimiento::with([
            'usuario',
            'codigoQr',
            'vigilante'
        ])->paginate(10);

        return response()->json($movimientos);
    }
    // 📋 Listar movimientos del usuario autenticado
    public function misMovimientos(Request $request)
{
    $movimientos = Movimiento::with(['codigoQr.articulos', 'vigilante'])
        ->where('id_usuario', $request->user()->id_usuario)
        ->orderBy('fecha', 'desc')
        ->paginate(15);

    return response()->json($movimientos);
}

    // ➕ Crear movimiento
    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_qr' => 'required|exists:codigos_qr,id_qr',
            'tipo_movimiento' => 'required|string',
            'fecha' => 'required|date',
            'id_vigilante' => 'required|exists:usuarios,id_usuario',
        ]);

        $movimiento = Movimiento::create($request->all());

        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'data' => $movimiento
        ], 201);
    }

    // 🔎 Ver uno
    public function show($id)
    {
        $movimiento = Movimiento::with([
            'usuario',
            'codigoQr',
            'vigilante'
        ])->findOrFail($id);

        return response()->json($movimiento);
    }

    // 🗑 Eliminar
    public function destroy($id)
    {
        $movimiento = Movimiento::findOrFail($id);
        $movimiento->delete();

        return response()->json([
            'message' => 'Movimiento eliminado'
        ]);
    }
}
