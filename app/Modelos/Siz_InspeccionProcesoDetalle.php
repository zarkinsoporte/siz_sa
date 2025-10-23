<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_InspeccionProcesoDetalle extends Model
{
    protected $table = 'Siz_InspeccionProcesoDetalle';
    protected $primaryKey = 'IPD_id';
    public $timestamps = false;
    protected $fillable = [
        'IPD_iprId', 'IPD_chkId', 'IPD_estado', 'IPD_cantidad',
        'IPD_observacion', 'IPD_borrado', 'IPD_creadoEn', 'IPD_actualizadoEn'
    ];
}

