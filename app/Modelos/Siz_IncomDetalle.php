<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_IncomDetalle extends Model
{
    protected $table = 'Siz_IncomDetalle';
    protected $primaryKey = 'IND_id';
    public $timestamps = false;
    protected $fillable = [
        'IND_incId', 'IND_chkId', 'IND_estado', 'IND_observacion', 'IND_borrado', 'IND_creadoEn', 'IND_actualizadoEn', 'IND_cantidad'
    ];
} 