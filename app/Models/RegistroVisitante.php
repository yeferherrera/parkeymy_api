<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroVisitante extends Model
{
    protected $table = 'registro_visitantes';
    protected $primaryKey = 'id_registro';
    public $timestamps = false;

    protected $fillable = [
        'id_visitante','id_vigilante_ingreso','id_vigilante_salida',
        'fecha_ingreso','fecha_salida'
    ];

    public function visitante() {
        return $this->belongsTo(Visitante::class, 'id_visitante', 'id_visitante');
    }
}
