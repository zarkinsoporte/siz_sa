<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ROLLITEM extends Model
{
    protected $table = 'dbo.SIZ_ROLL_TEMP';
    protected $primaryKey = 'ROLL_codigo';
    public $timestamps = false;
    protected $fillable = ['ROLL_codigo', 'ROLL_precio', 'ROLL_moneda'];
}
