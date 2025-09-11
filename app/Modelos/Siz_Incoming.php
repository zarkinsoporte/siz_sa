<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_Incoming extends Model
{
    protected $table = 'Siz_Incoming';
    protected $primaryKey = 'INC_id';
    public $timestamps = false;
    protected $fillable = [
        'INC_docNum', 'INC_fechaRecepcion', 'INC_codProveedor', 'INC_nomProveedor',
        'INC_numFactura', 'INC_codMaterial', 'INC_nomMaterial', 'INC_unidadMedida',
        'INC_cantRecibida', 'INC_cantAceptada', 'INC_cantRechazada', 'INC_fechaInspeccion',
        'INC_codInspector', 'INC_nomInspector', 'INC_notas', 'INC_esPiel', 'INC_borrado',
        'INC_quienBorro', 'INC_creadoEn', 'INC_actualizadoEn', 'INC_lote', 'INC_lineNum'
    ];
} 