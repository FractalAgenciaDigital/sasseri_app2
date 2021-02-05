<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    //
    protected $table = 'impresoras';
    protected $fillable = ['id','nombre_impresora','codigo'];
}
