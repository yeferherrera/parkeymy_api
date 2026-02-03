<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoArticulo extends Model
{
    protected $table = 'fotos_articulos';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;

    protected $fillable = ['id_articulo','ruta'];

    public function articulo() {
        return $this->belongsTo(Articulo::class, 'id_articulo', 'id_articulo');
    }
}
