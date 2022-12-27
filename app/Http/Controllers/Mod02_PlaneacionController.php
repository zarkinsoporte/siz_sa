<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Dompdf\Dompdf;
//excel
use Illuminate\Http\Request;
//DOMPDF
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Datatables;
use Illuminate\Database\Eloquent\Collection;
use App\SAP;
use App\LOG;
use App\OP;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\LOGOT;
use Cache;
use App\Jobs\ItemPrecioUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log AS LALog;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod02_PlaneacionController extends Controller
{
public function reporteMRP()
{
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();
       
        $fechauser = Input::get('text_selUno');
        $tipo = Input::get('text_selDos');
       
        
        // if ($accion == 'Actualizar') {//se ejecuta la actualizacion de la tabla
        //     DB::update("exec SIZ_MRP");
        // }
        $f = DB::table('SIZ_Z_MRP')->first();// obtener fecha de ultima actualizacion
        if(isset($f)){
             $Text = 'Actualizado el: ' . \AppHelper::instance()->getHumanDate_format($f->fechaDeEjecucion, 'h:i A');
        }else{
             $Text = 'Sin datos';
        }
        $data = array(        
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'text' => $Text,
            'fechauser' => $fechauser,
            'tipo' => $tipo
        );
        return view('Mod02_Planeacion.reporteMRP', $data);
    } else {
        return redirect()->route('auth/login');
    }
}
public function indexGenerarOP(){
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $file_anterior = DB::table('SIZ_Log')
            ->where('LOG_cod_error', 'PRINT_PLANEACION')
            ->orderBy('LOG_fecha', 'desc')->first();
            
            $pedidos_activos = DB::select("SELECT 
                    OW.OriginNum AS Pedido 
                    FROM OWOR OW
                    INNER JOIN OITM O on OW.ItemCode = O.ItemCode					
                    WHERE  
                     OW.Status in ('P', 'R' )
                     AND O.U_TipoMat = 'PT'
					GROUP BY OriginNum
                    ORDER BY OW.OriginNum");
            $pedidos_activos = array_merge(['null'], $pedidos_activos);
            $data = array(
                'cbopedido' => collect($pedidos_activos)->lists('Pedido'),
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'file_anterior' => $file_anterior->LOG_descripcion,
                'tipo' => ['PT', 'CASCO', 'OTRO'],
                'tipocompleto' => ['FUNDAS', 'CASCO', 'PATAS Y BASTIDORES', 'SUB-ENSAMBLES', 'HABILITADO'],
                'estado' => ['Planificadas', 'Liberadas'],
                'estatus' => ['-', '01-DETENIDO VENTAS', '02-FALTA INFORMACION', '03-FALTA PIEL', '04-REVISION DE PIEL', '05-POR ORDENAR'],
                'prioridades' => ['-', 'S - 03 SIN ORDEN DE CLIENTE', 'C - 01 CON ORDEN DE CLIENTE', 'P - 06 PRONOSTICOS']
            );
            return view('Mod02_Planeacion.generarOP', $data);
        } else {
            return redirect()->route('auth/login');
        }
}
public function generarOP(Request $request){
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $orders = '';
        if(strlen($request->input('ordenesvta')) > 0 ){                     
            $orders = SAP::crearOrdenesProduccion($request->input('ordenesvta'));                        
        }else{
            $orders = 'Error, No se ha seleccionado ninguna OV';
        }
        return compact('orders');
}
public function programar_op(Request $request){
    // dd($request->all());
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    if(strlen($request->input('ordenes')) > 0 ){
        $preOrdenes = explode(',', $request->input('ordenes'));
        $mensajeErrr= [];

        $prog_corte = $request->input('prog_corte');
        $hule = $request->input('hule');
        $metales = $request->input('metales');
        $sec_ot = $request->input('sec_ot');
        $estatus = $request->input('estatus');
        $prioridad = $request->input('prioridad');
        $num_pedido = $request->input('num_pedido');
        $fCompra = $request->input('fCompra');
        $fProduccion = $request->input('fProduccion');
        switch ($prioridad) {
            case '1':
                $prioridad = 'S';
                break;
            case '2':
                $prioridad = 'C';
                break;
            case '3':
                $prioridad = 'P';
                break;
            
            default:
                # code...
                break;
        }
        
        //guardar
        foreach ($preOrdenes as $key => $orden) {                    
            //logica verificacion de pedido
            $n_pedido = '';
            if ($num_pedido != '') {                          
                $op =DB::table('OWOR')
                ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                    ->select(
                        'OWOR.ItemCode',
                        'OWOR.OriginNum as pedido',
                        'OWOR.plannedqty',
                        'OITM.ItemName'
                    )
                    ->where('OWOR.DocEntry', $orden)->first();
                $enpedido = DB::select("SELECT T0.[CardCode] AS CodCliente, 
                T0.[CardName] AS NombreCliente,
                T1.[Dscription] AS Descripcion, 
                T1.ItemCode AS ItemCode, 
                T1.Quantity AS Cantidad, 
                T0.[DocNum] AS NumPedido
                ,T1.LineStatus 
                FROM ORDR T0 INNER JOIN RDR1 T1 ON T0.DocEntry = T1.DocEntry 
                WHERE T0.[DocStatus] = 'O' AND T1.LineStatus = 'O'
                and T0.[DocNum] = ? AND ItemCode = ?", [$num_pedido, $op->ItemCode]);
                
                if (count($enpedido) > 0) {
                $n_pedido = $num_pedido;
                }else{
                    array_push($mensajeErrr, ['OP' => $orden, 'ItemCode'=> $op->ItemCode,
                    'ItemName'=> $op->ItemName
                ]);
                }
            }            
            SAP::ProductionOrderProgramar($orden, $prog_corte, $hule, $sec_ot, $estatus, $fCompra, $fProduccion, $prioridad, $n_pedido, $metales);                                   
        }
        return compact('mensajeErrr');
    }else{
        return 'No se ha seleccionado ninguna Orden';
    }
}
public function liberacion_op(Request $request){
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        if(strlen($request->input('ordenes')) > 0 ){
            $preOrdenes = explode(',', $request->input('ordenes'));
            $mensajeErrr= '';
            foreach ($preOrdenes as $key => $preorden) {
                $orden = explode('&',$preorden);
                if (count($orden) == 2) {
                    $op = $orden[1];
                    $estado = $orden[0];
                                 
                    if ($estado == 'PLANIFICADA') { //PLANIFICADO
                       //liberar en SAP
                       $rs = SAP::ProductionOrderStatus($op, 1); 
                       if(!($rs)){
                           $mensajeErrr = 'Error No se logro cambiar(planificar) el estado en SAP. OP#'.$op;
                        }else {
                            //poner en Control Piso
                            try {
                                //DB::beginTransaction();
                                $PlannedQty = DB::table('OWOR')->where('DocNum', $op)->value('PlannedQty');
                                //dd($PlannedQty);
                                $dt = date('Ymd H:i');
                                //esta linea obtiene el consecutivo del numero
                                $consecutivo = DB::select('select max (CONVERT(INT,Code)) as Code from [@CP_OF]');
                                //aqui acaba num consecutivo
                                $primer_estacion = OP::getRuta($op)[0];
                                $newCode = new OP();
                                $newCode->Code = ((int) $consecutivo[0]->Code) + 1;
                                $newCode->Name = ((int) $consecutivo[0]->Code) + 1;
                                $newCode->U_DocEntry = $op;
                                $newCode->U_CT = $primer_estacion;
                                $newCode->U_Entregado = 0;
                                $newCode->U_Orden = $primer_estacion;
                                $newCode->U_Procesado = 0;
                                $newCode->U_Recibido = intval($PlannedQty);
                                $newCode->U_Reproceso = "N";
                                $newCode->U_Defectuoso = 0;
                                $newCode->U_Comentarios = "";
                                $newCode->U_CTCalidad = 0;
                                $newCode->save();
                                //save=insert select max (CONVERT(INT,Code)) as Code
                                $consecutivologot = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                                $lot = new LOGOT();
                                $lot->Code = ((int) $consecutivologot[0]->Code) + 1;
                                $lot->Name = ((int) $consecutivologot[0]->Code) + 1;
                                $lot->U_idEmpleado = Auth::user()->empID;
                                $lot->U_CT = $primer_estacion;
                                $lot->U_Status = "O";
                                $lot->U_FechaHora = $dt;
                                $lot->U_OP = $op;
                                $lot->save();
                               // DB::commit();
                            } catch (Exception $e) {
                                DB::rollBack();
                                $mensajeErrr = 'Error al guardar nuevo registro en CP_OF.';
                            }
                        }
                       
                    } else if ($estado == 'LIBERADA') { //LIBERADO
                       //planificar en SAP
                       $rs = SAP::ProductionOrderStatus($op, 0); 
                       if(!($rs)){
                           $mensajeErrr = 'Error No se logro cambiar(liberar) el estado en SAP. OP#'.$op;
                        }else {
                            //quitar de CP
                            $code = OP::where('U_DocEntry', $op)->first();                            
                            $code->delete();
                        }
                       
                    }
                }
            }
            return compact('mensajeErrr');
        }else{
            return 'No se ha seleccionado ninguna Orden';
        }
}
public function impresion_op(Request $request){
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    if(strlen($request->input('ordenes')) > 0 ){

           // DB::beginTransaction();
                $preOrdenes = explode(',', $request->input('ordenes'));            
                $mensajeErrr = '';
                //clock($preOrdenes);
                $pdf_final = new \Clegginabox\PDFMerger\PDFMerger;
                //clock($pdf_final);
                $user_path = public_path('pdf_ordenes/user_' . Auth::user()->U_EmpGiro);
                //clock($user_path);
                if (!\File::exists($user_path)) {
                    \File::makeDirectory($user_path);
                    //clock('creado dir');
                }
                //dd();
                array_map("unlink", glob($user_path . '/*.pdf'));

                foreach ($preOrdenes as $op) {
                    //dd(base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'));
                    $info = OP::getInfoOwor($op);
                    $lista_precio = 1;
                    $xCodeSub[0] = $info->ItemCode;
                    $plannedqty = $info->plannedqty * 1;
                    //dd($xCodeSub);
                    $index = 0;
                    while ($index < count($xCodeSub)) {
            
                        $subs = DB::select("Select OITM.ItemCode AS CODIGO 
                           -- ,OITM.ItemName AS MATERIAL, 
                           -- OITM.InvntryUom AS UDM, 
                           -- RUTE.Name AS ESTACION, 
                           -- ITT1.Quantity AS CANTIDAD, 
                           -- ITM1.Price AS L_10,  
                           -- (ITT1.Quantity * ITM1.Price) AS IMPORTE, 
                           -- ITM1.Currency AS MONEDA 
                            from ITT1 
                            inner join OITM on OITM.ItemCode = ITT1.Code 
                            inner join [@PL_RUTAS] RUTE on RUTE.Code = OITM.U_estacion 
                            inner join ITM1 on ITM1.ItemCode = OITM.ItemCode 
                            and ITM1.PriceList=? 
                            where  ITT1.Father = ? and 
                            (OITM.QryGroup29 = 'Y' or OITM.QryGroup30 = 'Y' or OITM.QryGroup31 = 'Y' or OITM.QryGroup32 = 'Y') 
                            --Order by MATERIAL",
                            [$lista_precio, $xCodeSub[$index]]);
                        $subs = array_pluck($subs,'CODIGO');
                        if (count($subs) > 0) {
                            $xCodeSub = array_merge($xCodeSub, $subs);
                        }
                        $index++;
                    }
                    $sub_cadena = "";
                    for ($x = 0; $x < count($xCodeSub); $x++) {
                        if ($x == count($xCodeSub) - 1) {
                            $sub_cadena = $sub_cadena . $xCodeSub[$x];
                        } else {
                            $sub_cadena = $sub_cadena . $xCodeSub[$x] . ",";
                        }
                    }
                    //dd($sub_cadena);
                    $total_vs = 0;
                   // dd($sub_cadena);
                    $Materiales = DB::select('exec SIZ_MATERIALES_LDM_CODIGO ?, ?', [$sub_cadena, $lista_precio]);

                    $composicion = DB::table('RDR1')
                        ->select(DB::raw('ItemCode AS codigo, Dscription AS descripcion'))
                        ->where('TreeType', 'S')
                        ->where('ItemCode', 'like', '%'. substr($info->ItemCode, 0, 4) .'%')
                        ->where('DocEntry', $info->pedido) //No. Pedido
                        ->first(); 
                    $ordenesSerie = DB::select("SELECT o.Docentry AS op, 
                        o.ItemCode AS codigo, 
                        a.ItemName AS descripcion,
                        a.U_VS AS VS,
                        o.plannedqty AS cantidad
                        FROM OWOR o
                        INNER JOIN OITM a ON a.ItemCode=o.ItemCode
                        WHERE o.U_NoSerie = ? ", [$info->U_NoSerie]);  
                    $data = array(
                        'plannedqty' => $plannedqty,
                        'ordenes_serie' => $ordenesSerie,
                        'composicion' => $composicion,
                        'total_vs' => $total_vs,
                        'data' => $Materiales,
                        'info' => $info,
                        'op' => $op,
                        'db' => DB::table('OADM')->value('CompnyName'),
                    );
                //clock($data);
                    $headerHtml = view()->make('header', $data)->render();
                   // clock($headerHtml);
                    $pdf = \SPDF::loadView('Mod01_Produccion.impresionOPPDF2', $data);
                    $pdf->setOption('header-html', $headerHtml);
                    $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
                    $pdf->setOption('footer-left', 'SIZ');
                   // clock($pdf);
                    $pdf->setOption('margin-top', '55mm');
                    $pdf->setOption('margin-left', '5mm');
                    $pdf->setOption('margin-right', '5mm');
                    $pdf->setOption('page-size', 'Letter');
                    $pdf->save($user_path . '/' . $op . '.pdf');
                  //  clock($user_path);
                    //return $pdf->inline();
                    //$pdf = PDF::loadView('pdf.invoice', $data);
                    //header('Content-Type: application/pdf');
                    //header('Content-Disposition: attachment; filename="file.pdf"');
                    //return SPDF::getOutput();
                    
                    $pdf_final->addPDF($user_path . '/' . $op . '.pdf');
                    $rs = SAP::updateImpresoOrden($op, '1'); 
                 /*  DB::table('OWOR')
                    ->where('DocEntry', '=', $op)
                    ->update(['U_Impreso' =>  1]);*/
                   // clock($rs);
                } //end Foreach
                $pdf_final->merge('file', $user_path . '/ordenes.pdf', 'P');
                $file = '/pdf_ordenes/user_' . Auth::user()->U_EmpGiro.'/ordenes.pdf';
               
              //  DB::commit();
                return compact('mensajeErrr', 'file');
                
      
    }else{
        return 'Error , No se ha seleccionado ninguna Orden';
    }
}
public function asignar_series(Request $request){
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $mensajeErrr= '';
        if(strlen($request->input('ordenes')) > 0 ){
            //dd($request->input('ordenes'));
            $preOrdenes = explode(',', $request->input('ordenes'));
            $index = 0;
            $numSerieGrupal = null;
            foreach ($preOrdenes as $key => $preorden) {
                $orden = explode('&',$preorden);
                if (count($orden) == 2) {
                    $grupal = $orden[1];
                    $orden = $orden[0];
                    //obetener siguiente numero de serie
                    $numSerie = DB::table('OWOR')->max('U_NoSerie');
                    $numSerie++;
                    if ($grupal == 1) {
                       if ($index == 0){
                           $index = 1;
                           $numSerieGrupal = $numSerie;
                       }
                       if (!is_null($numSerieGrupal)) {
                        $rs = SAP::updateSerieOrden($orden, $numSerieGrupal); 
                           if(!is_numeric($rs)){
                               $mensajeErrr = $mensajeErrr.$rs;
                            }                         
                       }
                    } else if ($grupal == 0) {
                        $rs = SAP::updateSerieOrden($orden, $numSerie);
                        if(!is_numeric($rs)){
                            $mensajeErrr = $mensajeErrr.$rs;
                         }
                    }
                }
            }
           // dd($mensajeErrr);
            return compact('mensajeErrr');
        }else{
            return 'No se ha seleccionado ninguna Orden';
        }
}
public function reset_series_op(Request $request){
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    $mensajeErrr= '';
    if(strlen($request->input('ordenes')) > 0 ){
        //dd($request->input('ordenes'));
        $preOrdenes = explode(',', $request->input('ordenes'));
        $numSerie = '1';
        foreach ($preOrdenes as $key => $preorden) {                
            $rs = SAP::updateSerieOrden($preorden, $numSerie);
            if(!is_numeric($rs)){
                $mensajeErrr = $mensajeErrr.$rs;
            }
        }
        // dd($mensajeErrr);
        return compact('mensajeErrr');
    }else{
        return 'No se ha seleccionado ninguna Orden';
    }
}
public function updateOV(Request $request){
    ini_set('memory_limit', '-1');
        set_time_limit(0);
        //if(strlen($request->input('ordenvta')) > 0 ){                     
       //     $orders = SAP::updateOV($request->input('ordenvta'));
        if(true ){
            $Item = DB::table('RDR1')->where('DocEntry', '2208')->where('ItemCode', '3086-02-P0233')->first();                     
            $orders = SAP::updateOV('2208', $Item, 1);
            return $orders;
        }else{
            return 'No se ha seleccionado ninguna OV';
        }
}
public function registros_gop(Request $request){
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);            
            $sel = "SELECT
                '0' [Grupal],
                OITT.Code AS Code,
                CONVERT (VARCHAR, [Fecha inicio], 103) AS FechaInicio,
                Prioridad,
                [Número de cliente] + ' - ' + [Razón social] AS Cliente,
                OC,
                TMP.Pedido AS [Pedido],
                CONVERT (VARCHAR, [Fecha entrega], 103) AS FechaEntrega,
				Item.[Codigo],
                Item.[Descripcion],				
                Item.[CantidadCompletada],
                Item.[CantidadSolicitada],
                Item.[Procesado],
                Item.[Pendiente]
                ,Item.[LineNum]
                ,OITM.DfltWH
                FROM
                (
                    SELECT
                        TaxDate AS [Fecha inicio],
                        DocTime,
                        (
                            SELECT
                            FldValue 
                            FROM
                            UFD1 
                            WHERE
                            FldValue = U_Prioridad 
                            AND TableID = 'ORDR' 
                            AND FieldID = 
                            (
                                SELECT
                                    FieldID 
                                FROM
                                    CUFD 
                                WHERE
                                    TableID = 'ORDR' 
                                    AND AliasID = 'Prioridad'
                            )
                        )
                        AS ValorPrioridad,
                        (
                            SELECT
                            Descr 
                            FROM
                            UFD1 
                            WHERE
                            FldValue = U_Prioridad 
                            AND TableID = 'ORDR' 
                            AND FieldID = 
                            (
                                SELECT
                                    FieldID 
                                FROM
                                    CUFD 
                                WHERE
                                    TableID = 'ORDR' 
                                    AND AliasID = 'Prioridad'
                            )
                        )
                        AS Prioridad,
                        CardCode AS [Número de cliente],
                        CardName AS [Razón social],
                        NumAtCard AS OC,
                        DocNum AS Pedido,
                        DocDueDate AS [Fecha entrega]
                      
                    FROM
                        ORDR
						 
                    WHERE
                        U_Prioridad is not NULL 
                        AND 
                        (
                            U_Procesado = 'N'
                        )
                        AND DocStatus = 'O' 
                )     TMP 
				LEFT JOIN (
						SELECT T0.DocEntry, T0.DocNum[Pedido], T1.LineNum, T1.ItemCode[Codigo],
	T1.Dscription[Descripcion],
	ISNULL((SELECT SUM(ISNULL(CmpltQty,0)) FROM OWOR WHERE  
	ItemCode = T1.ItemCode And OriginAbs = T0.DocEntry ),0) [CantidadCompletada],
	T1.WhsCode[Almacen], T1.Quantity[CantidadSolicitada],ISNULL(T1.U_Procesado,0)[Procesado],
	(T1.Quantity-ISNULL(T1.U_Procesado,0))[Pendiente]
	FROM dbo.ORDR T0
	INNER JOIN dbo.RDR1 T1 ON T0.DocEntry = T1.DocEntry
	WHERE  T1.Quantity > ISNULL(T1.U_Procesado,0)
						) as Item on Item.Pedido = TMP.Pedido
    inner join OITM on OITM.ItemCode = Item.[Codigo]
	AND OITM.InvntItem = 'Y'
    left join OITT on OITT.Code = 	Item.[Codigo]						
	ORDER BY
                ValorPrioridad, [Fecha inicio], DocTime";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);
    //U_LineNum = T1.LineNum AND
            $pedidos_gop = collect($consulta);
            return compact('pedidos_gop');
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
public function registros_gop_pedido(Request $request){
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);            
            $pedido = $request->input('cbopedido');
            $sel = "SELECT 
                    OW.DocEntry as OP,
					OW.ProdName AS Descripcion,
					OW.ItemCode AS Codigo,
                    OW.U_NoSerie AS Serie,
                    CASE OW.Status
                    WHEN 'R' THEN 'LIBERADA'
                    WHEN 'P' THEN 'PLANIFICADA' END AS Estado
                    FROM OWOR OW
                    INNER JOIN OITM O on OW.ItemCode = O.ItemCode				
                    WHERE  
                     OW.Status in ('P', 'R')
                     AND OW.OriginNum = ?
                     AND O.U_TipoMat = 'PT'
                    ORDER BY OW.DocEntry";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel,[$pedido]);
    //U_LineNum = T1.LineNum AND
            $datos = collect($consulta);
            return compact('datos');
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
public function countRollout(Request $request){
    try {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $jobs = DB::select("SELECT queue from jobs
            where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
        if (count($jobs) > 0) {
            $count = -1;
        }else{
            $priceList = (int) $request->input('priceList');         
            $articulos = DB::select('exec SIZ_SP_ROLLOUT_SIMULADOR_COSTOS ?', [$priceList]);
            $count = count($articulos);
        }//end count jobs        
        return compact('count');
    } catch (\Exception $e) {
        Cache::forget('roll');
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
public function processRollout(Request $request){
    try {
        ini_set('memory_limit', '-1');
        set_time_limit(0);            
        $sel = "";
        $mensaje= '';            
        //if (Cache::has('hora_init_rollout')) {
          //  Cache::forget('hora_init_rollout');
        //}
        $fi = Carbon::now();
        DB::insert("INSERT INTO SIZ_PROCESOS_JOBS ( PROJO_Name, PROJO_FechaInicio, PROJO_Usuario) VALUES (?,?,?)", ["Rollout", $fi, Auth::user()->U_EmpGiro]);
        
       //Cache::forever('hora_init_rollout', $fi);
        // LALog::warning("fi desig");    
        // LALog::warning($fi);    
                $index = 1;
                $priceList = (int) $request->input('priceList'); 
                //while ($index >= 1) {
                    $articulos = DB::select('exec SIZ_SP_ROLLOUT_SIMULADOR_COSTOS ?', [$priceList]);
                    
                    //clock($index.'-'.$priceList);
                    //clock($articulos);
                    //clock(count($articulos));
                    if (count($articulos) > 0) {
                        
                        $mensajeErr= '';
                        foreach ($articulos as $key => $articulo) {
                            $codigo = $articulo->ItemCode;
                            $precio = $articulo->PRICE_SAVE;
                            $moneda = $articulo->MONEDA;
                            //$rs = SAP::updateItemPriceList($codigo, $priceList , $precio, $moneda); 
                            //clock($codigo, $rs);
                            /* if($rs !== 'ok'){
                                $mensajeErr = 'Error : Art#'.$codigo.', SAP:'.$rs;
                                $mensaje = $mensaje.$mensajeErr;
                            } */
                        //break;
                        $user = Auth::user()->U_EmpGiro;
                        $this->dispatch((new ItemPrecioUpdate($codigo, $priceList , $precio, $moneda, $user))->onQueue('ItemPrecioUpdate'));
                        
                        }
                        $index++;
                        //clock($mensajeErr);
                    } else {
                        $index = 0;
                 //       Cache::forget('roll');
                    }
                    
                //}//end while
            
        //} else {
       //     $mensaje = 'Aviso: existe otro ROLLOUT en proceso.';
       // }
        //Cache::forget('roll');
        
        return compact('mensaje');
    } catch (\Exception $e) {
        //Cache::forget('roll');
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
public function cancelProcessRollout(Request $request){
    try {
        ini_set('memory_limit', '-1');
        set_time_limit(0); 


        /* DB::table('jobs')
            ->where('queue', 'ItemPrecioControl')
            ->where('queue', 'ItemPrecioUpdate')
            ->update(['queue' => 'stop']);  */

        DB::table('SIZ_PROCESOS_JOBS')->insert(
                ['PROJO_Name' => 'stop']
        );
         $jobs = DB::select("SELECT queue from jobs
            where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
        if (count($jobs) > 0) {
        while (count($jobs) > 0) {
            $jobs = DB::select("SELECT queue from jobs
            where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
            if (count($jobs) > 0) {
                DB::delete("delete jobs where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
                
            }
        }
         DB::delete("delete SIZ_PROCESOS_JOBS 
         where PROJO_Name = 'stop'");
        /*    
        \Artisan::call('queue:restart');           
        //\Artisan::call('queue:clear --queue=ItemPrecioUpdate');           
        DB::delete("delete jobs where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
        
        */
        $mensaje = 'ok';
        return compact('mensaje');
    } catch (\Exception $e) {
        //Cache::forget('roll');
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
public function registros_tabla_liberacion(Request $request){
             //dd($request->all());
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $estado = $request->input('estado');
            $tipo = $request->input('tipo');
            /*
            'tipo' => ['PT', 'CA', 'OTRO'],
            'estado' => ['Planificadas', 'Liberadas']
            */
            $criterio = "";
            switch ($estado) {
                case 0:
                    $estado = 'P'; //PLANIFICADAS
                    break;
                case 1:
                    $estado = 'R'; //LIBERADAS
                    break;
                    default:
                    $estado = 'P';
                    break;
            }
            switch ($tipo) {
                case 0:
                    $tipo = "dbo.OITM.U_TipoMat = 'PT'";//PRODUCTO TERMINADO
                    $criterio = " AND U_NoSerie > 1 ";
                    break;
                case 1:
                    $tipo = "dbo.OITM.U_TipoMat = 'CA'"; //CASCO
                    break;
                case 2:
                    $tipo = "dbo.OITM.U_TipoMat <> 'PT' AND dbo.OITM.U_TipoMat <> 'CA'"; //DIFENTES A LAS ANTERIORES (SUBENSAMBLES)
                    break;
                    default:
                    $tipo = "dbo.OITM.U_TipoMat = 'PT'";//PRODUCTO TERMINADO
                    break;
            }
            //dd([$estado, $tipo]);

            $sel = "SELECT
					CASE OWOR.Status
                    WHEN 'R' THEN 'LIBERADA'
                    WHEN 'P' THEN 'PLANIFICADA' END Estado,
				dbo.ORDR.DocNum AS Pedido,
				dbo.OWOR.DocNum AS OP, 
                dbo.OWOR.ItemCode AS Codigo, 
                SUBSTRING(dbo.OITM.ItemName, 0, 50) AS Descripcion,
				dbo.ORDR.CardCode +' - '+ dbo.ORDR.CardName AS Cliente, 
                OWOR.U_Starus 
                FROM      dbo.ORDR INNER JOIN
                                dbo.OWOR ON dbo.ORDR.DocNum = dbo.OWOR.OriginNum INNER JOIN
                                dbo.OITM ON dbo.OWOR.ItemCode = dbo.OITM.ItemCode
                WHERE   (Status = '". $estado ."') AND (". $tipo .")
                ".$criterio."
                ORDER BY Pedido";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);
            //dd($sel);
            $tabla_liberacion = collect($consulta);
            return compact('tabla_liberacion');
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
    public function registros_tabla_programar(Request $request)
    {
        //dd($request->all());
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $estado = $request->input('estado');
            $tipo = $request->input('tipo');
            /*
            'tipo' => ['PT', 'CA', 'OTRO'],
            'estado' => ['Planificadas', 'Liberadas']
            */
           
            switch ($estado) {
                case 0:
                    $estado = 'P'; //PLANIFICADAS
                    break;
                case 1:
                    $estado = 'R'; //LIBERADAS
                    break;
                default:
                    $estado = 'P';
                    break;
            }
             
             switch ($tipo) {
                case 0:
                    $tipo = "dbo.OITM.QryGroup31 = 'N' AND dbo.OITM.QryGroup32 = 'N' AND dbo.OITM.QryGroup30 = 'N' AND dbo.OITM.QryGroup29 = 'N'"; //FUNDAS
                    break;
                case 1:
                    $tipo = "dbo.OITM.QryGroup29 = 'Y'"; //CASCO 29
                    break; 
                case 2:
                    $tipo = "dbo.OITM.QryGroup31 = 'Y'"; //PATAS Y BAST 31
                    break;
                case 3:
                    $tipo = "dbo.OITM.QryGroup32 = 'Y'"; //SUBENSAMBLES 32
                    break;
                case 4:
                    $tipo = "dbo.OITM.QryGroup30 = 'Y'"; //HABILITADO 30
                    break;   
            }
            //dd([$estado, $tipo]);
            
           // $estado = 'R';
           // $tipo = "dbo.OITM.U_TipoMat = 'PT'";
            $sel = "SELECT
            OWOR.Status,
            OITM.QryGroup29,
            OITM.QryGroup30,
            OITM.QryGroup31,
            OITM.QryGroup32,
                        OWOR.OriginNum AS PEDIDO,
                        OWOR.CARDCODE,
                        SUBSTRING(OCRD.CARDNAME, 0, 35) CARDNAME,
                        OWOR.DOCNUM,
                        OWOR.U_C_Orden AS PRIORIDAD,
                        OWOR.ITEMCODE,
                        SUBSTRING(OITM.ITEMNAME,0, 35) ITEMNAME,
                        OWOR.U_FCOMPRAS,
                        OWOR.U_FPRODUCCION,
						OWOR.U_GRUPO AS PROG_CORTE,
                        OWOR.U_Ubicacion AS METALES,
						OWOR.U_OF AS HULE,
						OWOR.U_OT AS SEC_OT,
                        CASE
                           WHEN
                              U_Starus IS NULL 
                           THEN
                              'POR PROGRAMAR' 
                           ELSE
                              CASE
                                 WHEN
                                    U_Starus = '01' 
                                 THEN
                                    'DET VENTAS' 
                                 ELSE
                                    CASE
                                       WHEN
                                          U_Starus = '02' 
                                       THEN
                                          'FALTA INF.' 
                                       ELSE
                                          CASE
                                             WHEN
                                                U_Starus = '03' 
                                             THEN
                                                'FALTA PIEL' 
                                             ELSE
                                                CASE
                                                   WHEN
                                                      U_Starus = '04' 
                                                   THEN
                                                      'REV. PIEL' 
                                                   ELSE
                                                      CASE
                                                         WHEN
                                                            U_Starus = '05' 
                                                         THEN
                                                            'POR ORDENAR' 
                                                         ELSE
                                                            CASE
                                                               WHEN
                                                                  U_Starus = '06' 
                                                               THEN
                                                                  'EN PROCESO' 
                                                            END
                                                      END
                                                END
                                          END
                                    END
                              END
                        END
                        AS U_STARUS, ORDR.DOCDUEDATE, CP.U_CT 
                     FROM
                        OWOR 
                        INNER JOIN
                           OITM 
                           ON OWOR.ITEMCODE = OITM.ITEMCODE 
                        INNER JOIN
                           OCRD 
                           ON OWOR.CardCode = OCRD.CardCode 
                        LEFT JOIN
                           [@CP_OF] CP 
                           ON OWOR.DocNum = CP.U_DocEntry 
                        LEFT JOIN
                           ORDR 
                           ON OWOR.OriginNum = ORDR.DocNum 
                     WHERE
                     (Status = '" . $estado . "') AND (" . $tipo . ") AND  OWOR.PlannedQty <> OWOR.CmpltQty
                        ORDER BY  OWOR.U_FPRODUCCION, OWOR.DOCNUM";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);
            //dd($sel);
            $tabla_programar = collect($consulta);
            return compact('tabla_programar');
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
    public function registros_tabla_impresion(Request $request)
    {
        //dd($request->all());
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $estado = $request->input('estado');
            $tipo = $request->input('tipo');
            /*
            'tipo' => ['PT', 'CA', 'OTRO'],
            'estado' => ['Planificadas', 'Liberadas']
            */
            /*
            switch ($estado) {
                case 0:
                    $estado = 'P'; //PLANIFICADAS
                    break;
                case 1:
                    $estado = 'R'; //LIBERADAS
                    break;
                default:
                    $estado = 'P';
                    break;
            }
            switch ($tipo) {
                case 0:
                    $tipo = "dbo.OITM.U_TipoMat = 'PT'"; //PRODUCTO TERMINADO
                    break;
                case 1:
                    $tipo = "dbo.OITM.U_TipoMat = 'CA'"; //CASCO
                    break;
                case 2:
                    $tipo = "dbo.OITM.U_TipoMat <> 'PT' AND dbo.OITM.U_TipoMat <> 'CA'"; //DIFENTES A LAS ANTERIORES (SUBENSAMBLES)
                    break;
                default:
                    $tipo = "dbo.OITM.U_TipoMat = 'PT'"; //PRODUCTO TERMINADO
                    break;
            }
            //dd([$estado, $tipo]);
            */
            $estado = 'R';
            $tipo = "dbo.OITM.U_TipoMat = 'PT'";
            $sel = "SELECT
					CASE OWOR.Status
                    WHEN 'R' THEN 'LIBERADA'
                    WHEN 'P' THEN 'PLANIFICADA' END Estado,
				dbo.ORDR.DocNum AS Pedido,
				dbo.OWOR.DocNum AS OP, 
                dbo.OWOR.ItemCode AS Codigo, 
                dbo.OITM.ItemName AS Descripcion,
				dbo.ORDR.CardCode +' - '+ dbo.ORDR.CardName AS Cliente,
                OWOR.U_GRUPO AS PROG_CORTE,
                OWOR.U_OF AS SEC_COMPRA,
                OWOR.U_OT AS SEC_OT
                FROM      dbo.ORDR INNER JOIN
                                dbo.OWOR ON dbo.ORDR.DocNum = dbo.OWOR.OriginNum INNER JOIN
                                dbo.OITM ON dbo.OWOR.ItemCode = dbo.OITM.ItemCode
                WHERE   (Status = '" . $estado . "') AND (" . $tipo . ")
                AND U_NoSerie > 1 AND (U_Impreso = 0 OR U_Impreso IS NULL)
                ORDER BY Pedido";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);
            //dd($sel);
            $tabla_impresion = collect($consulta);
            return compact('tabla_impresion');
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
public function registros_tabla_series(){
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            
            $sel = "SELECT O.u_tipoMat, OW.DocEntry from OWOR OW
					INNER JOIN OITM O on OW.ItemCode = O.ItemCode
					WHERE U_NoSerie = 1 AND Status in ('P', 'R' ) AND O.U_TipoMat <> 'PT'";
            $subensambles = DB::select($sel);
            foreach ($subensambles as $key => $value) {
                SAP::updateSerieOrden($value->DocEntry, '2');
            }

            $sel = "SELECT 
                    '0' [Grupal],
                    OW.DocEntry as Orden,
                    OW.OriginNum AS Pedido, 
					OW.ProdName AS Descripcion,
					OW.ItemCode AS Codigo,
                   (SELECT CardName  FROM OCRD WHERE	OCRD.CardCode = OW.CardCode) Cliente,
                    OW.U_NoSerie AS NumSerie,
                    CASE OW.Status
                    WHEN 'R' THEN 'LIBERADA'
                    WHEN 'P' THEN 'PLANIFICADA' END AS Estado,
                   (SELECT U_TipoMat  FROM OITM WHERE	OITM.ItemCode = OW.ItemCode) TipoMaterial
                    FROM OWOR OW					
                    WHERE  
                    OW.U_NoSerie = 1
                    AND OW.Status in ('P', 'R' )
                    ORDER BY OW.DocEntry";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);
            
            $tabla_series = collect($consulta);
            return compact('tabla_series');
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
public function actualizaMRP(){
        DB::update("exec SIZ_NEWMRP");
        
        Session::flash('mensaje', "MRP actualizado...");
        
        return redirect('home/MRP');
}
    public function DataShowMRP(Request $request)
    {
        if (Auth::check()) {
        $fecha = $request->get('fechauser');
        $tipo = $request->get('tipo');
        

         //   $fecha = Input::get('text_selUno');
         //   $tipo = Input::get('text_selDos');
         //   $accion = Input::get('text_selTres');  
         $consulta = '';
        switch ($tipo) {
            case 'Completo': 
               $tipo = '%';
                break;
            case 'Proyección':
                $tipo = 'P';
                break;
            case 'Con Orden':
                $tipo = 'C';
                break;
        }
        switch ($fecha) {
            case 'Producción':
                    // $consulta = DB::table('SIZ_T_MRP')
                    //     ->select(DB::raw('fechaDeEjecucion, Descr, Itemcode, ItemName, UM, ExistGDL, ExistLERMA, WIP, sum(S0) S0, sum(S1)S1, sum(S2)S2, sum(S3)S3, sum(S4)S4, sum(S5)S5, sum(S6)S6, sum(S7)S7, sum(S8)S8, sum(S9)S9, sum(S10)S10, sum(S11)S11, sum(S2)S12, sum(S13)S13, sum(S14)S14, sum(S15)S15, sum(S16)S16, sum(S17)S17, sum(S18)S18, sum(S19)S19, sum(necesidadTotal)necesidadTotal, OC, Reorden, Minimo, Maximo, TE, Costo,Proveedor, Comprador'))
                    //     ->where('U_C_Orden', 'like', $tipo)
                    //     ->groupBy( "fechaDeEjecucion", 'Descr', 'Itemcode', 'ItemName', 'UM', 'ExistGDL', 'ExistLERMA', 'WIP', 'Costo', 'Proveedor', 'Comprador', 'Reorden', 'Maximo', 'Minimo', 'TE', 'OC');
                    $consulta = DB::select('exec SIZ_SP_MRP ?, ?', ['semana', $tipo]);
                
                break;
            case 'Compras':
                    // $consulta = DB::table('SIZ_T_MRP')
                    //     ->select(DB::raw( 'fechaDeEjecucion, Descr, Itemcode, ItemName, UM, ExistGDL, ExistLERMA, WIP, sum(Sc0) S0, sum(Sc1)S1, sum(Sc2)S2, sum(Sc3)S3, sum(Sc4)S4, sum(Sc5)S5, sum(Sc6)S6, sum(Sc7)S7, sum(Sc8)S8, sum(Sc9)S9, sum(Sc10)S10, sum(Sc11)S11, sum(Sc2)S12, sum(Sc13)S13, sum(Sc14)S14, sum(Sc15)S15, sum(Sc16)S16, sum(Sc17)S17, sum(Sc18)S18, sum(Sc19)S19, sum(necesidadTotal)necesidadTotal, OC, Reorden, Minimo, Maximo, TE, Costo,Proveedor, Comprador'))
                    //     ->where('U_C_Orden', 'like', $tipo)
                    //     ->groupBy( "fechaDeEjecucion", 'Descr', 'Itemcode', 'ItemName', 'UM', 'ExistGDL', 'ExistLERMA', 'WIP', 'Costo', 'Proveedor', 'Comprador', 'Reorden', 'Maximo', 'Minimo', 'TE', 'OC');
                    $consulta = DB::select('exec SIZ_SP_MRP ?, ?', ['semana_c', $tipo]);
                break;
        }
        //Definimos las columnas del MRP
        $columns = array(
            ["data" => "Itemcode", "name" => "Código"],
            ["data" => "ItemName", "name" => "Descripción"],
            ["data" => "Descr", "name" => "Grupo"],
            ["data" => "UM", "name" => "UM"],
            ["data" => "ExistGDL", "name" => "Ext. Gdl"],
            ["data" => "ExistLERMA", "name" => "Ext. Lerma"],
            ["data" => "WIP", "name" => "WIP"],
            //["data" => "", "name" => ""],            
        );
            $columns_xls = array(
                ["data" => "Descr", "name" => "Grupo"],
                ["data" => "Itemcode", "name" => "Código"],
                ["data" => "ItemName", "name" => "Descripción"],
                ["data" => "UM", "name" => "UM"],
                ["data" => "ExistGDL", "name" => "EXISTENCIA GDL"],
                ["data" => "ExistLERMA", "name" => "EXISTENCIA LERMA"],
                ["data" => "WIP", "name" => "WIP"],
                //["data" => "", "name" => ""],            
            );
        //dd(array_has($consulta[0], 'ant'));
        //Si existe Cant Anterior agregamos la columna
        //dd($consulta[0]);
        if ( isset( $consulta[0]->ant ) ) {
            array_push($columns,["data" => "ant", "name" => "Anterior"]);
            array_push($columns_xls,["data" => "ant", "name" => "Anterior"]);
        } 
        //Obtenemos solo las columnas numericas para agregarlas al Array Columnas
        $numerickeys = array_where(array_keys((array)$consulta[0]), function ($key, $value) {
                    return is_numeric($value);
                });
        //Antes de agregar hay que ordenar las columnas numericas obtenidas
        sort($numerickeys);
        //agregar columnas...  hasta 2099 usar 20, para 2100 a 2199 usar 21...
        $string_comienzo_anio = '20';
        foreach ($numerickeys as $value) {
            //averiguamos cuando inicia la semana
            $num_semana = substr($value, 2, 2);
            $year = $string_comienzo_anio. substr($value, 0, 2);
            $StartAndEnd=\AppHelper::instance()->getStartAndEndWeek($num_semana, $year);
            
            //preparamos el nombre
            $name = 'Sem-'.$num_semana.' '.$StartAndEnd['week_start'];
            array_push($columns,["data" => $value, "name" => $name, "defaultContent"=> ".00"]);
            array_push($columns_xls,["data" => $value, "name" => $name, "defaultContent"=> ".00"]);
        }
       
        //agregamos las ultimas columnas pendientes
        array_push($columns,["data" => "necesidadTotal", "name" => "Necesidad"]);
        array_push($columns,["data" => "Necesidad", "name" => "Disp. S/WIP"]);
        array_push($columns,["data" => "OC", "name" => "OC", "defaultContent" => ".00"]);
        array_push($columns,["data" => "Reorden", "name" => "P. Reorden"]);
        array_push($columns,["data" => "Minimo", "name" => "S. Minimo"]);
        array_push($columns,["data" => "Maximo", "name" => "S. Maximo"]);
        array_push($columns,["data" => "TE", "name" => "T.E."]);
        array_push($columns,["data" => "Costo", "name" => "Costo Compras"]);
        array_push($columns,["data" => "Moneda", "name" => "Moneda"]);
        array_push($columns,["data" => "Proveedor", "name" => "Proveedor"]);
        array_push($columns,["data" => "Comprador", "name" => "Comprador"]);
        
        array_push($columns_xls,["data" => "necesidadTotal", "name" => "Necesidad"]);
        array_push($columns_xls,["data" => "Necesidad", "name" => "Disp. S/WIP"]);
        array_push($columns_xls,["data" => "OC", "name" => "OC", "defaultContent" => ".00"]);
        array_push($columns_xls,["data" => "Reorden", "name" => "P. Reorden"]);
        array_push($columns_xls,["data" => "Minimo", "name" => "S. Minimo"]);
        array_push($columns_xls,["data" => "Maximo", "name" => "S. Maximo"]);
        array_push($columns_xls,["data" => "TE", "name" => "T.E."]);
        array_push($columns_xls,["data" => "Costo", "name" => "Costo Compras"]);
        array_push($columns_xls,["data" => "Moneda", "name" => "Moneda"]);
        array_push($columns_xls,["data" => "Proveedor", "name" => "Proveedor"]);
        array_push($columns_xls,["data" => "Comprador", "name" => "Comprador"]);
        



        
        
        return response()->json(array('data'=>$consulta, 'columns'=>$columns, 'columnsxls'=> $columns_xls));
            //collect($consulta)->toJson());
      
            // dd( Datatables::of(collect($consulta))
            //     // ->addColumn('Resto', function ($consulta) {
            //     //     return ($consulta->S13 + $consulta->S14 + $consulta->S15 + $consulta->S16 + $consulta->S17 + $consulta->S18 + $consulta->S19);
            //     // })
            //     ->addColumn('Necesidad', function ($consulta) {
            //         return ($consulta->ExistGDL + $consulta->ExistLERMA) - $consulta->necesidadTotal;
            //     })  
            //     ->make(true));
        } else {
            return redirect()->route('auth/login');
        }
    }
    
      
    public function mrpPDF() 
    {
        $data = json_decode(Session::get('mrp'));
      //  dd($data);
        $pdf = \PDF::loadView('Mod02_Planeacion.ReporteMrpPDF', compact('data'));
        $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true]);  
        return $pdf->stream('Siz_MRP' . ' - ' . date("d/m/Y") . '.Pdf');
    }

    public function mrpXLS()
    {
        if (Auth::check()) {
        $path = public_path() . '/assets/plantillas_excel/Mod_02/mrp.xlsx';
        $data = json_decode(Session::get('mrp'), true);
        //se obtienen los nombres de las columnas
        $name_cols = array_pluck( json_decode(Session::get('cols'), true) , ['name']);
        //se obtienen las keys para obtener los valores de cada fila   
        $data_cols = array_pluck(json_decode(Session::get('cols'), true), ['data']);
        $mrp_parameter = Session::get('parameter'); 
        Excel::load($path, function ($excel) use ($data, $name_cols, $data_cols, $mrp_parameter) {
            $excel->sheet('General', function ($sheet) use ($data, $name_cols, $data_cols, $mrp_parameter) {
                $sheet->cell('A4', function ($cell) {
                    $cell->setValue("Fecha de Impresión: ".\AppHelper::instance()->getHumanDate(date("Y-m-d H:i:s")).' '. date("H:i:s"));
                });
                //obtenemos primer fila y la fecha de ejecucion
                $fechaEjecucion =$data[0][ 'fechaDeEjecucion'];
                //se coloca titulo del archivo 
                $sheet->cell('A2', function ($cell)  use ($mrp_parameter){
                    $cell->setValue($mrp_parameter);
                });
                //se coloca fecha de Ejecucion 
                $sheet->cell('A5', function ($cell)  use ($fechaEjecucion){
                    $cell->setValue('Fecha de Actualización: ' . \AppHelper::instance()->getHumanDate($fechaEjecucion));
                });                
                //se colocan los nombres de las columnas en el xls
                $sheet->row(6, $name_cols);
                //obtiene la ultima columna por texto: ejem. "F"      
                $column = \PHPExcel_Cell::stringFromColumnIndex(count($name_cols)-1);
                //ultima celda de encabezado seria:
                $cell = $column . '6';
                //el rango al que quiero aplicar estilo de encabezado
                $range = 'A6:' . $cell;
                $sheet->getStyle($range)->
                applyFromArray(
                    array(
                        'font' => array(
                            'name'      =>  'Arial',
                            'size'      =>  11,
                            'bold'      =>  true,
                            'color' => array('rgb' => '473AC9')
                        ),
                        'borders' => array(
                            'outline' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THICK,
                                
                            ),
                        ),
                    ));
                $sheet->setAutoFilter($range); //esto agrega un filtro encabezados
                $index = 7; // se inicia llenado de datos
                foreach ($data as $row) {
                    //se elabora la fila de acuedo a la cantidad de columnas (de acuerdo al nombre)
                    $fila = [];
                    foreach ($data_cols as $key) {
                        array_push($fila, $row[$key] ?: '0');
                    }
                    //se coloca la fila en el XLS
                    $sheet->row($index, $fila);

                    $index++;
                }
                
                $cant = count($data)+6; //+6 por las primeras filas
                $sheet->getColumnDimension('C')->setAutoSize(false);//ajusta ancho de celda segun texto
                $sheet->getColumnDimension('C')->setWidth(46);
                //ultima columna
                $sheet->getColumnDimension($column)->setAutoSize(true);
                
                //penultima columna
                $column2 = \PHPExcel_Cell::stringFromColumnIndex(count($name_cols) - 2);
                $sheet->getColumnDimension($column2)->setAutoSize(false);
                $sheet->getColumnDimension($column2)->setWidth(40);
                
                //formato para columnas con numeros (negativos rojo y centrados)
                $ultima_column_numero = \PHPExcel_Cell::stringFromColumnIndex(count($name_cols) - 3);
                $sheet->getStyle('E7:'.$ultima_column_numero.$cant)->getNumberFormat()->setFormatCode( '#,##0.00;[red]-#,##0.00');
                $sheet->getStyle('D7:'.$ultima_column_numero.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                //relleno encabezado semanas otro color
                $ultima_column_sem =\PHPExcel_Cell::stringFromColumnIndex(count($name_cols) - 11);
                $sheet->getStyle('H6:' . $ultima_column_sem . '6')->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'startcolor' => array('rgb' => 'E78ABF')
                        ),
                    )
                );

                //relleno blanco para todas las columnas
                $sheet->getStyle('A7:'.$column.$cant)->applyFromArray(
                        array(
                            'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                              'startcolor' => array( 'rgb' => 'FFFFFF' )
                            ),
                        )
                    );
                //$sheet->getStyle('B6:B'.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                //alinear texto de estas columnas a la izquierda (se le pasa el rango de hasta donde hay datos en la columna)
                //$sheet->getStyle('C6:C'.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                //ultima columna
                //$sheet->getStyle($column.'6:'.$column.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
               //penultima columna
                //$sheet->getStyle($column2.'6:'.$column2.$cant)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            });
        })
            ->setFilename('SIZ Resumen de MRP')
           ->export('xlsx', [ 'Set-Cookie' => 'xlscook=done; path=/;' ]);
        } else {
        return redirect()->route('auth/login');
        }
    }
  
}