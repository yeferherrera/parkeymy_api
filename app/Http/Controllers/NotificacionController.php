<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    // Listar notificaciones del usuario autenticado
    public function index(Request $request)
    {
        $notificaciones = Notificacion::where('id_usuario', $request->user()->id_usuario)
            ->orderBy('fecha_envio', 'desc')
            ->get();

        return response()->json($notificaciones);
    }

    // Contar no leídas (para el badge)
    public function sinLeer(Request $request)
    {
        $count = Notificacion::where('id_usuario', $request->user()->id_usuario)
            ->where('leida', 0)
            ->count();

        return response()->json(['sin_leer' => $count]);
    }

    // Marcar una como leída
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update([
            'leida' => 1,
            'fecha_lectura' => now()
        ]);

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    // Marcar todas como leídas
    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::where('id_usuario', $request->user()->id_usuario)
            ->where('leida', 0)
            ->update([
                'leida' => 1,
                'fecha_lectura' => now()
            ]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}