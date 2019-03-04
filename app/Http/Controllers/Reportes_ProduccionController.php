<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\OP;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Datatables;
ini_set('max_execution_time', 360);
class Reportes_ProduccionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Mod00_Administrador.admin');
    }

    public function Produccion1(Request $request)
    {

        $enviado = $request->input('send');
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();

            if ($enviado == 'send') {
                $departamento = $request->input('dep');
                //  $fecha = explode(" - ",$request->input('date_range'));
                //$dt = date('d-m-Y H:i:s');
                $fechaI = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechIn'))));
                $fechaF = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechaFa'))));
                $fecha_desde = strtotime($request->input('FechIn'));
                $fecha_hasta = strtotime($request->input('FechaFa'));
                if ($fecha_hasta >= $fecha_desde) {
                    $clientes = DB::select('SELECT CardName from  "CP_ProdTerminada" WHERE  (fecha>=\'' . $fechaI . '\' AND
  fecha<=\'' . $fechaF . '\') AND
 (Name= (\'' . $departamento . '\')  OR Name= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 GROUP BY CardName, fecha, Name');
                    $produccion = DB::select('SELECT "CP_ProdTerminada"."orden", "CP_ProdTerminada"."Pedido", "CP_ProdTerminada"."Codigo",
 "CP_ProdTerminada"."modelo", "CP_ProdTerminada"."VS", "CP_ProdTerminada"."fecha",
 "CP_ProdTerminada"."CardName", 
 "CP_ProdTerminada"."Cantidad", "CP_ProdTerminada"."TVS"
 FROM   "CP_ProdTerminada" "CP_ProdTerminada"
 WHERE  ("CP_ProdTerminada"."fecha">=\'' . $fechaI . '\' AND
 "CP_ProdTerminada"."fecha"<=\'' . $fechaF . '\') AND
 ("CP_ProdTerminada"."Name"= (\'' . $departamento . '\')  OR "CP_ProdTerminada"."Name"= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 ORDER BY "CP_ProdTerminada"."CardName", "CP_ProdTerminada"."orden"');
                } else {
                    return redirect()->back()->withErrors(array('message' => 'de rango de Fechas'));
                }
                $result = json_decode(json_encode($produccion), true);
                $finalarray = [];
                foreach ($clientes as $client) {
                    $miarray = array_filter($result, function ($item) use ($client) {
                        return $item['CardName'] == $client->CardName;
                    });
                    $finalarray[$client->CardName] = $miarray;
                }
                //dd(($finalarray['CASTRO HERRERA ALEJANDRO ISAAC'][0]['orden']));
                $values = ['produccion' => $produccion, 'actividades' => $actividades, 'ultimo' => count($actividades), 'ofs' => $finalarray, 'departamento' => $departamento, 'fechaI' => $fechaI, 'fechaF' => $fechaF, 'tvs' => 0, 'cant' => 0];
                Session::flash('Ocultamodal', 1);
                //dd($produccion);
                $pdf_array = [
                    $produccion,
                    'del día ' . $fechaI . ' al ' . $fechaF,
                    $departamento
                ];
                Session::put('repP', $values);
                Session::put('pdf_array', $pdf_array);
                return view('Mod01_Produccion.produccionGeneral', $values);
                $compiled = view('Mod01_Produccion.produccionGeneral', $values)->render();
            } else {
                Session::flash('Ocultamodal', false);
                return view('Mod01_Produccion.produccionGeneral', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
            }
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function ReporteProduccionPDF()
    {
        $pdf_array = Session::get('pdf_array');
        $valores = $pdf_array[0];
        $fecha = $pdf_array[1];
        $depto = $pdf_array[2];
        $pdf = \PDF::loadView('Mod01_Produccion.produccionGeneralPDF', compact('valores', 'fecha', 'depto'));
        //$pdf = new FPDF('L', 'mm', 'A4');
        $pdf->setOptions(['isPhpEnabled' => true]);
        //        Session::forget('values');
        return $pdf->stream('Siz_Reporte_Produccion ' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }
    public function ReporteProduccionEXL()
    {
        if (Session::has('repP')) {
            $values = Session::get('repP');
            Excel::create('Siz_Reporte_Produccion_General' . ' - ' . $hoy = date("d/m/Y") . '', function ($excel) use ($values) {
                $excel->sheet('Hoja 1', function ($sheet) use ($values) {
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, [
                        'Cliente', 'Fecha', 'Orden', 'Pedido', 'Código', 'Modelo', 'VS', 'Cantidad', 'Total VS'
                    ]);
                    //Datos    
                    $fila = 2;
                    foreach ($values['produccion'] as $produccion) {
                        //  $tvs= $tvs + $produccion->TVS;
                        //$cant = $cant + $produccion->Cantidad;
                        $sheet->row(
                            $fila,
                            [
                                $produccion->CardName,
                                substr($produccion->fecha, 0, 10),
                                $produccion->orden,
                                $produccion->Pedido,
                                $produccion->Codigo,
                                $produccion->modelo,
                                $produccion->VS,
                                $produccion->Cantidad,
                                $produccion->TVS,
                                //  $produccion->cant,
                                //$produccion->tvs,
                            ]
                        );
                        $fila++;
                    }
                });
            })->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function showModal(Request $request)
    {

        $nombre = str_replace('%20', ' ', explode('/', $request->path())[1]);

        switch ($nombre) {
            case "HISTORIAL OP":
                $fechas = false;
                $fieldOtroNumber = 'OP';
                $fieldOtroText = '';
                break;
            case "MATERIALES OP":
                $fechas = false;
                $fieldOtroNumber = 'OP';
                $fieldOtroText = '';
                break;
            default:
                $fechas = false;
                $fieldOtroNumber = ''; //se usa por ejemplo para el valor de OP              
                $fieldOtroText = '';
        }


        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            return view('Mod01_Produccion.modalParametros', ['actividades' => $actividades, 'ultimo' => count($actividades), 'nombre' => $nombre, 'fieldOtroNumber' => $fieldOtroNumber, 'fieldOtroText' => $fieldOtroText, 'fechas' => $fechas]);
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function historialOP(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();

            $op = $request->input('fieldOtroNumber');
            $consulta = DB::select(DB::raw("SELECT [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,OHEM.firstName + ' ' + OHEM.lastName AS Empleado,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)) AS FechaF ,
       [@CP_LOGOF].U_DocEntry  ,OWOR.ItemCode , OITM.ItemName ,
        SUM([@CP_LOGOF].U_Cantidad) AS U_CANTIDAD,
        sum(oitm.U_VS ) AS VS,
        (SELECT CompnyName FROM OADM ) AS CompanyName
        FROM [@CP_LOGOF] inner join [@PL_RUTAS] ON [@CP_LOGOF].U_CT = [@PL_RUTAS].Code
        left join OHEM ON [@CP_LOGOF].U_idEmpleado = OHEM.empID
        inner join OWOR ON [@CP_LOGOF].U_DocEntry = OWOR.DocNum
        inner join OITM ON OWOR.ItemCode = OITM.ItemCode
        WHERE U_DocEntry = $op
        GROUP BY [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,
        OHEM.firstName + ' ' + OHEM.lastName ,
         DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)),[@CP_LOGOF].U_DocEntry
        ,OWOR.ItemCode , OITM.ItemName
        ORDER BY [@CP_LOGOF].U_CT, FechaF, Empleado"));
            Session::put('rephistorial', $consulta);

            $info = OP::getInfoOwor($op);
            switch ($info->Status) {
                case "P":
                    $status = 'Planificada';
                    break;
                case "R":
                    $status = 'Liberada';
                    break;
                case "L":
                    $status = 'Cerrada';
                    break;
                case "C":
                    $status = 'Cancelada';
                    break;
            }
            $data = array(
                'data' => $consulta,
                'op' => $op,
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'info' => $info,
                'status' => $status
            );
            return view('Mod01_Produccion.ReporteHistorialOP', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function historialOPXLS()
    {
        if (Session::has('rephistorial')) {
            $values = Session::get('rephistorial');
            if (count($values) == 0) {
                return redirect()->back()->withErrors(array('message' => '!!Sin registros!!'));
            }
            Excel::create('Siz_Reporte_HistorialOP' . ' - ' . $hoy = date("d/m/Y") . '', function ($excel) use ($values) {
                $excel->sheet('Hoja 1', function ($sheet) use ($values) {
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, [
                        'Código: ' . $values[0]->ItemCode, 'Descripción: ' . $values[0]->ItemName
                    ]);
                    $sheet->row(3, [
                        'Fecha', 'Estación', 'Empleado', 'Cantidad'
                    ]);
                    //Datos    
                    $fila = 4;
                    foreach ($values as $fil) {
                        //  $tvs= $tvs + $produccion->TVS;
                        //$cant = $cant + $produccion->Cantidad;
                        $sheet->row(
                            $fila,
                            [
                                date('d-m-Y', strtotime($fil->FechaF)),
                                $fil->NAME,
                                $fil->Empleado,
                                $fil->U_CANTIDAD
                            ]
                        );
                        $fila++;
                    }
                });
            })->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function materialesOP(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();

            $op = $request->input('fieldOtroNumber');
            $consulta = DB::select(DB::raw("SELECT b.DocNum AS DocNumOf,
        '*' + CAST(b.DocNum as varchar (50)) + '*' as CodBarras,
        b.ItemCode,
        c.ItemName,
        c.U_VS AS VS,
        d.CardCode,
        d.CardName,
        d.DocNum AS DocNumP,
        b.DueDate AS FechaEntrega,
        b.plannedqty,
        d.Comments as Comentario,
        b.Comments,
        c.UserText,
        f.InvntryUom,
        --f.U_estacion as CodEstacion,
         '*' + cast(f.u_estacion as varchar (3)) + '*' as BarrEstacion,
        ISNULL((SELECT Name FROM [@PL_RUTAS] WHERE Code=f.U_Estacion),'Sin Estacion') AS Estacion,
        a.ItemCode AS Codigo,
        f.ItemName as Descripcion,
        a.PlannedQty AS Cantidad,
        0 AS [Cant. Entregada],
        0 AS [Cant. Devolución],
        --g.Father,
        b.U_NoSerie,
        f.U_Metodo,
        b.U_OF as origen,
        (SELECT TOP 1 ItemName FROM OITM INNER JOIN OWOR ON OITM.ITEMCODE = OWOR.ItemCode  WHERE OWOR.DocNum = b.U_OF ) as Funda
    FROM (WOR1 a
         INNER JOIN OWOR b ON a.DocEntry=b.DocEntry
         INNER JOIN OITM c ON b.ItemCode=c.ItemCode
         INNER JOIN ORDR d ON b.OriginAbs=d.DocEntry
         INNER JOIN OITM f ON a.ItemCode=f.ItemCode)
         --inner join ITT1 g on a.ItemCode  = g.Code and b.ItemCode = g.Father
    WHERE a.DocEntry=CONVERT(Int,$op)
       AND NOT (f.InvntItem='N' AND f.SellItem='N' AND f.PrchseItem='N' AND f.AssetItem='N')
       AND f.ItemName  not like  '%Gast%'
    ORDER BY CONVERT(INT, f.U_Estacion)"));
            //dd($consulta);
            Session::put('repmateriales', $consulta);
            $info = OP::getInfoOwor($op);
            Session::put('repinfo', $info);
            switch ($info->Status) {
                case "P":
                    $status = 'Planificada';
                    break;
                case "R":
                    $status = 'Liberada';
                    break;
                case "L":
                    $status = 'Cerrada';
                    break;
                case "C":
                    $status = 'Cancelada';
                    break;
            }
            $data = array(
                'data' => $consulta,
                'op' => $op,
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'db' => DB::getDatabaseName(),
                'info' => $info,
                'status' => $status
            );
            return view('Mod01_Produccion.ReporteMaterialesOP', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function materialesOPXLS()
    {
        if (Session::has('repmateriales')) {
            $values = Session::get('repmateriales');
            //dd($values);
            $info = Session::get('repinfo');
            if (count($values) == 0) {
                return redirect()->back()->withErrors(array('message' => '!!Sin registros!!'));
            }
            Excel::create('Siz_Reporte_MaterialesOP' . ' - ' . $hoy = date("d/m/Y") . '', function ($excel) use ($values, $info) {
                $excel->sheet('Hoja 1', function ($sheet) use ($values, $info) {
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, [
                        'Código: ' . $info->ItemCode, 'Descripción: ' . $info->ItemName
                    ]);
                    $sheet->row(3, [
                        'Fecha de Entrega', 'Estación', 'Código', 'Descripción', 'UM', 'Solicitada'
                    ]);
                    //Datos    
                    $fila = 4;
                    foreach ($values as $fil) {
                        //  $tvs= $tvs + $produccion->TVS;
                        //$cant = $cant + $produccion->Cantidad;
                        $sheet->row(
                            $fila,
                            [
                                date('d-m-Y', strtotime($fil->FechaEntrega)),
                                $fil->Estacion,
                                $fil->Codigo,
                                $fil->Descripcion,
                                $fil->InvntryUom,
                                number_format($fil->Cantidad, 2)
                            ]
                        );
                        $fila++;
                    }
                });
            })->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function backorder()
    {
        if (Auth::check()) {            
                $user = Auth::user();
                $actividades = $user->getTareas(); 
                $ultimo = count($actividades);
            return view('Mod01_Produccion.ReporteBackOrder', compact('user', 'actividades', 'ultimo'));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function DataShowbackorder()
    {
        // $bo =  DB::table('Vw_BackOrderExcel')
        // ->select('OP', 'Pedido', 'fechapedido', 'OC', 'd_proc', 'no_serie', 'cliente', 'codigo1', 'codigo3', 'Descripcion',
		// 'Cantidad', 'VSind', 'VS', 'destacion', 'U_GRUPO', 'Secue', 'SecOT', 'SEMANA2', 'fentrega',
		// 'fechaentregapedido', 'SEMANA3', 'u_fproduccion', 'prioridad', 'comments', 'u_especial', 'modelo');        
        $rowsBo = DB::table('SIZ_View_ReporteBO');
         
        return Datatables::of($rowsBo)
        ->addColumn('Funda', function ($rowbo) {
            if(is_null ($rowbo->prefunda)){
                if(is_null ($rowbo->U_Entrega_Piel)){
                    if(is_null ($rowbo->U_Status)){
                        return '00 Por Programar';                    
                    }else{
                        $valuefunda = '00 Por Programar';
                        switch ($rowbo->U_Status) {
                            case "01":
                                $valuefunda = '00 Detenido Ventas';
                                break;
                            case "02":
                                $valuefunda = '00 Falta Inf.';
                                break;
                            case "03":
                                $valuefunda = '00 Falta Mat.';
                                break;
                            case "04":
                                $valuefunda = '00 Revision Piel';
                                break;
                            case "05":
                                $valuefunda = '00 Por Ordenar Mat.';
                                break;
                            case "06":
                                $valuefunda = '00 Proceso';
                                break;                       
                        }
                        return $valuefunda;
                    }
                }else {
                    return 'Sin liberar';
                } //end_2do_if           
            }else {
                if (($rowbo->CodFunda == 1 || $rowbo->CodFunda == 2) && (is_null ($rowbo->U_Entrega_Piel))) {
                    if(is_null ($rowbo->U_Status)){
                        return '00 Por Programar';    
                    }else{
                        $valuefunda = '00 Por Programar';
                        switch ($rowbo->U_Status) {
                            case "01":
                                $valuefunda = '00 Detenido Ventas';
                                break;
                            case "02":
                                $valuefunda = '00 Falta Inf.';
                                break;
                            case "03":
                                $valuefunda = '00 Falta Mat.';
                                break;
                            case "04":
                                $valuefunda = '00 Revision Piel';
                                break;
                            case "05":
                                $valuefunda = '00 Por Ordenar Mat.';
                                break;
                            case "06":
                                $valuefunda = '00 Proceso';
                                break;                       
                        }
                        return $valuefunda;
                    }
                }else{
                    return $rowbo->prefunda;
                }
            }//end_1er_if
        }
        )
        ->make(true);
    }
    public function backOrderAjaxToSession(){
        //ajax nos envia los registros del datatable que el usuario filtro y los alamcenamos en la session
        //formato JSON
        Session::put('miarr',Input::get('arr'));   
    }
    public function ReporteBackOrderVentasPDF()
    {           
        $data = json_decode(stripslashes(Session::get('miarr')));      
        $pdf = \PDF::loadView('Mod01_Produccion.ReporteBackOrderPDF_Ventas', compact('data'));
        $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true]);             
        return $pdf->stream('Siz_Reporte_BackOrderV ' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }
    public function ReporteBackOrderPlaneaPDF()
    {   
        $data = json_decode(stripslashes(Session::get('miarr')));
        $pdf = \PDF::loadView('Mod01_Produccion.ReporteBackOrderPDF_Planea', compact('data'));
        $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true]);             
        return $pdf->stream('Siz_Reporte_BackOrderP ' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }

}
