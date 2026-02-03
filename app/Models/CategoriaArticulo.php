<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaArticulo extends Model
{
    protected $table = 'categorias_articulos';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function articulos() {
        return $this->hasMany(Articulo::class, 'id_categoria', 'id_categoria');
    }
}
