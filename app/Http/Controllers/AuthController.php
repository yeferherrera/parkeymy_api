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
            'correo_institucional' => 'required|email',
            'password' => 'required|string'
        ]);

        $usuario = Usuario::where('correo_institucional', $request->correo_institucional)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        if ($usuario->estado !== 'activo') {
            return response()->json([
                'message' => 'Usuario inactivo'
            ], 403);
        }

        $token = $usuario->createToken('parkeymy_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'usuario' => $usuario->load('rol')
        ], 200);
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
