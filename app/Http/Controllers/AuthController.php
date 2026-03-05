<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required',
            'password' => 'required'
        ]);

        $usuarioInput = $request->usuario;

        if (filter_var($usuarioInput, FILTER_VALIDATE_EMAIL)) {
            $usuario = Usuario::where('correo_institucional', $usuarioInput)->first();
        } else {
            $usuario = Usuario::where('numero_documento', $usuarioInput)->first();
        }

        if (!$usuario || !Hash::check($request->password, $usuario->password_hash)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $usuario->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => $usuario
        ]);
    }

    public function perfil(Request $request)
    {
        return response()->json([
            'usuario' => $request->user()->load('rol')
        ]);
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = $request->user();

        $request->validate([
            'nombres'    => 'sometimes|string|max:100',
            'apellidos'  => 'sometimes|string|max:100',
            'telefono'   => 'sometimes|string|max:20',
            'correo_institucional' => 'sometimes|email|unique:usuarios,correo_institucional,' . $usuario->id_usuario . ',id_usuario',
        ]);

        $usuario->update($request->only(['nombres', 'apellidos', 'telefono', 'correo_institucional']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'usuario' => $usuario->fresh()->load('rol')
        ]);
    }

    public function solicitarCambioPassword(Request $request)
    {
        $usuario = $request->user();

        // Generar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar en BD con expiración de 10 minutos
        $usuario->update([
            'codigo_2fa'        => $codigo,
            'codigo_2fa_expira' => now()->addMinutes(10),
        ]);

        // Enviar correo
        Mail::send([], [], function ($message) use ($usuario, $codigo) {
            $message->to($usuario->correo_institucional)
                ->subject('ParkeyMY — Código de verificación')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto;'>
                        <div style='background: #004C97; padding: 24px; border-radius: 12px 12px 0 0; text-align: center;'>
                            <h1 style='color: #fff; margin: 0; font-size: 24px;'>ParkeyMY</h1>
                        </div>
                        <div style='background: #F8FAFC; padding: 32px; border-radius: 0 0 12px 12px; border: 1px solid #E5E7EB;'>
                            <h2 style='color: #111827; margin-top: 0;'>Código de verificación</h2>
                            <p style='color: #6B7280;'>Usa este código para cambiar tu contraseña. Expira en <strong>10 minutos</strong>.</p>
                            <div style='background: #fff; border: 2px dashed #004C97; border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0;'>
                                <span style='font-size: 42px; font-weight: 800; letter-spacing: 12px; color: #004C97;'>{$codigo}</span>
                            </div>
                            <p style='color: #9CA3AF; font-size: 13px;'>Si no solicitaste este cambio, ignora este correo.</p>
                        </div>
                    </div>
                ");
        });

        return response()->json([
            'message' => 'Código enviado a tu correo institucional'
        ]);
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'codigo'           => 'required|string|size:6',
            'nueva_password'   => 'required|string|min:8|confirmed',
        ]);

        $usuario = $request->user();

        // Verificar código
        if ($usuario->codigo_2fa !== $request->codigo) {
            return response()->json(['message' => 'Código incorrecto'], 422);
        }

        // Verificar expiración
        if (now()->isAfter($usuario->codigo_2fa_expira)) {
            return response()->json(['message' => 'El código ha expirado'], 422);
        }

        // Cambiar contraseña y limpiar código
        $usuario->update([
            'password_hash'     => Hash::make($request->nueva_password),
            'codigo_2fa'        => null,
            'codigo_2fa_expira' => null,
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}