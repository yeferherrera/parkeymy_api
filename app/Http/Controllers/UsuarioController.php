<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    //  LISTAR
    public function index()
    {
        return response()->json(
            Usuario::with('rol')->get(),
            200
        );
    }

    //  CREAR USUARIO
    public function store(Request $request)
    {
        $request->validate([
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|string|max:20|unique:usuarios,numero_documento',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo_institucional' => 'required|email|unique:usuarios,correo_institucional',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'id_rol' => 'required|exists:roles,id_rol',
            'estado' => 'required|string'
        ]);

        $usuario = Usuario::create([
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'correo_institucional' => $request->correo_institucional,
            'telefono' => $request->telefono,
            'password_hash' => Hash::make($request->password),
            'id_rol' => $request->id_rol,
            'fecha_registro' => now(),
            'estado' => $request->estado,
            'intentos_fallidos' => 0,
            'autenticacion_dos_pasos' => 0
        ]);

        return response()->json($usuario, 201);
    }

    // 📌 MOSTRAR UNO
    public function show($id)
    {
        return response()->json(
            Usuario::with('rol')->findOrFail($id),
            200
        );
    }

    // 📌 ACTUALIZAR
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'tipo_documento' => 'sometimes|string|max:10',
            'numero_documento' => 'sometimes|string|max:20|unique:usuarios,numero_documento,' . $id . ',id_usuario',
            'correo_institucional' => 'sometimes|email|unique:usuarios,correo_institucional,' . $id . ',id_usuario',
            'id_rol' => 'sometimes|exists:roles,id_rol'
        ]);

        $data = $request->all();

        //  Si envían password → hashear
        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return response()->json($usuario, 200);
    }

    //  ELIMINAR
    public function destroy($id)
    {
        Usuario::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Usuario eliminado'
        ], 200);
    }
}
