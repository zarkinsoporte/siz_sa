<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class OP extends Model
{
    protected $table = 'dbo.@CP_OF';
    protected $primaryKey = 'U_DocEntry';
    public $timestamps = false;


    public static function getRuta($docEntry){
    $rs = DB::select('select u_Ruta from OWOR where DocEntry ='. $docEntry);
    //dd($rs);
    foreach ($rs as $r) {
        $ruta = explode(",", $r->u_Ruta);
        return $ruta;
    }
   }

   public static function getStatus($docEntry){
        return $order = OP::find($docEntry);
   }

   public static function getEstacionSiguiente ($docEntry){

        $i = 1 +  array_search(OP::find($docEntry)->U_CT, self::getRuta($docEntry));

       $rs = DB::select('select * from [@PL_RUTAS] where U_Orden ='. self::getRuta($docEntry)[$i]);

       foreach ($rs as $r) {
           return $r->Name;
       }
   }


}
