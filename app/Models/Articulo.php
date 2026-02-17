<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulos';
    protected $primaryKey = 'id_articulo';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario','id_categoria','nombre','descripcion','estado_articulo'
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function categoria() {
        return $this->belongsTo(CategoriaArticulo::class, 'id_categoria', 'id_categoria');
    }

    public function fotos() {
        return $this->hasMany(FotoArticulo::class, 'id_articulo', 'id_articulo');
    }

    public function movimientos() {
        return $this->hasMany(MovimientoArticulo::class, 'id_articulo', 'id_articulo');
    }

    public function articuloqr() {
        return $this->hasOne(ArticuloQr::class, 'id_articulo', 'id_articulo');
    }

    public function codigosQr()
{
    return $this->belongsToMany(
        CodigoQr::class,
        'articulo_qr',
        'id_articulo',
        'id_qr'
    );
}

}
