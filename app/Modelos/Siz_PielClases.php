<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_PielClases extends Model
{
    protected $table = 'Siz_PielClases';
    protected $primaryKey = 'PLC_id';
    public $timestamps = false;
    protected $fillable = [
        'PLC_incId', 'PLC_claseA', 'PLC_claseB', 'PLC_claseC', 'PLC_claseD', 'PLC_borrado', 'PLC_creadoEn', 'PLC_actualizadoEn'
    ];
} 