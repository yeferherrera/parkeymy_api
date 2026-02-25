<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AuthController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
{
    $request->validate([
        'usuario' => 'required',
        'password' => 'required'
    ]);

    $usuarioInput = $request->usuario;

    // detectar si es correo o documento
    if (filter_var($usuarioInput, FILTER_VALIDATE_EMAIL)) {
        $usuario = Usuario::where('correo_institucional', $usuarioInput)->first();
    } else {
        $usuario = Usuario::where('numero_documento', $usuarioInput)->first();
    }

    if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
        return response()->json([
            'message' => 'Credenciales incorrectas'
        ], 401);
    }

    $token = $usuario->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'usuario' => $usuario
    ]);
}


    /**
     * PERFIL
     */
    public function perfil(Request $request)
    {
        return response()->json([
            'usuario' => $request->user()->load('rol')
        ]);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
