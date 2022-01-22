<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ACABADO extends Model
{
    protected $table = 'dbo.SIZ_Acabados';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['ACA_Eliminado', 'Arti', 'inval01_al0102', 'CODIDATO', 'DESCDATO', 'inval01_descripcion2', 'Surtir', 'idUser', 'FechaMov'];
}
