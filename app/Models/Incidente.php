<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    protected $table = 'incidentes';
    protected $primaryKey = 'id_incidente';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_incidente','id_usuario_reporta','id_usuario_resuelve',
        'id_articulo','id_vehiculo','descripcion','estado'
    ];

    public function tipo() {
        return $this->belongsTo(TipoIncidente::class, 'id_tipo_incidente', 'id_tipo_incidente');
    }

    public function reporta() {
        return $this->belongsTo(Usuario::class, 'id_usuario_reporta', 'id_usuario');
    }

    public function evidencias() {
        return $this->hasMany(EvidenciaIncidente::class, 'id_incidente', 'id_incidente');
    }
}
