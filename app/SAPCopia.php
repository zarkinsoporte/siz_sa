<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \COM;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SAPCopia extends Model
{
    private static $vCmp = false;


    public static function Connect()
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "6";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        if ($lRetCode <> 0) {
            return self::$vCmp->GetLastErrorDescription();
        } else {
            return 'Conectado';
        }
    }
    public static function ProductionOrderStatus($orden, $status)
    {
        //Cambia el status de una orden en SAP. el status sigue los siguientes criterios
        //--P -> 0 OP95 planificado
        //--R -> 1 OP271 liberado
        //--L -> 2 OP4 cerrado
        //--C -> 3 OP1 cancelado
        (self::$vCmp == false) ? self::Connect() : '';
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
    public static function SaveArticulo($array)
    {
        //pKey
        //proveedor Mainsupplier
        //metodo U_Metodo
        //grupop U_GrupoPlanea
        //comprador U_Comprador
        //costocompras PriceList->Price
        //monedacompras PriceList->Currency

        (self::$vCmp == false) ? self::Connect() : '';
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
    public static function Connect2()
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "6";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        if ($lRetCode <> 0) {
            return self::$vCmp->GetLastErrorDescription();
        } else {
            return 'Conectado';
        }
    }
    public static function Transfer($data)
    {

        $id = $data['id_solicitud'];
        (self::$vCmp == false) ? self::Connect2() : '';
        //self::$vCmp->XmlExportType("xet_ExportImportMode");
        $vItem = self::$vCmp->GetBusinessObject("67");
        //Obtener Lineas de una Transferencia
        // $RetVal = $vItem->GetByKey("7782");
        //dd($vItem->Printed);
        //echo $vItem->Lines->SetCurrentLine(0);
        //echo $vItem->Lines->ItemCode;
        //dd($data['items']);
        DB::beginTransaction();
        if (count($data['items']) > 0) {
            //Crear Transferencia
            $vItem->DocDate = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $vItem->FromWarehouse = $data['almacen_origen']; //origen
            $vItem->PriceList = $data['pricelist'];
            $vItem->FolioNumber = $id; //**/Vale:solicitud
            $vItem->Comments = $data['observaciones'];
            $vItem->JournalMemo = "Traslados -";

            foreach ($data['items'] as $item) {
                $varDestino = explode(' - ', $item->Destino);

                $surtido = DB::select('select WTR1.ItemCode, SUM(Quantity) as Cant
                    from WTR1 
                    inner join SIZ_TransferSolicitudesMP as t on t.DocEntry_Transfer = WTR1.DocEntry
                    left join SIZ_MaterialesSolicitudes as s on s.Id_Solicitud = t.Id_Solicitud and s.ItemCode = WTR1.ItemCode
                    where t.Id_Solicitud = ? AND s.ItemCode = ?
                    group by WTR1.ItemCode', [$id, $item->ItemCode]);

                $CantSurtida = array_sum(array_pluck($surtido, 'Cant'));
                if ($CantSurtida >= $item->Cant_Autorizada) {
                    DB::rollBack();
                    return 'Error, Material ' . $item->ItemCode . ' ya fue surtido.';
                }
                //agregar lineaS               
                if ($data['almacen_origen'] == 'APG-PA') {
                    if ($item->Cant_PendienteA >= $item->CA) {
                        $vItem->Lines->Quantity = $item->CA;
                        DB::table('SIZ_MaterialesSolicitudes')
                            ->where('Id', $item->Id)
                            ->update([
                                'Cant_PendienteA' => ($item->Cant_PendienteA - $item->CA),
                                'Cant_ASurtir_Origen_A' => 0
                            ]);
                        $vItem->Lines->ItemCode = $item->ItemCode;
                        $vItem->Lines->WarehouseCode = trim($varDestino[0]);
                        if ($item->BatchNum > 0) {
                            $lotes = DB::table('SIZ_MaterialesLotes')
                                ->where('Id_Item', $item->Id)
                                ->where('alm', 'APG-PA')
                                ->get();
                            if (count($lotes) > 0) {
                                foreach ($lotes as $l) {
                                    $vItem->Lines->BatchNumbers->BatchNumber = $l->lote;
                                    $vItem->Lines->BatchNumbers->Quantity = $l->Cant;
                                    $vItem->Lines->BatchNumbers->Add();
                                }
                            } else {
                                DB::rollBack();
                                return 'Error, Material ' . $item->ItemCode . ' sin lotes asignados';
                            }
                        }
                        $vItem->Lines->Add();
                        if (($item->Cant_PendienteA - $item->CA) == 0) {
                            DB::table('SIZ_MaterialesSolicitudes')
                                ->where('Id', $item->Id)
                                ->update(['EstatusLinea' => 'T']);
                        } elseif (($item->Cant_PendienteA - $item->CA) > 0) {
                            // DB::table('SIZ_MaterialesSolicitudes')
                            //     ->where('Id', $item->Id)
                            //     ->update(['EstatusLinea' => 'P']);
                        }
                    }
                } elseif ($data['almacen_origen'] == 'AMP-ST') {

                    if ($item->Cant_PendienteA >= $item->CB) {
                        $vItem->Lines->Quantity = $item->CB;
                        DB::table('SIZ_MaterialesSolicitudes')
                            ->where('Id', $item->Id)
                            ->update([
                                'Cant_PendienteA' => ($item->Cant_PendienteA - $item->CB),
                                'Cant_ASurtir_Origen_B' => 0
                            ]);
                        $vItem->Lines->ItemCode = $item->ItemCode;
                        $vItem->Lines->WarehouseCode = trim($varDestino[0]);
                        if ($item->BatchNum > 0) {
                            $lotes = DB::table('SIZ_MaterialesLotes')
                                ->where('Id_Item', $item->Id)
                                ->where('alm', 'AMP-ST')
                                ->get();
                            if (count($lotes) > 0) {
                                foreach ($lotes as $l) {
                                    $vItem->Lines->BatchNumbers->BatchNumber = $l->lote;
                                    $vItem->Lines->BatchNumbers->Quantity = $l->Cant;
                                    $vItem->Lines->BatchNumbers->Add();
                                }
                            } else {
                                DB::rollBack();
                                return 'Error, Material ' . $item->ItemCode . ' sin lotes asignados';
                            }
                        }
                        $vItem->Lines->Add();
                        if (($item->Cant_PendienteA - $item->CB) == 0) {
                            DB::table('SIZ_MaterialesSolicitudes')
                                ->where('Id', $item->Id)
                                ->update(['EstatusLinea' => 'T']);
                        } elseif (($item->Cant_PendienteA - $item->CB) > 0) {
                            // DB::table('SIZ_MaterialesSolicitudes')
                            // ->where('Id', $item->Id)
                            // ->update(['EstatusLinea' => 'P']);
                        }
                    }
                }
            }
        } else {
            DB::rollBack();
            return 'Error, No hay ningun material que surtir';
        }
        //Guardar Transferencia

        if ($vItem->Add() == 0) { // cero es correcto
            DB::commit();
            $docentry = DB::table('OWTR')
                ->where('FolioNum', $id)
                ->max('DocEntry');
            DB::table('SIZ_TransferSolicitudesMP')->insert(
                ['Id_Solicitud' => $id, 'DocEntry_Transfer' => $docentry, 'Usuario' => Auth::user()->U_EmpGiro]
            );
            return $docentry;
        } else {
            DB::rollBack();
            return 'Error desde SAP: ' . self::$vCmp->GetLastErrorDescription();
        }
    }
    public static function Transfer2($data)
    {

        $id = $data['id_solicitud'];
        (self::$vCmp == false) ? self::Connect2() : '';
        //self::$vCmp->XmlExportType("xet_ExportImportMode");
        $vItem = self::$vCmp->GetBusinessObject("67");

        DB::beginTransaction();
        if (count($data['items']) > 0) {
            //Crear Transferencia
            $vItem->DocDate = (new \DateTime('now'))->format('Y-m-d H:i:s');
            $vItem->FromWarehouse = $data['almacen_origen']; //origen
            $vItem->PriceList = $data['pricelist'];
            $vItem->FolioNumber = $id; //**/Vale:solicitud
            $vItem->Comments = $data['observaciones'];
            $vItem->JournalMemo = "Traslados -";

            foreach ($data['items'] as $item) {
                $varDestino = explode(' - ', $item->Destino);
                //agregar lineaS 
                if ($item->Cant_PendienteA >= $item->CA) {
                    $vItem->Lines->Quantity = $item->CA;
                    DB::table('SIZ_MaterialesTraslados')
                        ->where('Id', $item->Id)
                        ->update([
                            'Cant_PendienteA' => ($item->Cant_PendienteA - $item->CA),
                            'Cant_ASurtir_Origen_A' => ($item->Cant_PendienteA - $item->CA)
                        ]);
                    $vItem->Lines->ItemCode = $item->ItemCode;
                    $vItem->Lines->WarehouseCode = trim($varDestino[0]);
                    if ($item->BatchNum > 0) {
                        $lotes = DB::table('SIZ_MaterialesLotes')
                            ->where('Id_Item', $item->Id)
                            ->where('alm', $data['almacen_origen'])
                            ->get();
                        if (count($lotes) > 0) {
                            foreach ($lotes as $l) {
                                $vItem->Lines->BatchNumbers->BatchNumber = $l->lote;
                                $vItem->Lines->BatchNumbers->Quantity = $l->Cant;
                                $vItem->Lines->BatchNumbers->Add();
                            }
                        } else {
                            DB::rollBack();
                            return 'Error, Material ' . $item->ItemCode . ' sin lotes asignados';
                        }
                    }
                    $vItem->Lines->Add();
                    if (($item->Cant_PendienteA - $item->CA) == 0) {
                        DB::table('SIZ_MaterialesTraslados')
                            ->where('Id', $item->Id)
                            ->update(['EstatusLinea' => 'T']);
                    }
                }
            }
        } else {
            return 'No hay ningun material que surtir';
        }
        //Guardar Transferencia

        if ($vItem->Add() == 0) { // cero es correcto
            $docentry = DB::table('OWTR')
                ->where('FolioNum', $id)
                ->max('DocEntry');
            DB::commit();
            DB::table('SIZ_TransferSolicitudesMP')->insert(
                ['Id_Solicitud' => $id, 'DocEntry_Transfer' => $docentry, 'Usuario' => Auth::user()->U_EmpGiro]
            );
            return $docentry;
        } else {
            DB::rollBack();
            return 'Error desde SAP: ' . self::$vCmp->GetLastErrorDescription();
        }
    }

    public static function setReciboProduccion($docEntry, $Cant)
    {
        (self::$vCmp == false) ? self::Connect() : '';

        $vItem = self::$vCmp->GetBusinessObject("59");
        $vItem->Lines->BaseEntry = $docEntry;
        $vItem->Lines->BaseType = '202';
        $vItem->Lines->TransactionType = '0'; // botrntComplete
        $vItem->Lines->Quantity = $Cant;
        $vItem->Lines->WarehouseCode = 'APT-ST';
        $vItem->Lines->Add();
        if ($vItem->Add() == 0) { // cero es correcto   
            return 'Recibo de producción creado correctamente';
        } else {
            $descripcionError = self::$vCmp->GetLastErrorDescription();
            if (strpos($descripcionError, 'IGN1.WhsCode][line: 1') !== false) {
                $descripcionError = $descripcionError . ' Uno o más materiales tienen stock negativo.';
            }
            return 'Error SAP: ' . $descripcionError;
        }
    }
    public function getApellidoPaternoUsuario($apellido)
    {
        $preposiciones = ["DE", "LA", "LAS", "D", "LOS", "DEL"];
        if (in_array($apellido[0], $preposiciones) && count($apellido) > 1) {
            if (in_array($apellido[1], $preposiciones) && count($apellido) > 2) {
                return $apellido[0] . ' ' . $apellido[1] . ' ' . $apellido[2];
            } else {
                return $apellido[0] . ' ' . $apellido[1];
            }
        } else {
            return $apellido[0];
        }
    }
    public function crearOPs($Items_OV, $OV, $cantidadOP, $repetir)
    {
        if (self::$vCmp == false) {
            $cnn = self::Connect();
            if ($cnn == 'Conectado') {
            } else {
                self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
                self::$vCmp->DbServerType = "6";
                self::$vCmp->server = env('SAP_server');;
                self::$vCmp->LicenseServer = env('SAP_LicenseServer');
                self::$vCmp->CompanyDB = env('SAP_CompanyDB');
                self::$vCmp->username = env('SAP_username');
                self::$vCmp->password = env('SAP_password');
                self::$vCmp->DbUserName = env('SAP_DbUserName');
                self::$vCmp->DbPassword = env('SAP_DbPassword');
                self::$vCmp->UseTrusted = false;
                self::$vCmp->language = "6";
                $lRetCode = self::$vCmp->Connect;
                if ($lRetCode != 0) {
                    dd(self::$vCmp->GetLastErrorDescription());
                }
            }
        }
        $resultadoEjecucion = [];
        //-----------------------GENERACION OP DI API SAP BO-----------------------------------
        //DEFINICIONES
        //bopotStandard	    0	A production order type for producing a regular production item (default), using a production bill of materials.
        //bopotSpecial	    1	A production order type for producing and repairing items that can be any inventory item.
        //bopotDisassembly	2	A production order type for dismantling a parent item to its components, using a production Bill of Materials.
        //CREACION DE OBJETO
        //businessObject = (ProductionOrders)Globals.oCompany.GetBusinessObject(BoObjectTypes.oProductionOrders);
        $vItem = self::$vCmp->GetBusinessObject("202");

        foreach ($Items_OV as $key => $itemOV) {
            for ($i = 0; $i < $repetir; $i++) { //este ciclo es para las op que no son grupales
                //CABECERA IZQ
                $vItem->ProductionOrderType = 0; //Estandar
                $vItem->ProductionOrderStatus = 0; //Orden planeada
                $vItem->ItemNo = $itemOV->ItemCode; // codigo del Articulo
                $vItem->PlannedQuantity = $cantidadOP; //cant con la que se hace la orden
                $vItem->Warehouse = 'APT-ST';

                //CABECERA DER (2)
                //fecha de finalizacion Fecha Entrega – 21 días (Compra de Materiales)
                $fecha = Carbon::parse($OV->DocDueDate);
                //$fecha = $fecha->subDays(21);
                if ($fecha->lessThan(Carbon::now())) {
                    $fecha = Carbon::now();
                }
                $vItem->DueDate = $fecha->format('Y-m-d H:i:s'); //fecha de finalizacion
                //--Usuario: El del Sistema //auto
                //--Origen: Manual
                $vItem->ProductionOrderOriginEntry = $ov; // Num pedido (DocEntry de ORDR)

                $apellido = Self::getApellidoPaternoUsuario(explode(' ', Auth::user()->lastName));
                $usuario_reporta = explode(' ', Auth::user()->firstName)[0] . ' ' . $apellido;
                //validacion de longitud 50
                $vItem->JournalRemarks = $OV->Comments . ', Elaboro:' . $usuario_reporta; // observaciones

                //CAMPOS DEFINIDOS X USUARIO EN SAP
                //$rutaOP = (usar VALUE) SELECT b.U_Ruta FROM OITM b WHERE b.ItemCode=       
                $rutaOP = DB::table('OITM')
                    ->where('ItemCode', $itemOV->ItemCode)
                    ->value('U_Ruta');
                //set ruta
                $vItem->UserFields->Fields->Item('U_Ruta')->Value = $rutaOP;

                //$vItem->UserFields->Fields->Item('U_LineNum')->Value = '';
                $vItem->UserFields->Fields->Item('U_Status')->Value = '03'; //Falta de material
                //$vItem->UserFields->Fields->Item('U_NoSerie')->Value = '1';

                //Con Orden: Del pedido se toma la Prioridad y con ella se determina
                //esto. Si es Prioridad 1 = Con Orden. Prioridad 2 seria = Sin Orden y
                // la 3 seria = Pronostico.
                //1_Alta-C 2_Media-S 3_Baja-P

                switch ($OV->U_Prioridad) {
                    case '1':
                        $item_uc_orden = 'C';
                        break;
                    case '2':
                        $item_uc_orden = 'S';
                        break;
                    case '3':
                        $item_uc_orden = 'P';
                        break;
                    default:
                        $item_uc_orden = 'C';
                        break;
                }
                switch ($OV->U_Especial) {
                    case 'L':
                        $atencion_especial = 'N';
                        break;
                    case 'E':
                        $atencion_especial = 'S';
                        break;

                    default:
                        $atencion_especial = 'N';
                        break;
                }

                $vItem->UserFields->Fields->Item('U_C_Orden')->Value = $item_uc_orden; //S,C o P
                $vItem->UserFields->Fields->Item('U_AteEspecial')->Value = $atencion_especial; //S o N
                $vItem->UserFields->Fields->Item('U_cc')->Value = $OV->U_comp; //

                //Fecha de Producción = Fecha Entrega – 7 días (Entrega producción)
                $fechaProduccion = Carbon::now(); //vamos a guardar

                $fechaEntregaOV = Carbon::parse($OV->DocDueDate); //fecha Entrega 
                $fechaContabilizacionOV = Carbon::parse($OV->DocDate); //fecha contabilizacion
                $fechaContabilizacion_mas30 = $fechaContabilizacionOV->addDays(30); //+30

                if ($fechaEntregaOV->lessThan($fechaContabilizacion_mas30)) {
                    $fechaProduccion = $fechaContabilizacion_mas30;
                } else {
                    $fechaProduccion = $fechaEntregaOV;
                }
                $fechaProduccion = $fechaProduccion->subDays(7);

                $vItem->UserFields->Fields->Item('U_FProduccion')->Value = $fechaProduccion->format('Y-m-d H:i:s'); //


                $fechaCompras = Carbon::now(); //vamos a guardar

                if ($fechaEntregaOV->lessThan($fechaContabilizacion_mas30)) {
                    $fechaCompras = $fechaContabilizacion_mas30;
                } else {
                    $fechaCompras = $fechaEntregaOV;
                }
                $fechaCompras = $fechaCompras->subDays(21);

                $vItem->UserFields->Fields->Item('U_FCompras')->Value = $fechaCompras->format('Y-m-d H:i:s'); //

                //Nota: Si el Producto no cuenta con LDM No se puede realizar la OP

                $RetCode = $vItem->Add();

                $Nk = "";

                if ($RetCode == 0) {
                    array_push($resultadoEjecucion, self::$vCmp->GetNewObjectCode($Nk));
                } else if ($lRetCode != 0) {
                    array_push($resultadoEjecucion, self::$vCmp->GetLastErrorDescription());
                }
            } //end for
        } //end foreach
        return $resultadoEjecucion;
    }
    public static function crearOrdenesOV($ov)
    {
        $Items_OV = DB::select('select * from RDR1 where RDR1.DocEntry = ?', [$ov]);
        $OV = DB::table('ORDR')->where('DocEntry', $ov)->first();

        $preOrdenes = explode(',', $request->input('ordenesvta'));
        foreach ($preOrdenes as $key => $preorden) {
            $orden = explode('&', $preorden);
            $grupal = $orden[1];
            $ov = $orden[0];
            if ($grupal == 1) {
                $cantidadOP = $itemOV->Quantity;
                Self::crearOPs($Items_OV, $OV, $cantidadOP, 1);
            } else if ($grupal == 0) {
                $cantidadOP = 1;
                $repetir = $itemOV->Quantity;
                Self::crearOPs($Items_OV, $OV, $cantidadOP, $repetir);
            }
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