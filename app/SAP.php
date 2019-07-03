<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \COM;
class SAP extends Model
{
     private static $vCmp = false;


    public static function Connect(){
        self::$vCmp = new COM ('SAPbobsCOM.company') or die ("Sin conexión");
        self::$vCmp->DbServerType="6"; 
        self::$vCmp->server = "SERVER-SAPBO";
        self::$vCmp->LicenseServer = "SERVER-SAPBO:30000";
        self::$vCmp->CompanyDB = "Pruebas";
        self::$vCmp->username = "manager";
        self::$vCmp->password = "aqnlaaepp";
        self::$vCmp->DbUserName = "sa";
        self::$vCmp->DbPassword = "B1Admin";
        self::$vCmp->UseTrusted = false;
        self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        if ($lRetCode <> 0) {
           return self::$vCmp->GetLastErrorDescription();
        } else {
            return 'Conectado';
        }  
   }
   public static function ProductionOrderStatus($orden, $status){
        //Cambia el status de una orden en SAP. el status sigue los siguientes criterios
        //--P -> 0 OP95 planificado
        //--R -> 1 OP271 liberado
        //--L -> 2 OP4 cerrado
        //--C -> 3 OP1 cancelado
    (self::$vCmp == false) ? self::Connect(): '';
    $vItem = self::$vCmp->GetBusinessObject("202");
    $RetVal = $vItem->GetByKey($orden);
    $vItem->ProductionOrderStatus = $status;
    $vItem->Update;
    if ($vItem->ProductionOrderStatus <> $status) {
        return false;
    } else {
        return true;
    }
   }
   public static function SaveArticulo($array){
        
        (self::$vCmp == false) ? self::Connect(): '';
        $vItem = self::$vCmp->GetBusinessObject("4");
        $RetVal = $vItem->GetByKey($array['pKey']);
        dd($vItem->ItemName);
   }
}

/*
    Thanks a lot for this post.

    For me the following is running

    $oItem=$vCmp->GetBusinessObject(4);

    $RetBool=$oItem->GetByKey("A1010");

    echo "<br>". $RetBoll;

    echo "<br>". $oItem->ItemName;

    This return:

    1

    Nom de l'article

    For recordset

    $oRS=$vCmp->GetBusinessObject(300);

    $oRS->DoQuery("Select Top 10 itemcode,itemName from oitm")

    $oRS->MoveFirst

    while ($oRS->EOF!=1){

    echo "<BR>".$oRS->Fields->Item(0)->value." ".$oRS->Fields->Item(1)->value;

    $oRS->MoveNext;

    }

    To add an order

    $oOrder=$vCmp->GetBusinessObject(17);

    $oOrder->CardCode="C01";

    $oOrder->DocDueDate="06/04/2009";

    $oOrder->Lines->Itemcode="A1010";

    $oOrder->Quantity=100;

    $RetCode=$oOrder->Add;

    $Nk="";

    if ($RetCode==0) {

    $vCmp->GetNewObjectCode($Nk);

    echo "<BR>" ."Doc Entry ".$vCmp->GetNewObjectCode($Nk);

    }
*/