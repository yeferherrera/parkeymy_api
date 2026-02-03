<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    protected $table = 'visitantes';
    protected $primaryKey = 'id_visitante';
    public $timestamps = false;

    protected $fillable = ['nombre','documento'];

    public function registros() {
        return $this->hasMany(RegistroVisitante::class, 'id_visitante', 'id_visitante');
    }
}
