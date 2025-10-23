<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_InspeccionProcesoImagen extends Model
{
    protected $table = 'Siz_InspeccionProcesoImagen';
    protected $primaryKey = 'IPI_id';
    public $timestamps = false;
    protected $fillable = [
        'IPI_iprId', 'IPI_ruta', 'IPI_descripcion', 'IPI_cargadoPor',
        'IPI_cargadoEn', 'IPI_borrado'
    ];
}

