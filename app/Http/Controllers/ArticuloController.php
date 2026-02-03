<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index()
    {
        return response()->json(
            Articulo::with(['usuario','categoria','fotos'])->get(),
            200
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_categoria' => 'required|exists:categorias_articulos,id_categoria',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required'
        ]);

        return response()->json(
            Articulo::create($request->all()),
            201
        );
    }

    public function show($id)
    {
        return response()->json(
            Articulo::with(['usuario','categoria','fotos'])->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->update($request->all());

        return response()->json($articulo, 200);
    }

    public function destroy($id)
    {
        Articulo::findOrFail($id)->delete();
        return response()->json(['message' => 'Artículo eliminado'], 200);
    }
}
