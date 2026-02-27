<?php

namespace App\Observers;

use App\Models\Articulo;
use App\Models\AuditoriaSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ArticuloObserver
{

    private $datosAnteriores = null;

    public function created(Articulo $articulo)
    {
    

        AuditoriaSistema::create([
            'tabla_afectada' => 'articulos',
            'id_registro' => $articulo->id_articulo,
                'tipo_operacion' => 'INSERT',
            'id_usuario' => Auth::id() ?? $articulo->id_usuario,
            'datos_anteriores' => json_encode($this->datosAnteriores),
            'datos_nuevos' => json_encode($articulo->toArray()),
            'fecha_hora' => now(),
            'ip_address' => Request::ip()
        ]);
    }

    public function updating(Articulo $articulo)
    {
        // Guarda los datos ANTES de editar
        $this->datosAnteriores = $articulo->getOriginal();
    }

    public function updated(Articulo $articulo)
    {
        AuditoriaSistema::create([
            'tabla_afectada' => 'articulos',
            'id_registro' => $articulo->id_articulo,
            'tipo_operacion' => 'UPDATE',
            'id_usuario' => Auth::id() ?? $articulo->id_usuario,
            'datos_anteriores' => json_encode($this->datosAnteriores),
            'datos_nuevos' => json_encode($articulo->toArray()),
            'fecha_hora' => now(),
            'ip_address' => Request::ip()
        ]);
    }

    public function deleted(Articulo $articulo)
    {
        AuditoriaSistema::create([
            'tabla_afectada' => 'articulos',
            'id_registro' => $articulo->id_articulo,
            'tipo_operacion' => 'DELETE',
            'id_usuario' => Auth::id() ?? $articulo->id_usuario,
            'datos_anteriores' => json_encode($articulo->toArray()),
            'datos_nuevos' => null,
            'fecha_hora' => now(),
            'ip_address' => Request::ip()
        ]);
    }
}