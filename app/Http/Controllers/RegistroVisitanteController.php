<?php

namespace App\Http\Controllers;

use App\Models\RegistroVisitante;
use Illuminate\Http\Request;

class RegistroVisitanteController extends Controller
{
    public function index()
    {
        return response()->json(
            RegistroVisitante::with('visitante')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_visitante' => 'required|exists:visitantes,id_visitante',
            'id_vigilante_ingreso' => 'required|exists:usuarios,id_usuario',
            'fecha_ingreso' => 'required|date'
        ]);

        return response()->json(
            RegistroVisitante::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            RegistroVisitante::with('visitante')->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $registro = RegistroVisitante::findOrFail($id);
        $registro->update($request->all());

        return response()->json($registro, 200);
    }

    public function destroy($id)
    {
        RegistroVisitante::findOrFail($id)->delete();
        return response()->json(['message' => 'Registro eliminado'], 200);
    }
}
