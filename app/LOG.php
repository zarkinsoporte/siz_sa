<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class LOG extends Model
{
    protected $table = 'dbo.SIZ_Log';
    protected $primaryKey = 'LOG_user';
    public $timestamps = false;

}
