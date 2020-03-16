<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \COM;
use Session;
use Illuminate\Support\Facades\Auth;
class SAPi extends Model
{
     private static $vCmp = false;


    public static function Connect(){
        self::$vCmp = new COM ('SAPbobsCOM.company') or die ("Sin conexión");
        self::$vCmp->DbServerType="6"; 
        self::$vCmp->server = "SERVER-SAPBO";
        self::$vCmp->LicenseServer = "SERVER-SAPBO:30000";
        self::$vCmp->CompanyDB = "SALOTTO";
        self::$vCmp->username = "controlp";
        self::$vCmp->password = "190109";
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

   
public static function ReciboProduccion($docEntry, $whs, $Cant, $comentario, $memo){
    //dd($docEntry);
       (self::$vCmp == false) ? self::Connect(): '';
   
    $vItem = self::$vCmp->GetBusinessObject("59");

    $vItem->Comments = utf8_decode($comentario);
    $vItem->JournalMemo = utf8_decode($memo); //Asiento Contable

        $vItem->Lines->BaseEntry = $docEntry; 
        $vItem->Lines->BaseType = '202'; 
        $vItem->Lines->TransactionType = '0'; // botrntComplete
        $vItem->Lines->Quantity = $Cant;
        //$vItem->Lines->WarehouseCode = $whs;
        $vItem->Lines->Add(); 
        if ($vItem->Add() == 0) {// cero es correcto   
                return 'Recibo creado correctamente';
        } else {
                $descripcionError = self::$vCmp->GetLastErrorDescription();    
                if (strpos($descripcionError, 'IGN1.WhsCode][line: 1') !== false) {
                $descripcionError = $descripcionError.' Uno o más materiales tienen stock negativo. Actualiza Reporte "POSIBLE ROMPIMIENTOS"';
                }
                return 'Error SAP: '.$descripcionError;
        }  
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