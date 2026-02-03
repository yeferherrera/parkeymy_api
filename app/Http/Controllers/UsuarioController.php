<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(
            Usuario::with('rol')->get(),
            200
        );//formato json para devolver todo junto con un tiempo de respuesta 200 
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_rol' => 'required|exists:roles,id_rol',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios',
            'password' => 'required|string|min:6',
            'estado' => 'required',
            'role' => 'required|in:admin,vigilante,aprendiz'
        ]);

        $usuario = Usuario::create([
            'id_rol' => $request->id_rol,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'estado' => $request->estado,
            'role' => $request->role
        ]);

        return response()->json($usuario, 201);
    }

    public function show($id)
    {
        return response()->json(
            Usuario::with('rol')->findOrFail($id),
            200
        );
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'id_rol' => 'exists:roles,id_rol',
            'correo' => 'email|unique:usuarios,correo,' . $id . ',id_usuario'
        ]);

        if ($request->password) {
            $request->merge([
                'password' => Hash::make($request->password)
            ]);
        }

        $usuario->update($request->all());

        return response()->json($usuario, 200);
    }

    public function destroy($id)
    {
        Usuario::findOrFail($id)->delete();
        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
}
