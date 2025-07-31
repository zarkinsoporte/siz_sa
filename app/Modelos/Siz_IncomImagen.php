<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_IncomImagen extends Model
{
    protected $table = 'Siz_IncomImagen';
    protected $primaryKey = 'IMG_id';
    public $timestamps = false;
    protected $fillable = [
        'IMG_incId', 'IMG_ruta', 'IMG_descripcion', 'IMG_cargadoPor', 'IMG_cargadoEn', 'IMG_borrado'
    ];
} 