<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaSistema extends Model
{
    protected $table = 'auditoria_sistema';
    protected $primaryKey = 'id_auditoria';
    public $timestamps = false;

    protected $fillable = [
        'tabla_afectada',
        'id_registro',
        'tipo_operacion',
        'id_usuario',
        'datos_anteriores',
        'datos_nuevos',
        'fecha_hora',
        'ip_address'
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    
}