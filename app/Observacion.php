<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'observaciones';
    //protected $primaryKey = 'id';
    protected $fillable = ['observacion','id_articulo','estado'];

}
