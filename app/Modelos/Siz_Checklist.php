<?php
namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Siz_Checklist extends Model
{
    protected $table = 'Siz_Checklist';
    protected $primaryKey = 'CHK_id';
    public $timestamps = false;
    protected $fillable = [
        'CHK_descripcion', 'CHK_activo', 'CHK_orden'
    ];
} 