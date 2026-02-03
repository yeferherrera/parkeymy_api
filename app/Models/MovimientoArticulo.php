<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoArticulo extends Model
{
    protected $table = 'movimientos_articulos';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_articulo','id_usuario_propietario','id_vigilante','id_qr','tipo','fecha'
    ];

    public function articulo() {
        return $this->belongsTo(Articulo::class, 'id_articulo', 'id_articulo');
    }

    public function propietario() {
        return $this->belongsTo(Usuario::class, 'id_usuario_propietario', 'id_usuario');
    }

    public function vigilante() {
        return $this->belongsTo(Usuario::class, 'id_vigilante', 'id_usuario');
    }
}
