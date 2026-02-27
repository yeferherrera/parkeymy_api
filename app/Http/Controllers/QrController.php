<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoQr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Movimiento;
use App\Models\Articulo;
use App\Models\Notificacion;

class QrController extends Controller
{
    public function generar(Request $request)
    {
        $user = $request->user();

        // Validar si hay un qr existente
        $qrActivo = CodigoQr::where('id_usuario', $user->id_usuario)
            ->where('estado_qr', 'activo')
            ->where('fecha_expiracion', '>', now())
            ->exists();

        if ($qrActivo) {
            return response()->json([
                'message' => 'Ya tiene un QR activo'
            ], 400);
        }

        // Validar artículos
        $request->validate([
            'articulos' => 'required|array',
            'articulos.*' => 'exists:articulos,id_articulo',
            'tipo_movimiento' => 'required|in:ingreso,salida'
        ]);

        // Validar que un artículo no esté fuera
        foreach ($request->articulos as $idArticulo) {
            $articulo = Articulo::find($idArticulo);

            // Si es SALIDA → debe estar en_sede
            if ($request->tipo_movimiento === 'salida'
                && $articulo->estado_articulo !== 'en_sede') {
                return response()->json([
                    'message' => 'El artículo no está dentro del complejo'
                ], 400);
            }

            // Si es INGRESO → debe estar registrado o retirado
            if ($request->tipo_movimiento === 'ingreso'
                && !in_array($articulo->estado_articulo, ['registrado', 'retirado'])) {
                return response()->json([
                    'message' => 'El artículo ya está dentro o no puede ingresar'
                ], 400);
            }
        }

        // 1️⃣ Generar UUID
        $codigo = (string) Str::uuid();

        // 2️⃣ Guardar en base de datos
        $codigoQr = CodigoQr::create([
            'id_usuario' => $user->id_usuario,
            'codigo_qr' => $codigo,
            'tipo_movimiento' => $request->tipo_movimiento,
            'tipo_qr' => count($request->articulos) > 1 ? 'multiple' : 'individual',
            'fecha_generacion' => now(),
            'fecha_expiracion' => now()->addMinutes(1),
            'cantidad_articulos' => count($request->articulos),
            'estado_qr' => 'activo'
        ]);

        // 3️⃣ Insertar relación en tabla pivot
        foreach ($request->articulos as $idArticulo) {
            DB::table('articulos_qr')->insert([
                'id_articulo' => $idArticulo,
                'id_qr' => $codigoQr->id_qr
            ]);
        }

        // 4️⃣ Generar imagen QR
        $nombreArchivo = $codigo . '.svg';
        $ruta = public_path('qrcodes/' . $nombreArchivo);

        $qrImage = QrCode::format('svg')
            ->size(300)
            ->generate($codigo);

        file_put_contents($ruta, $qrImage);

        // 5️⃣ Crear notificación
        Notificacion::create([
            'id_usuario' => $user->id_usuario,
            'tipo_notificacion' => 'articulo',
            'titulo' => 'QR generado',
            'mensaje' => 'Tu QR de ' . $request->tipo_movimiento . ' fue generado y expira en 2 horas. No olvides hacer el QR de salida al retirarte.',
            'fecha_envio' => now(),
            'leida' => 0
        ]);

        // 6️⃣ Respuesta final
        return response()->json([
            'message' => 'Código QR generado correctamente',
            'qr_id' => $codigoQr->id_qr,
            'codigo_qr' => $codigo,
            'qr_url' => asset('qrcodes/' . $nombreArchivo)
        ]);
    }

    public function validar(Request $request, $codigo)
    {
        $vigilante = $request->user();

        $qr = CodigoQr::with('articulos')
            ->where('codigo_qr', $codigo)
            ->where('estado_qr', 'activo')
            ->where('fecha_expiracion', '>', now())
            ->first();

        if (!$qr) {
            return response()->json([
                'message' => 'QR inválido o expirado'
            ], 404);
        }

        // Cambiar estado de artículos según tipo
        foreach ($qr->articulos as $articulo) {
            if ($qr->tipo_movimiento === 'ingreso') {
                $articulo->update([
                    'estado_articulo' => 'en_sede'
                ]);
            }

            if ($qr->tipo_movimiento === 'salida') {
                $articulo->update([
                    'estado_articulo' => 'retirado'
                ]);
            }
        }

        // Registrar movimiento
        Movimiento::create([
            'id_usuario' => $qr->id_usuario,
            'id_qr' => $qr->id_qr,
            'tipo_movimiento' => $qr->tipo_movimiento,
            'fecha' => now(),
            'id_vigilante' => $vigilante->id_usuario
        ]);

        // Marcar QR como usado
        $qr->update([
            'estado_qr' => 'usado',
            'id_vigilante' => $vigilante->id_usuario,
            'fecha_validacion' => now()
        ]);

        // Crear notificación
        Notificacion::create([
            'id_usuario' => $qr->id_usuario,
            'tipo_notificacion' => 'articulo',
            'titulo' => $qr->tipo_movimiento === 'ingreso' ? '✅ Ingreso registrado' : '✅ Salida registrada',
            'mensaje' => 'Tu ' . $qr->tipo_movimiento . ' fue validado correctamente por el vigilante.',
            'fecha_envio' => now(),
            'leida' => 0
        ]);

        return response()->json([
            'message' => 'Movimiento registrado correctamente',
            'tipo' => $qr->tipo_movimiento
        ]);
    }

    public function fuera()
    {
        $articulos = Articulo::where('estado_articulo', 'retirado')->get();

        return response()->json($articulos);
    }
}