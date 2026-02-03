<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        return response()->json(
            Vehiculo::with(['usuario','tipo'])->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_tipo_vehiculo' => 'required|exists:tipos_vehiculos,id_tipo_vehiculo',
            'placa' => 'required|string|max:20'
        ]);

        return response()->json(
            Vehiculo::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Vehiculo::with(['usuario','tipo'])->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->update($request->all());

        return response()->json($vehiculo, 200);
    }

    public function destroy($id)
    {
        Vehiculo::findOrFail($id)->delete();
        return response()->json(['message' => 'Vehículo eliminado'], 200);
    }
}
