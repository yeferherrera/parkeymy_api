<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoQr extends Model
{
    protected $table = 'codigos_qr';
    protected $primaryKey = 'id_qr';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'codigo_qr',
        'tipo_qr',
        'fecha_generacion',
        'fecha_expiracion',
        'cantidad_articulos',
        'estado_qr'
    ];

    // 🔹 QR pertenece a un usuario (aprendiz)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // 🔹 QR tiene muchos artículos (relación pivot)
    public function articulos()
    {
        return $this->belongsToMany(
            Articulo::class,
            'articulos_qr',
            'id_qr',
            'id_articulo'
        );
    }
}

