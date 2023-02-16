<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\LOGOT;
use App\OP;
use App\User;
use Auth;
use DB;
use Hash;
use Dompdf\Dompdf;
//excel
use Illuminate\Http\Request;
//DOMPDF
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Lava;
use Mail;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\SAP;
class Mod03_ComprasController extends Controller
{
    public function cpp_combobox_articulos(Request $request){
        $f1 = explode("/", Input::get('fstart'));
        $fstart = $f1[2].$f1[1].$f1[0];
        $f2 = explode("/", Input::get('fend'));
        $fend = $f2[2].$f2[1].$f2[0];

        $oitms = DB::select("SELECT 
        PDN1.ItemCode codigo, PDN1.ItemCode +' - '+ OITM.ItemName AS descripcion   
        From PDN1 
        inner join OPDN on OPDN.DocEntry = PDN1.DocEntry left join OITM on PDN1.ItemCode = OITM.ItemCode
        left join ITM1 on PDN1.ItemCode = ITM1.ItemCode and ITM1.PriceList= 9 
        left join UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
        Where OITM.ItemCode is not null AND Cast(OPDN.DocDate as DATE) Between '" . $fstart . "' and '" . $fend . "' 
        GROUP BY PDN1.ItemCode, OITM.ItemName");
        return compact('oitms');
    }
    public function cpp_combobox_proveedores(Request $request){
        $f1 = explode("/", Input::get('fstart'));
        $fstart = $f1[2].$f1[1].$f1[0];
        $f2 = explode("/", Input::get('fend'));
        $fend = $f2[2].$f2[1].$f2[0];
        $todos_articulos = Input::get('todos_articulos');
       // dd($todos_articulos);
        $articulos = "'" . $request->input('articulos') . "'";
        $articulos = str_replace("'',", "", $articulos);
        $criterio = " ";
        if (strlen($articulos) > 3 && $articulos != '' && $todos_articulos == 'false') {
            $criterio = " AND (PDN1.ItemCode in(" . $articulos . ") ) ";
        }
        $proveedores = DB::select("SELECT 
        OPDN.CardCode codigo, OPDN.CardCode +' - '+ OPDN.CardName AS descripcion   
        From PDN1 
        inner join OPDN on OPDN.DocEntry = PDN1.DocEntry left join OITM on PDN1.ItemCode = OITM.ItemCode
        left join ITM1 on PDN1.ItemCode = ITM1.ItemCode and ITM1.PriceList= 9 
        left join UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
        Where OITM.ItemCode is not null AND Cast(OPDN.DocDate as DATE) Between '" . $fstart . "' and '" . $fend ."'  
        " . $criterio . "
        group by OPDN.CardCode, OPDN.CardName
        Order by OPDN.CardName");
        return compact('proveedores');
    }
    public function datatables_compras_proveedor(Request $request)
    {
        try {
           // ini_set('memory_limit', '-1');
            //set_time_limit(0);

            $articulos = "'" . $request->input('articulos') . "'";
            $articulos = str_replace("'',", "", $articulos);
            $proveedores = "'" . $request->input('proveedores') . "'";
            $proveedores = str_replace("'',", "", $proveedores);
            //dd(Input::get('fstart'), Input::get('fend'));
            $f1 = explode("/", Input::get('fstart'));
            $fstart = $f1[2].$f1[1].$f1[0];
            $f2 = explode("/", Input::get('fend'));
            $fend = $f2[2].$f2[1].$f2[0];

            $todos_articulos = Input::get('todos_articulos');
            $todos_proveedores = Input::get('todos_proveedores');

            $criterio = " ";
            if (strlen($articulos) > 3 && $articulos != '' && $todos_articulos == 'false') {
                $criterio = " AND (PDN1.ItemCode in(" . $articulos . ") ) ";
            }
            if (strlen($proveedores) > 3 && $proveedores != '' && $todos_proveedores == 'false') {
                $criterio = $criterio . " AND (OPDN.CardCode in(" . $proveedores . ") ) ";
            }

            $sel = "SELECT OPDN.CardCode +'-'+ OPDN.CardName as PROVEEDOR,
                OPDN.DocNum as N_ENTRADA, 
                Cast(OPDN.DocDate as DATE) as F_COMPRA, 
                PDN1.ItemCode as ARTICULO_CODIGO, 
                OITM.ItemName as ARTICULO_DESCR, 
                OITM.InvntryUom as UM, 
                PDN1.Quantity as CANTIDAD, 
                PDN1.NumPerMsr as X_PAQ, 
                (PDN1.Quantity*PDN1.NumPerMsr) as Q_INV, 
                (PDN1.Price / PDN1.NumPerMsr) as PREC_UNIT,
                PDN1.Rate as TIPO_CAMBIO,
                PDN1.Currency as M_C,
                PDN1.LineTotal as IMPORTE
                From PDN1 
                inner join OPDN on OPDN.DocEntry = PDN1.DocEntry left join OITM on PDN1.ItemCode = OITM.ItemCode
                left join ITM1 on PDN1.ItemCode = ITM1.ItemCode and ITM1.PriceList= 9 
                left join UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
                Where OITM.ItemCode is not null AND Cast(OPDN.DocDate as DATE) Between '".$fstart."' and '".$fend."' 
                ".$criterio."
                Order by PDN1.DocEntry";

            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            //dd($sel);
            $consulta = DB::select($sel);
            $registros = collect($consulta);
            Session::put('datatables_compras_proveedor', $registros);
            Session::put('fechas_compras_proveedor', 'del '. Input::get('fstart') .' al '. Input::get('fend'));
            return compact('registros');
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }
    public function index_compras_proveedor(){
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);

        $proveedores = [];
        $articulos = [];
        $fstart = \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $fend = \Carbon\Carbon::now()->endOfMonth()->toDateString();

        return view('Mod04_Materiales.ComprasPorProveedor', 
        compact( 'actividades', 'ultimo', 
        'proveedores', 'articulos', 'fstart', 'fend'));    
    }
    public function pedidosCsv()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas(); 
            if (Session::has('OrdenCompra')) {
            Session::forget('OrdenCompra');
            }      
            return view('Mod03_Compras.Pedidos',
                ['actividades' => $actividades,
                    'ultimo' => count($actividades),                           
                    ]
                );
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function postPedidosCsv()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();    
            $pedido = DB::select(DB::raw("SELECT POR1.LineStatus, OITM.BuyUnitMsr, OPOR.CANCELED, OPOR.DocStatus, OITM.ValidComm,OPOR.DocNum as NumOC, OPOR.DocDate as FechOC, OPOR.CardCode as CodeProv, OPOR.CardName as NombProv, POR1.ItemCode as Codigo, POR1.Dscription as Descrip, POR1.Quantity as CantTl, POR1.OpenQty as CantPend, POR1.ShipDate as FechEnt, OSLP.SlpName as Elaboro, 'ARTICULOS' as TipoOC, UFD1.Descr as Comprador, case when datediff(day,POR1.ShipDate, getdate()) >  0 and datediff(day,POR1.ShipDate, getdate()) < 9 then 'A-08 dias' when datediff(day,POR1.ShipDate, getdate()) >  8 and datediff(day,POR1.ShipDate, getdate()) < 16 then 'A-15 dias' 
            when datediff(day,POR1.ShipDate, getdate()) > 15 and datediff(day,POR1.ShipDate, getdate()) < 31 then 'A-30 dias' when datediff(day,POR1.ShipDate, getdate()) > 30 and datediff(day,POR1.ShipDate, getdate()) < 61 then 'A-60 dias' when datediff(day,POR1.ShipDate, getdate()) > 60 and datediff(day,POR1.ShipDate, getdate()) < 91 then 'A-90 dias' when datediff(day,POR1.ShipDate, getdate()) > 90 then 'A-MAS dias' End as Grupo, 1 as QtyMat, OPOR.Comments , POR1.Price, POR1.Currency FROM OPOR INNER JOIN POR1 ON OPOR.DocEntry = POR1.DocEntry LEFT JOIN OITM ON POR1.ItemCode = OITM.ItemCode 
            INNER JOIN OSLP on OSLP.SlpCode= POR1.SlpCode LEFT JOIN UFD1 on OITM.U_Comprador= UFD1.FldValue and UFD1.TableID='OITM' and UFD1.FieldID=10 
            WHERE   POR1.ItemCode is not null and DocNum = " .Input::get('NumOC'). " Union all 
            SELECT POR1.LineStatus, OITM.BuyUnitMsr, OPOR.CANCELED, OPOR.DocStatus, OITM.ValidComm, OPOR.DocNum as NumOC, OPOR.DocDate as FechOC, OPOR.CardCode as CodeProv, OPOR.CardName as NombProv, POR1.ItemCode as Codigo, POR1.Dscription as Descrip, POR1.Quantity as CantTl, POR1.OpenQty as CantPend, OPOR.DocDueDate as FechEnt, OSLP.SlpName as Elaboro, 'SERVICIOS' as TipoOC, 'Libre' as Comprador, case 
            when datediff(day,OPOR.DocDueDate, getdate()) >  0 and datediff(day,OPOR.DocDueDate, getdate()) < 9 then 'A-08 dias' when datediff(day,OPOR.DocDueDate, getdate()) >  8 and datediff(day,OPOR.DocDueDate, getdate()) < 16 then 'A-15 dias' when datediff(day,OPOR.DocDueDate, getdate()) > 15 and datediff(day,OPOR.DocDueDate, getdate()) < 31 then 'A-30 dias' when datediff(day,OPOR.DocDueDate, getdate()) > 30 and datediff(day,OPOR.DocDueDate, getdate()) < 61 then 'A-60 dias' when datediff(day,OPOR.DocDueDate, getdate()) > 60 and datediff(day,OPOR.DocDueDate, getdate()) < 91 then 'A-90 dias' 
            when datediff(day,OPOR.DocDueDate, getdate()) > 90 then 'A-MAS dias' End as Grupo, 1 as QtyMat, OPOR.Comments, POR1.Price, POR1.Currency FROM OPOR INNER JOIN POR1 ON OPOR.DocEntry = POR1.DocEntry LEFT JOIN OITM ON POR1.ItemCode = OITM.ItemCode INNER JOIN OSLP on OSLP.SlpCode= POR1.SlpCode 
            WHERE    POR1.ItemCode is null and DocNum = " .Input::get('NumOC'). "ORDER BY  CantPend desc, Descrip"));
            //dd($pedido); 
            if (count($pedido)>0){
            
            $datas = ['actividades' => $actividades,
                            'ultimo' => count($actividades),            
                            'pedido' => $pedido,
            ];
            Session::put('OrdenCompra', $datas);
                return view('Mod03_Compras.Pedidos', $datas);
            }else{
                return redirect()->back()->withErrors(array('message' => 'La orden no existe.'));
            }
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function desPedidosCsv()
    {
        try{
            if(Session::has ('OrdenCompra')){          
                $datas=Session::get('OrdenCompra');
                Excel::create('Siz_Orden_Compra' . ' - ' . $hoy = date("d/m/Y").'', function($excel)use($datas) {
                
                $excel->sheet('Hoja 1', function($sheet) use($datas){
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, ['Orden Compra','# Proveedor','Sku','Largo','Ancho','Alto','Version','Cantidad','Fecha Ped','Fecha Emb']);
                //Datos    
                $fila = 2;        
                foreach ( $datas['pedido'] as $pedi){
                    $date=date_create($pedi->FechOC);
                    $dat=date_create($pedi->FechEnt); 
                if($pedi->LineStatus == 'O'){
                    $sheet->row($fila, 
                    [
                        $pedi->NumOC,
                        $pedi->CodeProv,
                        $pedi->Codigo,
                        '',
                        '',
                        '',
                        $pedi->ValidComm,
                        number_format($pedi->CantTl,2),
                        date_format($date, 'd-m-Y'),          
                        date_format($dat, 'd-m-Y')          
                        ]);	
                }

                        $fila ++;
                    }
        });         
        })->download('csv');
        return  redirect()->back();
            }else {
            return redirect()->back()->withErrors(array('message' => 'No se almaceno correctamente la OC.'));
        }
        }catch(Exception $e){
            return redirect()->back()->withErrors(array('message' => 'Error al descargar archivo CSV'));
        }
    }
    public function PedidosCsvPDF()
    {
        $pdf = \PDF::loadView('Mod03_Compras.PedidosPDF', Session::get('OrdenCompra'));
        $pdf->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);
        return $pdf->stream('Siz_Orden_Compra' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }
    public function cppXLS(){

        $path = public_path() . '/assets/plantillas_excel/Mod_03/SIZ_compras_proveedor.xlsx';
       
          
        $datatables_compras_proveedor = Session::get('datatables_compras_proveedor');
       
        $excel =  Excel::load($path, function ($excel) use ($datatables_compras_proveedor) {
            $excel->sheet('COMPRAS', function ($sheet2) use ($datatables_compras_proveedor){
                $index = 6;

                $cant = count($datatables_compras_proveedor) + 6;
                $range = 'A5:M5';
                
               
                    $sheet2->cell('A4', function ($cell) {
                        $cell->setValue('ACTUALIZADO: '.(date("Y-m-d H:i:s")));
                    });
                    $sheet2->cell('D4', function ($cell) {
                        $cell->setValue(Session::get('fechas_compras_proveedor'));
                    });
                foreach ($datatables_compras_proveedor as $row) {
                    $sheet2->row($index, 
                    [
                        $row->PROVEEDOR
                        ,$row->N_ENTRADA
                        ,$row->F_COMPRA
                        ,$row->ARTICULO_CODIGO
                        ,$row->ARTICULO_DESCR
                        ,$row->UM
                        , number_format( $row->CANTIDAD , 2)
                        , number_format( $row->X_PAQ , 2)
                        , number_format( $row->Q_INV , 2)
                        , number_format( $row->PREC_UNIT , 2)
                        , number_format( $row->TIPO_CAMBIO , 2)
                        ,$row->M_C
                        , $row->IMPORTE
                    ]
                    );
                    $index++;
                }
                $sheet2->getStyle('A6:M'.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $sheet2->getColumnDimension('A')->setAutoSize(true);
                $sheet2->getColumnDimension('E')->setAutoSize(true);               
               // $sheet2->getColumnDimension('D')->setAutoSize(true);              
                //$sheet2->getColumnDimension('F')->setAutoSize(true);
                 $sheet2->setAutoFilter($range);
            });
        })
            ->setFilename('SIZ Compras por Proveedor')
            ->export('xlsx');    
    
    }
    public function ultimos_precios_XLS(){

        $path = public_path() . '/assets/plantillas_excel/Mod_03/SIZ_ultimos_precios.xlsx';
               
        $data_row = Session::get('datatables_ultimos_precios');
       
        $excel =  Excel::load($path, function ($excel) use ($data_row) {
            $excel->sheet('COMPRAS', function ($sheet2) use ($data_row){
                $index = 6;

                $cant = count($data_row) + 6;
                $range = 'A5:F5';
                
               
                    $sheet2->cell('A4', function ($cell) {
                        $cell->setValue('IMPRESO: '.(date("Y-m-d H:i:s")));
                    });
                    if (count($data_row) > 0) {
                       
                        $sheet2->cell('D4', function ($cell) use ($data_row){
                            $cell->setValue($data_row[0]->CODIGO.' - '.$data_row[0]->MATERIAL. '  UDM:'. $data_row[0]->UDM);
                        });
                    }
                foreach ($data_row as $row) {
                    $sheet2->row($index, 
                    [
                        $row->ORDEN_C
                        ,$row->COMPRA
                        ,$row->FECHAF
                        ,$row->PROVEEDOR
                        , str_replace("$", "", $row->PRECIOF)
                        ,$row->MONEDA
                    ]
                    );
                    $index++;
                }
                $sheet2->getStyle('A6:F'.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                //$sheet2->getColumnDimension('A')->setAutoSize(true);
                $sheet2->getColumnDimension('D')->setAutoSize(true);               
               // $sheet2->getColumnDimension('D')->setAutoSize(true);              
                //$sheet2->getColumnDimension('F')->setAutoSize(true);
                 $sheet2->setAutoFilter($range);
            });
        })
            ->setFilename('SIZ Ultimos Precios')
            ->export('xlsx');    
    
    }
    public function ultimos_precios_PDF()
    {
        $data_row = Session::get('datatables_ultimos_precios');
        $item = 'Sin registros';
        if (count($data_row) > 0) {
           $item = $data_row[0]->CODIGO.' - '.$data_row[0]->MATERIAL. '  UDM:'. $data_row[0]->UDM;
        }
        $fechaImpresion = date("d-m-Y H:i:s");
        $headerHtml = view()->make(
            'Mod03_Compras.ReporteUltimosPrecios_pdfheader',
            [
                'titulo' => 'Reporte Últimos Precios',
                'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                'item' => $item
            ]
        )->render();

        //return view('Mod05_Ventas.ReporteBarrasPDF', compact('a', 'generator'));
        $pdf = \SPDF::loadView('Mod03_Compras.ReporteUltimosPreciosPDF', compact('data_row'));
       
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
        $pdf->setOption('footer-left', 'SIZ');
        //$pdf->setOption('orientation', 'Landscape');
        $pdf->setOption('margin-top', '33mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('page-size', 'Letter');

        return $pdf->inline();

    }
    public function index_ultimos_precios(){
        //$user = Auth::user();
        $actividades = session('userActividades');;
        $ultimo = count($actividades);

        $articulos = [];
        $fstart = \Carbon\Carbon::now()->toDateString();

        return view('Mod04_Materiales.UltimosPrecios', 
        compact( 'actividades', 'ultimo', 
        'articulos', 'fstart'));    
    }
    public function datatables_ultimos_precios(Request $request)
    {
        // $registros = [];
        // return compact('registros');
        try {
           // ini_set('memory_limit', '-1');
           // set_time_limit(0);

            $articulos = $request->input('articulos');
           // $articulos = str_replace("'',", "", $articulos);
           
            $f1 = explode("/", Input::get('fstart'));
            $fstart = $f1[2].$f1[1].$f1[0];
          
            $criterio = " ";
            if ($articulos != '') {
                $criterio = " AND (PDN1.ItemCode = '" . $articulos . "' ) ";

            }

            $sel = "SELECT
                OITM.ItemCode AS CODIGO
                        , OITM.ItemName AS MATERIAL
                        , OITM.InvntryUom AS UDM
                        , PDN1.BaseRef AS ORDEN_C
                        , OPDN.DocNum AS COMPRA
                        ,Convert(varchar, OPDN.DocDueDate, 23)AS FECHAF 
                        ,(OPDN.CardCode +'  '+ OPDN.CardName)AS PROVEEDOR
                        ,FORMAT(PDN1.Price,'C4','es-MX')AS PRECIOF
                        , PDN1.Currency AS MONEDA
                From PDN1
                Inner Join OPDN on OPDN.DocEntry = PDN1.DocEntry
                Inner Join OITM on OITM.ItemCode = PDN1.ItemCode
                Where Cast(OPDN.DocDueDate as date) >= Cast('" . $fstart . "'  as date)
                ".$criterio."
                Order by PDN1.DocEntry desc";

            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            //dd($sel);
            $consulta = DB::select($sel);
            $registros = collect($consulta);
            Session::put('datatables_ultimos_precios', $registros);
            //Session::put('fechas_compras_proveedor', 'del '. Input::get('fstart') .' al '. Input::get('fend'));
            return compact('registros');
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }
     public function cpp_combobox_articulos_ultimos_precios(Request $request){
        $f1 = explode("/", Input::get('fstart'));
        $fstart = $f1[2].$f1[1].$f1[0];

        $oitms = DB::select("SELECT 
        PDN1.ItemCode codigo, PDN1.ItemCode +' - '+ OITM.ItemName AS descripcion,
         OITM.ItemName AS descr, 
         OITM.InvntryUom AS udm
        From PDN1 
        inner join OPDN on OPDN.DocEntry = PDN1.DocEntry left join OITM on PDN1.ItemCode = OITM.ItemCode
        left join ITM1 on PDN1.ItemCode = ITM1.ItemCode and ITM1.PriceList= 9 
        left join UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
        Where OITM.ItemCode is not null AND Cast(OPDN.DocDueDate as date)>= Cast('" . $fstart . "'  as date)
        GROUP BY PDN1.ItemCode, OITM.ItemName, InvntryUom
        ORDER BY OITM.ItemName");
        return compact('oitms');
    }

    public function index_ordenes_compra(){
        //$user = Auth::user();
        $actividades = session('userActividades');
        $ultimo = count($actividades);

        $proveedores = DB::select("SELECT CardCode, CardName, Currency FROM OCRD WHERE CardType = ? AND frozenFor='N' ORDER BY CardName", ['S']);
        //$fstart = \Carbon\Carbon::now()->toDateString();
       
        $rates = DB::table('ORTT')->where('RateDate', date('Y-m-d'))->get();        
        if (count($rates) < 3) {
           
            Session::flash('error', 'No estan Capturados los Tipos de Cambio en SAP.');
        }
        return view('Mod03_Compras.OrdenesCompra', 
        compact( 'actividades', 'ultimo', 
        'proveedores'));    // 'rates',
    }
    public function get_oc_xfecha(Request $request){
        $query = "SELECT 
            OPOR.DocNum as NumOC
            , CONVERT(varchar, OPOR.DocDate, 23) as FechaOC
            , OPOR.CardCode + ' - ' +OPOR.CardName as Proveedor
            , OSLP.SlpName as Elaboro 
            , CASE WHEN docstatus = 'C' THEN 'CERRADA' ELSE 'ABIERTA' END Estatus
            , DocTotal Total
            , OPOR.DocCur Moneda
            FROM OPOR 
            INNER JOIN POR1 ON OPOR.DocEntry = POR1.DocEntry
            LEFT JOIN OSLP on OSLP.SlpCode= POR1.SlpCode 
            WHERE 
            DocType = 'I' AND
            OPOR.DocDate between '".$request->get('fi')." 00:00:00' and  '".$request->get('ff')." 23:59:59'
            GROUP BY DocNum, OPOR.DocDate, CardCode, SlpName, CardName, docstatus, DocCur, DocTotal                
        ";
        //dd($query);
        $result = DB::select($query);
        $data=[];
        if (count($result) > 0) {
            $respuesta = 'ok';
            $data = $result;
        } else {
            $respuesta = 'Error, La OC no existe';
        }
        return compact('respuesta', 'data');
    }
    public function get_oc(Request $request){
        $result = DB::select("SELECT 
            OPOR.DocNum as NumOC
            , CONVERT(varchar, OPOR.DocDate, 23) as FechaOC
            , OPOR.CardCode + ' - ' +OPOR.CardName as Proveedor
            , OSLP.SlpName as Elaboro 
            , CASE WHEN docstatus = 'C' THEN 'CERRADA' ELSE 'ABIERTA' END Estatus
            , FORMAT(DocTotal, '##.##') Total
            , OPOR.DocCur Moneda
            FROM OPOR 
            INNER JOIN POR1 ON OPOR.DocEntry = POR1.DocEntry
            LEFT JOIN OSLP on OSLP.SlpCode= POR1.SlpCode 
            WHERE 
            DocType = 'I' AND
            OPOR.DocNum = ?
            GROUP BY DocNum, OPOR.DocDate, CardCode, SlpName, CardName, docstatus, DocCur, DocTotal
        ", [$request->get('oc')]);
        $data=[];
        if (count($result) > 0) {
            $respuesta = 'ok';
            $data = $result[0];
        } else {
            $respuesta = 'Error, La OC no existe';
        }
        return compact('respuesta', 'data');
    }
    public function orden_compra_pdf($NumOC)
    {
        //$NumOC = $request->get('NumOC');
       
        $fechaImpresion = date("d-m-Y H:i:s");
        $headerHtml = view()->make(
            'Mod03_Compras.ReporteUltimosPrecios_pdfheader',
            [
                'titulo' => 'Orden De Compra',
                'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                'item' => ''
            ]
        )->render();

       // return view('Mod03_Compras.OrdenCompraPDF');//, compact('a', 'generator'));
        $pdf = \SPDF::loadView('Mod03_Compras.OrdenCompraPDF', []);
       
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
        $pdf->setOption('footer-left', 'SIZ');
        //$pdf->setOption('orientation', 'Landscape');
        $pdf->setOption('margin-top', '33mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('page-size', 'Letter');

        return $pdf->inline();

    }
    public function getProveedorOC(Request $request){
        $result = DB::select("
        
        ", [$request->get('id')]);
        $data=[];
        if (count($result) > 0) {
            $respuesta = 'ok';
            $data = $result[0];
        } else {
            $respuesta = 'Error, No existe';
        }
        return compact('respuesta', 'data');
    }
    public function get_rates(Request $request){
        $rates = DB::table('ORTT')
        ->where('Currency', $request->get('mon'))
        ->where('RateDate', date('Y-m-d'))->get();
       
        return compact('rates');
    }
    public function get_detalleProveedor(Request $request){
        $proveedor = DB::select("SELECT
            Proveedores.CardCode PRO_Codigo,
            CardName PRO_Nombre,
            Proveedores.LicTradNum PRO_RFC,
            Proveedores.E_Mail PRO_Email,
            Phone1 PRO_Telefono,
            Currency Moneda,
            (select top 1
            OCPR.Name + ' '+ OCPR.LastName + ', email:' + OCPR.E_MailL 
            from OCPR where OCPR.CardCode = Proveedores.CardCode AND validFor = 'Y'
            Group by 
            OCPR.Name , OCPR.LastName , OCPR.E_MailL
            ) AS CON_Contacto,

            (select top 1
            CRD1.Street + ', ' + CRD1.Block + ', '+ CRd1.City + ', '+ CRd1.State+', '+Crd1.Country + '. CP:'+ CRd1.ZipCode
            from CRD1 where CRD1.CardCode = Proveedores.CardCode
            Group by 
            CRD1.Street , CRD1.Block , CRd1.City ,CRd1.State,Crd1.Country, CRd1.ZipCode
            ) AS PRO_Domicilio
            FROM OCRD Proveedores
            WHERE CardType = 'S' AND frozenFor='N' AND Proveedores.CardCode = ?
            ORDER BY CardName
        ", [$request->get('id')]);
        $proveedor = $proveedor[0];
        return compact('proveedor');
    }

     public function cargaTablaArticulos(Request $request){
        //$tc_sys = 1;
        if (false) {
        //if ($request->get('moneda') != 'MXP') {
            $tc_sys =  DB::table('ORTT')
            //->where('Currency', $request->get('moneda'))
            ->where('RateDate', date('Y-m-d'));
        }
        $tc_sys = DB::table('ORTT')        
        ->where('RateDate', date('Y-m-d'))->get();
        $tipos_cambio = [];
        foreach ($tc_sys as $value) {
            $tipos_cambio [$value->Currency] = $value->Rate.'';
        }
        $sql = "SELECT top 100
                        OITM.ItemCode ART_CodigoArticulo,
                        OITM.ItemName ART_Nombre,
                        OITM.U_TipoMat ATP_Descripcion,
                        Cast(CONVERT(DECIMAL(10,2), OITM.PurPackUn) as nvarchar) PurPackUn, 
                        OITM.PurPackMsr UMC,   
                        Cast(CONVERT(DECIMAL(10,2), OITM.NumInBuy) as nvarchar) AFC_FactorConversion, 
                        OITM.BuyUnitMsr UMI,
                        Cast(CONVERT(DECIMAL(10,4), ITM1.Price) as nvarchar) AS LIS_COMPRA,                        
                        ((ITM1.Price * OITM.NumInBuy * 
                        CASE WHEN ITM1.Currency = 'MXP' THEN 1
                        WHEN ITM1.Currency = 'USD' THEN CONVERT(DECIMAL(10,4),'".$tipos_cambio ['USD']."')
                        WHEN ITM1.Currency = 'EUR' THEN CONVERT(DECIMAL(10,4),'".$tipos_cambio ['EUR']."')
                        END
                        ) / '".$request->get('tipo_cambio')."') AS 
                        Precio_Tipo_Cambio,
                        Cast(CONVERT(DECIMAL(10,4), ITM1.Price * OITM.NumInBuy 
                        ) as nvarchar) Precio,
                        ITM1.Currency AS M_L
                                    
                    FROM OITM
                    LEFT JOIN ITM1 ON OITM.ItemCode = ITM1.ItemCode
                    AND ITM1.PriceList= 9

                    WHERE OITM.ItemCode IS NOT NULL 
                    AND OITM.PrchseItem = 'Y' AND OITM.InvntItem = 'Y'
                    ORDER BY OITM.ItemName, OITM.ItemCode";
                    // if ($request->get('moneda') == 'MXP'){
                    //     $sql = $sql . " AND  ITM1.Currency = 'MXP' ";
                    // }
                    //$sql = $sql . "ORDER BY OITM.ItemName, OITM.ItemCode";
        try{

            $consulta2 = [];
            if ($request->get('moneda') != ''){
                $consulta2 = \DB::select(
                \DB::raw( //cambiar consulta para SAP
                    $sql
                    )
                );
            }
                
            $ajaxData = array();
            $ajaxData['data2'] = $consulta2;
            $ajaxData['codigo'] = 200;
            $ajaxData['sql'] = $sql;

            return $ajaxData;

            //return $consulta;

        } catch (\Exception $e){

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array("mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine())));

        }

    }

    public function consultaIvasYUnidadesMedida(){

        try{

            $ivas = \DB::select(
                \DB::raw(
                    "SELECT Code CMIVA_IVAId, Rate CMIVA_Porcentaje 
                    FROM OSTC
                    WHERE  ValidForAP = 'Y'
                    ORDER BY Rate"
                )
            );

            // $unidadesMedida = \DB::select(
            //     \DB::raw(
            //         "SELECT
            //             CMUM_UnidadMedidaId,
            //             CMUM_Nombre
            //         FROM ControlesMaestrosUM
            //         ORDER BY
            //           CMUM_Nombre
            //         "
            //     )
            // );

            // $tipoPartidaMisc = \DB::select(
            //     \DB::raw(
            //         "SELECT
            //             CMM_ControlId,
            //             CMM_Valor
            //         FROM ControlesMaestrosMultiples
            //         WHERE CMM_Control = 'OC_CMM_TipoPartidaMiscelanea'
            //         AND CMM_Eliminado = 0
            //         "
            //     )
            // );

            $ajaxData = array();
            $ajaxData['ivas'] = $ivas;
            $ajaxData['unidadesMedida'] = [];
            $ajaxData['tipoPartidaMisc'] = [];
            $ajaxData['codigo'] = 200;

            return $ajaxData;

            //return $consulta;

        } catch (\Exception $e){

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array("mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine())));

        }

    }
    
    public function registraOC(){

        SAP::registraOC();    
        return 'ok';
        \DB::beginTransaction();

        try{

            date_default_timezone_set('America/Mexico_City');
            $hoy=date('d-m-Y H:i:s');
            $dia=date('d-m-Y');

            //$OC_CodigoOC = $_POST['OC_CodigoOC'] == '' ? null : $_POST['OC_CodigoOC'];
            $OC_PRO_ProveedorId = $_POST['OC_PRO_ProveedorId'];
            //$OC_PDOC_DireccionOCId = $_POST['OC_PDOC_DireccionOCId'] == '' ? null : $_POST['OC_PDOC_DireccionOCId'];
            //$OC_CMM_TipoOCId = $_POST['OC_CMM_TipoOCId'];
            $OC_MON_MonedaId = $_POST['OC_MON_MonedaId'];
            $OC_MONP_Paridad = $_POST['OC_MONP_Paridad'] == '' ? null : $_POST['OC_MONP_Paridad'];
            //$OC_MONP_ParidadId = $_POST['OC_MONP_ParidadId'] == '' ? null : $_POST['OC_MONP_ParidadId'];
            
          
            //$OC_PorcentajeDescuento = $_POST['OC_PorcentajeDescuento'] == '' ? null : $_POST['OC_PorcentajeDescuento'];
            //$OC_CMIVA_IVAId = $_POST['OC_CMIVA_IVAId'] == '' ? null : $_POST['OC_CMIVA_IVAId'];
            if($OC_CMIVA_IVAId == null){

                $OCD_CMIVA_PorcentajeIVA = 0;

            }
            else{

                $OCD_CMIVA_PorcentajeIVA = $_POST['OCD_CMIVA_PorcentajeIVA'] == '' ? null : $_POST['OCD_CMIVA_PorcentajeIVA'];

            }
            
            $TablaArticulosExistentes = isset($_POST['TablaArticulosExistentes']) ? json_decode($_POST['TablaArticulosExistentes'], true) : array();
           
            $empleadoId = '';//DataBaseSession::getEmpleadoId();
            $editaProveedor = $_POST['editaProveedor'];

            if($_POST['status'] == 0){

                //INSERTA ORDEN DE COMPRA
               
                for($x = 0; $x < $cuentaTablaArtExis; $x ++){

                    if($TablaArticulosExistentes[$x]['ID_ARTICULO'] != ""){

                        $contadorPartida++;
                                  \DB::commit();
                    }
                }  
            }  
            return ['Status' => 'Valido', 'respuesta' => $response];

        }
        catch (\Exception $e){

            \DB::rollback();
            return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' .$e->getMessage()];

        }

    }
}