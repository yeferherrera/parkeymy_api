<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticuloQr extends Model
{
    protected $table = 'articulo_qr';
    public $timestamps = false;

    protected $fillable = [
        'id_qr',
        'id_articulo'
    ];
}
