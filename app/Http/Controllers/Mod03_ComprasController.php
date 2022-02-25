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
            ini_set('memory_limit', '-1');
            set_time_limit(0);

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
}