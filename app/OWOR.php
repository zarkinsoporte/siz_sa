<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class OWOR extends Model
{
    protected $table = 'dbo.OWOR';
    protected $primaryKey = 'DocEntry';
    public $timestamps = false;
    protected $fillable = [];
}
