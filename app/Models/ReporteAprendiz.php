<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteAprendiz extends Model
{
    protected $table = 'reportes_aprendiz';
    protected $primaryKey = 'id_reporte';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'tipo_reporte',
        'titulo',
        'descripcion',
        'foto_url',
        'estado',
        'respuesta',
        'id_respondido_por',
        'fecha_reporte',
        'fecha_respuesta',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function respondidoPor()
    {
        return $this->belongsTo(Usuario::class, 'id_respondido_por', 'id_usuario');
    }
}