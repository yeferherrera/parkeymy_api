<?php

namespace App\Http\Controllers;

use App\Models\Visitante;
use Illuminate\Http\Request;

class VisitanteController extends Controller
{
    public function index()
    {
        return response()->json(
            Visitante::with('registros')->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'documento' => 'required|string|max:50'
        ]);

        return response()->json(
            Visitante::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Visitante::with('registros')->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $visitante = Visitante::findOrFail($id);
        $visitante->update($request->all());

        return response()->json($visitante, 200);
    }

    public function destroy($id)
    {
        Visitante::findOrFail($id)->delete();
        return response()->json(['message' => 'Visitante eliminado'], 200);
    }
}
