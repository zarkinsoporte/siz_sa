<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_InspeccionProceso extends Model
{
    protected $table = 'Siz_InspeccionProceso';
    protected $primaryKey = 'IPR_id';
    public $timestamps = false;
    protected $fillable = [
        'IPR_op', 'IPR_docEntry', 'IPR_codArticulo', 'IPR_nomArticulo', 
        'IPR_cantPlaneada', 'IPR_cantInspeccionada', 'IPR_cantRechazada',
        'IPR_centroInspeccion', 'IPR_nombreCentro', 'IPR_fechaInspeccion',
        'IPR_codInspector', 'IPR_nomInspector', 'IPR_observaciones',
        'IPR_borrado', 'IPR_creadoEn', 'IPR_actualizadoEn'
    ];
}

