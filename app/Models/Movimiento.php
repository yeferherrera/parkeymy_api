<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $primaryKey = 'id_movimiento';

    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'id_qr',
        'tipo_movimiento',
        'fecha',
        'id_vigilante'
    ];

  

    // Movimiento pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Movimiento pertenece a un código QR
    public function codigoQr()
    {
        return $this->belongsTo(CodigoQr::class, 'id_qr', 'id_qr');
    }

    // Movimiento pertenece a un vigilante
    public function vigilante()
    {
        return $this->belongsTo(Usuario::class, 'id_vigilante', 'id_usuario');
    }
}
