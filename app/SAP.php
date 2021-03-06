<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \COM;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SAP extends Model
{
    private static $vCmp = false;


    public static function Connect()
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        if ($lRetCode <> 0) {
            return self::$vCmp->GetLastErrorDescription();
        } else {
            return 'Conectado';
        }
    }
    public static function updateSerieOrden($orden, $numSerie)
    {
        /* //EN CASO DE QUE SEA MANUAL
        DB::table('OWOR')
        ->where('DocEntry', '=', $orden)
            ->update(['U_NoSerie' =>  $numSerie]);
            return $numSerie;
            */
        //Cambia el campo de usuario de serie.
        (self::$vCmp == false) ? self::Connect() : '';
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey($orden);
        $vItem->UserFields->Fields->Item('U_NoSerie')->Value = ''.$numSerie;
        $retCode = $vItem->Update;
        if ($retCode != 0) {
            return 'Error, '.self::$vCmp->GetLastErrorDescription();
        } else {
            return $numSerie;
        }
    }
    public static function updateImpresoOrden($orden, $impreso)
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        (self::$vCmp == false) ? self::Connect() : '';
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey($orden.'');
       // clock($RetVal);
        $vItem->UserFields->Fields->Item('U_Impreso')->Value = ''.$impreso;
        $retCode = $vItem->Update;
       // clock($retCode);
        if ($retCode != 0) {
            return 'SAP , '.self::$vCmp->GetLastErrorDescription();
        } else {
            return $impreso;
        }
    }
    public static function ProductionOrderProgramar($orden, $prog_corte, $sec_compra, $sec_ot, $estatus, $fCompra, $fProduccion)
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        (self::$vCmp == false) ? self::Connect() : '';
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey($orden.'');
       // clock($RetVal);
        if($prog_corte !== ''){
            $vItem->UserFields->Fields->Item('U_Grupo')->Value = ''.$prog_corte;
        }
        if($sec_compra !== ''){
            $vItem->UserFields->Fields->Item('U_OF')->Value = ''.$sec_compra;
        }
        if($sec_ot !== ''){
            $vItem->UserFields->Fields->Item('U_OT')->Value = ''.$sec_ot;
        }
        if($estatus !== ''){
            
            $vItem->UserFields->Fields->Item('U_Starus')->Value = '0'.$estatus;
        }
        if($fCompra !== ''){
            $vItem->UserFields->Fields->Item('U_FCompras')->Value = ''.$fCompra;
        }
        if($fProduccion !== ''){
            $vItem->UserFields->Fields->Item('U_FProduccion')->Value = ''.$fProduccion;
        }
        $retCode = $vItem->Update;
       // clock($retCode);
        if ($retCode != 0) {
            return 'SAP , '.self::$vCmp->GetLastErrorDescription();
        } else {
            return $orden;
        }
    }
    public static function updateStatusEntregaPiel($orden, $status, $FentregaPiel)
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
        (self::$vCmp == false) ? self::Connect() : '';
        $vItem = self::$vCmp->GetBusinessObject("202");
        $RetVal = $vItem->GetByKey($orden);
       // clock($RetVal);U_Starus' =>  '01', 'U_Entrega_Piel'
        $vItem->UserFields->Fields->Item('U_Starus')->Value = $status;
        $vItem->UserFields->Fields->Item('U_Entrega_Piel')->Value = $FentregaPiel;
        $retCode = $vItem->Update;
       // clock($retCode);
        if ($retCode != 0) {
            return 0;
        } else {
            return 1;
        }
    }
    public static function ProductionOrderStatus($orden, $status)
    {
        self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
        $lRetCode = self::$vCmp->Connect;
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
        self::$vCmp->DbServerType = "10";
        self::$vCmp->server = env('SAP_server');;
        self::$vCmp->LicenseServer = env('SAP_LicenseServer');
        self::$vCmp->CompanyDB = env('SAP_CompanyDB');
        self::$vCmp->username = env('SAP_username');
        self::$vCmp->password = env('SAP_password');
        self::$vCmp->DbUserName = env('SAP_DbUserName');
        self::$vCmp->DbPassword = env('SAP_DbPassword');
        self::$vCmp->UseTrusted = false;
        //self::$vCmp->language = "6";
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
            $vItem->ToWarehouse = $data['almacen_destino'];
            
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
            $vItem->ToWarehouse = $data['almacen_destino'];
            foreach ($data['items'] as $item) {
                $varDestino = explode(' - ', $item->Destino);
                //dd($data['almacen_origen']);
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
    public static function getApellidoPaternoUsuario($apellido)
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
    public static function crearOPs($itemOV, $OV, $cantidadOP, $repetir)
    {
        
        if (self::$vCmp == false) {
            $cnn = self::Connect();
            if ($cnn == 'Conectado') {
            } else {
                self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
                self::$vCmp->DbServerType = "10";
                self::$vCmp->server = env('SAP_server');;
                self::$vCmp->LicenseServer = env('SAP_LicenseServer');
                self::$vCmp->CompanyDB = env('SAP_CompanyDB');
                self::$vCmp->username = env('SAP_username');
                self::$vCmp->password = env('SAP_password');
                self::$vCmp->DbUserName = env('SAP_DbUserName');
                self::$vCmp->DbPassword = env('SAP_DbPassword');
                self::$vCmp->UseTrusted = false;
                //self::$vCmp->language = "6";
                $lRetCode = self::$vCmp->Connect;
                if ($lRetCode != 0) {
                    dd(self::$vCmp->GetLastErrorDescription());
                }
            }
        }
        $resultadoEjecucion = '';
        //-----------------------GENERACION OP DI API SAP BO-----------------------------------
        //DEFINICIONES
        //bopotStandard	    0	A production order type for producing a regular production item (default), using a production bill of materials.
        //bopotSpecial	    1	A production order type for producing and repairing items that can be any inventory item.
        //bopotDisassembly	2	A production order type for dismantling a parent item to its components, using a production Bill of Materials.
        //CREACION DE OBJETO
        //businessObject = (ProductionOrders)Globals.oCompany.GetBusinessObject(BoObjectTypes.oProductionOrders);
        $vItem = self::$vCmp->GetBusinessObject("202");

        //foreach ($Items_OV as $key => $itemOV) {
        for ($i = 0; $i < $repetir; $i++) { //este ciclo es para las op que no son grupales
            //CABECERA IZQ
            $vItem->ProductionOrderType = 0; //Estandar
            $vItem->ProductionOrderStatus = 0; //Orden planeada
            $vItem->ItemNo = $itemOV->ItemCode; // codigo del Articulo
            $vItem->PlannedQuantity = $cantidadOP; //cant con la que se hace la orden
            //$vItem->Warehouse = 'APT-ST';
            $vItem->Warehouse = ''.$itemOV->DfltWH; //'APT-ST';
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
            $vItem->ProductionOrderOriginEntry = $OV->DocEntry; // Num pedido (DocEntry de ORDR)

            $apellido = self::getApellidoPaternoUsuario(explode(' ', Auth::user()->lastName));
            $usuario_reporta = explode(' ', Auth::user()->firstName)[0] . ' ' . $apellido;
            //validacion de longitud 50
            $vItem->Remarks = $OV->Comments. ' Elaboro:'. $usuario_reporta;// observaciones

            //CAMPOS DEFINIDOS X USUARIO EN SAP
            //$rutaOP = (usar VALUE) SELECT b.U_Ruta FROM OITM b WHERE b.ItemCode=       
            $rutaOP = DB::table('OITM')
                ->where('ItemCode', $itemOV->ItemCode)
                ->value('U_Ruta');
            //set ruta
            $vItem->UserFields->Fields->Item('U_Ruta')->Value = $rutaOP;

            //$vItem->UserFields->Fields->Item('U_LineNum')->Value = '';
            $vItem->UserFields->Fields->Item('U_Starus')->Value = '03'; //Falta de material
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
            if (is_null( $OV->U_comp )) {
               $complejo = 'N/A';
            } else {
                $complejo = $OV->U_comp;    
            }
            $vItem->UserFields->Fields->Item('U_cc')->Value = $complejo; //

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

            if (intval($RetCode) == 0) {
               
                $resultadoEjecucion = 1;
                
            } else  {
                $resultadoEjecucion = $resultadoEjecucion .  'Error, '.self::$vCmp->GetLastErrorDescription();
            }
        } //end for
        //}//end foreach
        return $resultadoEjecucion;
    }
    public static function  crearOrdenesProduccion($ovs)
    {
        $preOrdenes = explode(',', $ovs);
        $mensajeErrr= '';
        foreach ($preOrdenes as $key => $preorden) {
            $orden = explode('&', $preorden);
            if (count($orden) == 4) {
                $ov = $orden[0];
                $grupal = $orden[1];
                $itemCode = $orden[2];
                $lineNum = $orden[3];
                //$Items_OV = DB::select('select * from RDR1 where RDR1.DocEntry = ?', [$ov]);
                $Item = DB::table('RDR1')
                ->leftJoin('OITM', 'RDR1.ItemCode', '=', 'OITM.ItemCode')
                ->where('RDR1.DocEntry', $ov)
                ->where('RDR1.ItemCode', $itemCode)
                ->where('RDR1.LineNum', $lineNum)
                ->select('RDR1.*', 'OITM.DfltWH')
                ->first();
                $OV = DB::table('ORDR')->where('DocEntry', $ov)->first();
                if ($grupal == 1) {
                    //(T1.Quantity-ISNULL(T1.U_Procesado
                    $cantidadOP = $Item->Quantity - ((is_numeric($Item->U_Procesado))? $Item->U_Procesado: 0);
                    $rs = self::crearOPs($Item, $OV, $cantidadOP, 1);
                    if(is_numeric($rs)){
                        if ($rs == 1) {
                            self::updateOV($ov, $Item, $cantidadOP);
                        }
                    }else{
                        $mensajeErrr = $mensajeErrr.$rs;
                    }
                } else if ($grupal == 0) {
                    $cantidadOP = 1;
                    $repetir = $Item->Quantity - ((is_numeric($Item->U_Procesado))? $Item->U_Procesado: 0);
                    $rs = self::crearOPs($Item, $OV, $cantidadOP, $repetir);
                    if(is_numeric($rs)){
                        if ($rs == 1) {
                            self::updateOV($ov, $Item, $repetir);
                        }
                    }else{
                        $mensajeErrr = $mensajeErrr.$rs;
                    }
                }
            }
        }
        return $mensajeErrr;
    }
    public static function updateOV($ov, $Item, $cantProcesada)
    {
        $order = DB::select('select sum (COALESCE(Quantity, 0)) as sum_cant, SUM(COALESCE(U_Procesado, 0)) as sum_procesado from RDR1 
        where  Docentry =  ? AND TreeType <> ?', [$ov, 'S']);

        if (count($order) > 0) { //verificar la existencia de la OV
            if ((int)$order[0]->sum_cant > (int)$order[0]->sum_procesado) { //si la cantidad de la OV es mayor a la procesada
                $orderLine = DB::select('select sum (COALESCE(Quantity, 0)) as sum_cant, SUM(COALESCE(U_Procesado, 0)) as sum_procesado from RDR1 
                where  Docentry =  ? AND ItemCode = ? AND LineNum = ?', [$ov, $Item->ItemCode, $Item->LineNum]);
                if ((int)$orderLine[0]->sum_cant > (int)$orderLine[0]->sum_procesado) {
                    $cantActual = (is_null($Item->U_Procesado)) ? 0 : ($Item->U_Procesado) * 1;
                    $cantActualProcesado = $cantActual + $cantProcesada;
                    DB::update('UPDATE RDR1 SET U_Procesado = ? WHERE DocEntry = ? AND ItemCode = ? AND LineNum = ?', [$cantActualProcesado, $ov, $Item->ItemCode, $Item->LineNum]);
                    $order = DB::select('select sum (COALESCE(Quantity, 0)) as sum_cant, SUM(COALESCE(U_Procesado, 0)) as sum_procesado from RDR1 
                        where  Docentry =  ? AND TreeType <> ?', [$ov, 'S']);
                    if (count($order) > 0) {
                        if ((int)$order[0]->sum_cant == (int)$order[0]->sum_procesado) { //si la cantidad de la OV es mayor a la procesada
                            self::$vCmp = new COM('SAPbobsCOM.company') or die("Sin conexión");
                            self::$vCmp->DbServerType = "10";
                            self::$vCmp->server = env('SAP_server');;
                            self::$vCmp->LicenseServer = env('SAP_LicenseServer');
                            self::$vCmp->CompanyDB = env('SAP_CompanyDB');
                            self::$vCmp->username = env('SAP_username');
                            self::$vCmp->password = env('SAP_password');
                            self::$vCmp->DbUserName = env('SAP_DbUserName');
                            self::$vCmp->DbPassword = env('SAP_DbPassword');
                            self::$vCmp->UseTrusted = false;
                            //self::$vCmp->language = "6";
                            $lRetCode = self::$vCmp->Connect;
                            if ($lRetCode != 0) {
                                dd(self::$vCmp->GetLastErrorDescription());
                            }

                            $oOrder = self::$vCmp->GetBusinessObject("17");
                            $RetVal = $oOrder->GetByKey($ov);
                            $oOrder->UserFields->Fields->Item('U_Procesado')->Value = 'S';
                            $retCode = $oOrder->Update;

                            if ($retCode != 0) {
                                return self::$vCmp->GetLastErrorDescription();
                            } 
                        }else{
                            return 'Error, No se marco OV como procesada, marcar manualmente en SAP (OV:'.$ov.') ';
                        }
                        
                    }
                } else {
                    return 'Error, No hay mas para procesar (OV:'.$ov.', Item:'.$Item->ItemCode.') ';
                }
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