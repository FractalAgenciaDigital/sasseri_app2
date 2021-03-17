<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleGasto extends Model
{
    protected $table = 'detalles_gastos';
    protected $fillable = [
        'detalle_gasto',
        'valor_gasto',
        'id_caja_cierre'
    ];
    public $timestamps = false;

}