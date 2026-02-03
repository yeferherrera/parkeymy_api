<?php

namespace App\Http\Controllers;

use App\Models\Incidente;
use Illuminate\Http\Request;

class IncidenteController extends Controller
{
    public function index()
    {
        return response()->json(
            Incidente::with(['tipo','reporta','evidencias'])->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tipo_incidente' => 'required|exists:tipos_incidentes,id_tipo_incidente',
            'id_usuario_reporta' => 'required|exists:usuarios,id_usuario',
            'descripcion' => 'required|string'
        ]);

        return response()->json(
            Incidente::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Incidente::with(['tipo','reporta','evidencias'])->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $incidente = Incidente::findOrFail($id);
        $incidente->update($request->all());

        return response()->json($incidente, 200);
    }

    public function destroy($id)
    {
        Incidente::findOrFail($id)->delete();
        return response()->json(['message' => 'Incidente eliminado'], 200);
    }
}
