<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        return response()->json(
            Rol::with('permisos')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100'
        ]);

        return response()->json(
            Rol::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Rol::with('permisos')->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->all());

        return response()->json($rol, 200);
    }

    public function destroy($id)
    {
        Rol::findOrFail($id)->delete();
        return response()->json(['message' => 'Rol eliminado'], 200);
    }
}
