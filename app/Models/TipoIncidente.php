<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIncidente extends Model
{
    protected $table = 'tipos_incidentes';
    protected $primaryKey = 'id_tipo_incidente';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function incidentes() {
        return $this->hasMany(Incidente::class, 'id_tipo_incidente', 'id_tipo_incidente');
    }
}
