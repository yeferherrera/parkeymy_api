<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipos_vehiculos';
    protected $primaryKey = 'id_tipo_vehiculo';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'id_tipo_vehiculo', 'id_tipo_vehiculo');
    }
}
