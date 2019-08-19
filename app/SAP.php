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
    //pKey
       //proveedor Mainsupplier
       //metodo U_Metodo
       //grupop U_GrupoPlanea
       //comprador U_Comprador
       //costocompras PriceList->Price
       //monedacompras PriceList->Currency
      
    (self::$vCmp == false) ? self::Connect(): '';
    //self::$vCmp->XmlExportType("xet_ExportImportMode");
    $vItem = self::$vCmp->GetBusinessObject("4");
    $RetVal = $vItem->GetByKey($array['pKey']);
    //Actualizar Proveedor
    $vItem->Mainsupplier = $array['proveedor'];
    //Seleccionar lista de Precios
    $vItem->PriceList->SetCurrentLine(8);
    $vItem->PriceList->Price = $array['costocompras'];
    $vItem->PriceList->Currency = $array['monedacompras'];

   //$arrayName = array(
    //'metod' => $vItem->UserFields->Fields->Item('U_Metodo')->Value, 
    //'grupo' => $vItem->UserFields->Fields->Item('U_GrupoPlanea')->Value, 
    //'comp' => $vItem->UserFields->Fields->Item('U_Comprador')->Value, 
    //);  
    //dd($arrayName);
    $vItem->UserFields->Fields->Item('U_Metodo')->Value = $array['metodo'];
    $vItem->UserFields->Fields->Item('U_GrupoPlanea')->Value = $array['grupop'];
    $vItem->UserFields->Fields->Item('U_Comprador')->Value = $array['comprador'];

    $retCode = $vItem->Update; 
    if ($retCode != 0) {
        return self::$vCmp->GetLastErrorDescription();
    } else {
        return 'ok';
    }  
                   
   }

   public static function Transfer($data){
        
        $items = $data['id_solicitud']; // items(Codigos, Cant. Real), Solicitante, Area, 
        //Anadir CantPendiente, CantRealSurtir
        $items = $data['PriceList']; //10
        $items = $data['Destination']; //APG ST

        echo '<br>';
        $vItem = $vCmp->GetBusinessObject("67");
        
        //Obtener Lineas de una Transferencia
        //$RetVal = $vItem->GetByKey("665");
        //echo $vItem->Lines->SetCurrentLine(0);
        //echo $vItem->Lines->ItemCode;
        //dd();

        //Crear Transferencia
        $vItem->DocDate = (new DateTime('now'))->format('Y-m-d H:i:s');
        $vItem->FromWarehouse = "AMP-ST";
        $vItem->PriceList = "10";
        $vItem->FolioNumber = "7780";//**/Pendiente
        $vItem->Comments = "Transfer de prueba Sistemas";
        //agregar linea
        $vItem->Lines->ItemCode = "10001";
        $vItem->Lines->WarehouseCode = "APG-ST";
        $vItem->Lines->Quantity = 1;        
        $vItem->Lines->Add();
        //Guardar Transferencia
        echo $vItem->Add();  // cero es correcto


        $vCmp->GetLastErrorDescription();
        echo 'fin';
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