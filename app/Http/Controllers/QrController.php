<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodigoQr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QrController extends Controller
{
    public function generar(Request $request)
    {
        $user = $request->user();

        // Validar artículos
        $request->validate([
            'articulos' => 'required|array',
            'articulos.*' => 'exists:articulos,id_articulo'
        ]);

        // 1️⃣ Generar UUID
        $codigo = (string) Str::uuid();

        // 2️⃣ Guardar en base de datos
        $codigoQr = CodigoQr::create([
            'id_usuario' => $user->id_usuario,
            'codigo_qr' => $codigo,
            'tipo_qr' => count($request->articulos) > 1 ? 'multiple' : 'individual',
            'fecha_generacion' => now(),
            'fecha_expiracion' => now()->addHours(2),
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

        // 4️⃣ Generar imagen QR usando GD (SIN IMAGICK)
        $nombreArchivo = $codigo . '.svg';
        $ruta = public_path('qrcodes/' . $nombreArchivo);

       $qrImage = QrCode::format('svg')
        ->size(300)
         ->generate($codigo);

        file_put_contents($ruta, $qrImage);

        // 5️⃣ Respuesta final
        return response()->json([
            'message' => 'Código QR generado correctamente',
            'qr_id' => $codigoQr->id_qr,
            'codigo_qr' => $codigo,
            'qr_url' => asset('qrcodes/' . $nombreArchivo)
        ]);
    }

   public function validar($codigo)
{
    $qr = CodigoQr::with(['usuario', 'articulos'])
        ->where('codigo_qr', $codigo)
        ->where('estado_qr', 'activo')
        ->where('fecha_expiracion', '>', now())
        ->first();

    if (!$qr) {
        return response()->json([
            'message' => 'QR inválido o expirado'
        ], 404);
    }

    //  Marcar como usado
    $qr->estado_qr = 'usado';
    $qr->save();

    return response()->json([
        'message' => 'QR válido y marcado como usado',
        'data' => $qr
    ]);
}

}
