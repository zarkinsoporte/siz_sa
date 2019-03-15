<?php
namespace App\Helpers;
use Carbon\Carbon;
class AppHelper
{
        private $meses = array();
        private $meses_min = array();
        private $diasSem_min = array();
    function __construct () {
        $this->meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $this->meses_min = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
        $this->diasSem_min = array('Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab');                
    }   

    public function getHumanDate($stringDate)
      {
        $fecha = Carbon::parse($stringDate);
        $dayOfTheWeek = $fecha->dayOfWeek;
        $weekday = $this->diasSem_min[$dayOfTheWeek];
        $mes = $this->meses_min[($fecha->format('n')) - 1];
        $inputs = $weekday.', '.$fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y');  
        return $inputs;
      }

     public static function instance()
     {
         return new AppHelper();
     }
}