<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \COM;
use Session;
use Illuminate\Support\Facades\Auth;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class SAPi extends Model
{
    private static $vCmp = false;

    public static function Connect(){
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = "".env('SAP_server');
        self::$vCmp->LicenseServer = "".env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = "".env('SAP_CompanyDB');
        self::$vCmp->username = "".env('SAP_username');
        self::$vCmp->password = "".env('SAP_password');
        self::$vCmp->DbUserName = "".env('SAP_DbUserName');
        self::$vCmp->DbPassword = "".env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        if ($lRetCode != 0) {
           return self::$vCmp->GetLastErrorDescription();
        } else {
            return 'Conectado';
        }  
   }

   public static function updateImpresoOrden($orden, $impreso)
    {
        (self::$vCmp == false) ? self::Connect() : '';
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey($orden."");
        //clock($RetVal);
        $vItem->UserFields->Fields->Item('U_Impreso')->Value = "".$impreso;
        $retCode = $vItem->Update;
        //clock($retCode);
        if ($retCode != 0) {
            return 'Error, '.self::$vCmp->GetLastErrorDescription();
        } else {
            return $impreso;
        }
    }
public static function ReciboProduccion($docEntry, $whs, $Cant, $comentario, $memo){
   if (self::$vCmp == false) {
       $cnn = self::Connect();
      if ( $cnn == 'Conectado') {
        
      }else{
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = "".env('SAP_server');
        self::$vCmp->LicenseServer = "".env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = "".env('SAP_CompanyDB');
        self::$vCmp->username = "".env('SAP_username');
        self::$vCmp->password = "".env('SAP_password');
        self::$vCmp->DbUserName = "".env('SAP_DbUserName');
        self::$vCmp->DbPassword = "".env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
          if ($lRetCode != 0) {
           dd(self::$vCmp->GetLastErrorDescription());
        } 
      }
   }
   //bloque actualiza fecha de la orden
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey("".$docEntry);
        $vItem->DueDate = date('d-m-Y');
        
        $retCode = $vItem->Update;
        if ($retCode != 0) {
            return 'Error SAP: '.self::$vCmp->GetLastErrorDescription();
        }
        $vItem = null;
        //fin bloque actualiza fecha de la orden
    $vItem = self::$vCmp->GetBusinessObject("59");//Crear un recibo de Produccion

    $vItem->Comments = "".utf8_decode($comentario);
    $vItem->JournalMemo = "".utf8_decode($memo); //Asiento Contable

        $vItem->Lines->BaseEntry = "".$docEntry; //OP
        $vItem->Lines->BaseType = "202"; 
        $vItem->Lines->TransactionType = "0"; // botrntComplete
        /*(double)filter_var($str, 
		FILTER_SANITIZE_NUMBER_FLOAT, 
		FILTER_FLAG_ALLOW_FRACTION);*/
        $vItem->Lines->Quantity = (float) filter_var($Cant, FILTER_SANITIZE_NUMBER_FLOAT, 
		FILTER_FLAG_ALLOW_FRACTION);
        //$vItem->Lines->WarehouseCode = $whs;
        $vItem->Lines->Add(); //guardar Recibo de Produccion
        if ($vItem->Add() == 0) {// cero es correcto   
                return 'Recibo creado correctamente';
        } else {
                $descripcionError = self::$vCmp->GetLastErrorDescription();    
                if (strpos($descripcionError, 'IGN1.WhsCode][line: 1') !== false) {
                $descripcionError = $descripcionError.
                ' Uno o más materiales tienen stock negativo. Actualiza ';
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