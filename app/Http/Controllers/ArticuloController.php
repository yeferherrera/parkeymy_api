<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user(); // Obtener el usuario autenticado


    // el admin puede ver todo
    if ($user->rol->nombre === 'admin') {
        return response()->json(
            Articulo::with(['usuario','categoria','fotos'])->get(),
            200
        );
    }

    // el aprendiz ve solo sus articulos
    return response()->json(
        Articulo::with(['usuario','categoria','fotos'])
            ->where('id_usuario', $user->id_usuario)
            ->get(),
        200
    );
}

    public function store(Request $request)
{
    $request->validate([
        'id_categoria' => 'required|exists:categorias_articulos,id_categoria',
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
    ]);

    $articulo = Articulo::create([
        'id_articulo' => uniqid('articulo_'),
        'id_usuario' => $request->user()->id_usuario,
        'id_categoria' => $request->id_categoria,
        'nombre_articulo' => $request->nombre,
        'descripcion' => $request->descripcion,
        'numero_serie' => $request->numero_serie,
        'marca' => $request->marca,
        'modelo' => $request->modelo,
        'color' => $request->color, 
        'fecha_registro' => now(),
        'observaciones' => $request->observaciones,
        'estado_articulo' => 'registrado'
    ]);

    return response()->json($articulo, 201);
}

    public function show($id)
    {
        return response()->json(
            Articulo::with(['usuario','categoria','fotos'])->findOrFail($id),
            200
        );
    }

    public function misArticulos(Request $request)
{
    $user = $request->user();

    $articulos = Articulo::with(['usuario','categoria','fotos'])
        ->where('id_usuario', $user->id_usuario)
        ->get();

    return response()->json($articulos, 200);
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
