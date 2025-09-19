<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_IncomRechazo extends Model
{
    protected $table = 'Siz_IncomRechazos';
    protected $primaryKey = 'IR_id';
    public $timestamps = false;
    //[IR_id]
    // ,[IR_INC_incomld]
    // ,[IR_codigoMaterial]
    // ,[IR_cantidadRechazada]
    // ,[IR_FechaReporte]
    // ,[IR_codigoInspector]
    // ,[IR_notasGenerales]
    // ,[IR_GeneroDevolucion]
    // ,[IR_NumDevolucion]
    // ,[IR_Eliminado]
    protected $fillable = [
        'IR_INC_incomld', 'IR_codigoMaterial', 'IR_cantidadRechazada', 'IR_FechaReporte', 'IR_codigoInspector', 'IR_notasGenerales', 'IR_GeneroDevolucion', 'IR_NumDevolucion', 'IR_Eliminado'
    ];
} 