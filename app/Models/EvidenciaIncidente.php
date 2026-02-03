<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaIncidente extends Model
{
    protected $table = 'evidencias_incidentes';
    protected $primaryKey = 'id_evidencia';
    public $timestamps = false;

    protected $fillable = ['id_incidente','ruta'];

    public function incidente() {
        return $this->belongsTo(Incidente::class, 'id_incidente', 'id_incidente');
    }
}
