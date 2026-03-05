<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
    'id_rol',
    'tipo_documento',
    'numero_documento',
    'nombres',
    'apellidos',
    'correo_institucional',
    'telefono',
    'password_hash',
    'estado',
    'intentos_fallidos',
    'autenticacion_dos_pasos',
    'codigo_2fa',
    'codigo_2fa_expira',
    'fecha_registro',
    'ultimo_acceso',
];

    protected $hidden = ['password_hash'];

    public function rol() {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function articulos() {
        return $this->hasMany(Articulo::class, 'id_usuario', 'id_usuario');
    }

    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'id_usuario', 'id_usuario');
    }

    public function notificaciones() {
        return $this->hasMany(Notificacion::class, 'id_usuario', 'id_usuario');
    }
    
    public function codigosQr()
    {
        return $this->hasMany(CodigoQr::class, 'id_usuario', 'id_usuario');
    }
    
    public function AuditoriaSistema()
    {
        return $this->hasMany(AuditoriaSistema::class, 'id_usuario', 'id_usuario');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_usuario', 'id_usuario');
    }
    
    public function movimientosVigilante()
    {
        return $this->hasMany(Movimiento::class, 'id_vigilante', 'id_usuario');

    }

}
