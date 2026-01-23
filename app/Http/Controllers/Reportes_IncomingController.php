<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class Reportes_IncomingController extends Controller
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
     * Muestra la vista principal del reporte R-143 Confiabilidad de Proveedores
     */
    public function index_rep04()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            
            // Obtener año actual por defecto
            $anoActual = date('Y');
            
            return view('Reportes_IncomingController.index_rep04', compact('user', 'actividades', 'ultimo', 'anoActual'));
        } else {
            return redirect()->route('auth/login');
        }
    }

    /**
     * AJAX: Buscar datos de confiabilidad de proveedores
     */
    public function buscarConfiabilidadProveedores(Request $request)
    {
        try {
            $nCiclo = $request->input('ano', date('Y'));
            
            // Obtener fechas del calendario de cierre
            $fechas = DB::connection('siz')->select("
                SELECT 
                    Cast(SCC.FEC_INI as date) as FEC_INI,
                    Cast(SCC.FEC_FIN as date) as FEC_FIN
                FROM Siz_Calendario_Cierre SCC 
                WHERE SCC.PERIODO = ? + '-01'
            ", [$nCiclo]);
            
            if (empty($fechas)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró calendario de cierre para el año ' . $nCiclo
                ]);
            }
            
            $fechaIS = $fechas[0]->FEC_INI;
            
            $fechasFin = DB::connection('siz')->select("
                SELECT Cast(SCC.FEC_FIN as date) as FEC_FIN
                FROM Siz_Calendario_Cierre SCC 
                WHERE SCC.PERIODO = ? + '-12'
            ", [$nCiclo]);
            
            $fechaFS = $fechasFin[0]->FEC_FIN ?? $fechaIS;
            
            // 1. Obtener promedio del año
            $promedioAnual = DB::connection('siz')->select("
                Select SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , OCRD.CardName AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (Case When (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) = 0 Then
                              0.00001 else (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) end)
                              else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
            ", [$fechaIS, $fechaFS]);
            
            // 2. Obtener datos por familias
            $familias = DB::connection('siz')->select("
                Select  RCP2.IDG 
                    , RCP2.GRUPO 
                    , SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , SIC.INC_nomProveedor AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, SIC.INC_nomProveedor, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
                Group By RCP2.IDG, RCP2.GRUPO
                Order By RCP2.IDG
            ", [$fechaIS, $fechaFS]);
            
            // 3. Obtener datos por proveedor
            $proveedores = DB::connection('siz')->select("
                Select SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , RCP2.IDG 
                    , RCP2.GRUPO 
                    , RCP2.COD_PRO
                    , RCP2.PROVEEDOR
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , OCRD.CardName AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (Case When (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) = 0 Then
                              0.00001 else (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) end)
                              else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
                Group By RCP2.IDG, RCP2.GRUPO, RCP2.COD_PRO, RCP2.PROVEEDOR
                Order By RCP2.IDG, RCP2.PROVEEDOR
            ", [$fechaIS, $fechaFS]);
            
            // Calcular promedio anual para cada proveedor
            foreach ($proveedores as $proveedor) {
                $meses = [
                    $proveedor->ENERO, $proveedor->FEBRERO, $proveedor->MARZO,
                    $proveedor->ABRIL, $proveedor->MAYO, $proveedor->JUNIO,
                    $proveedor->JULIO, $proveedor->AGOSTO, $proveedor->SEPTIEMBRE,
                    $proveedor->OCTUBRE, $proveedor->NOVIEMBRE, $proveedor->DICIEMBRE
                ];
                
                // Filtrar valores mayores a 0 para calcular promedio
                $mesesConDatos = array_filter($meses, function($valor) {
                    return $valor > 0;
                });
                
                $proveedor->PROMEDIO = count($mesesConDatos) > 0 
                    ? (array_sum($mesesConDatos) / count($mesesConDatos)) 
                    : 0;
            }
            
            return response()->json([
                'success' => true,
                'promedioAnual' => $promedioAnual[0] ?? null,
                'familias' => $familias,
                'proveedores' => $proveedores,
                'fechaIS' => $fechaIS,
                'fechaFS' => $fechaFS
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF del reporte R-143
     */
    public function generarPdfRep04(Request $request)
    {
        try {
            $nCiclo = $request->input('ano', date('Y'));
            
            // Guardar datos en sesión para el PDF
            Session::put('r143_ano', $nCiclo);
            
            // Obtener fechas del calendario de cierre
            $fechas = DB::connection('siz')->select("
                SELECT 
                    Cast(SCC.FEC_INI as date) as FEC_INI,
                    Cast(SCC.FEC_FIN as date) as FEC_FIN
                FROM Siz_Calendario_Cierre SCC 
                WHERE SCC.PERIODO = ? + '-01'
            ", [$nCiclo]);
            
            if (empty($fechas)) {
                abort(404, 'No se encontró calendario de cierre para el año ' . $nCiclo);
            }
            
            $fechaIS = $fechas[0]->FEC_INI;
            
            $fechasFin = DB::connection('siz')->select("
                SELECT Cast(SCC.FEC_FIN as date) as FEC_FIN
                FROM Siz_Calendario_Cierre SCC 
                WHERE SCC.PERIODO = ? + '-12'
            ", [$nCiclo]);
            
            $fechaFS = $fechasFin[0]->FEC_FIN ?? $fechaIS;
            
            // Ejecutar las mismas consultas que en buscarConfiabilidadProveedores
            $promedioAnual = DB::connection('siz')->select("
                Select SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , OCRD.CardName AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (Case When (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) = 0 Then
                              0.00001 else (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) end)
                              else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
            ", [$fechaIS, $fechaFS]);
            
            $familias = DB::connection('siz')->select("
                Select  RCP2.IDG 
                    , RCP2.GRUPO 
                    , SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , SIC.INC_nomProveedor AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, SIC.INC_nomProveedor, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
                Group By RCP2.IDG, RCP2.GRUPO
                Order By RCP2.IDG
            ", [$fechaIS, $fechaFS]);
            
            $proveedores = DB::connection('siz')->select("
                Select SUM(RCP2.ENTRADAS) AS ENTRADAS
                    , RCP2.IDG 
                    , RCP2.GRUPO 
                    , RCP2.COD_PRO
                    , RCP2.PROVEEDOR
                    , ISNULL(AVG(RCP2.ENE), 0) AS ENERO
                    , ISNULL(AVG(RCP2.FEB), 0) AS FEBRERO
                    , ISNULL(AVG(RCP2.MAR), 0) AS MARZO
                    , ISNULL(AVG(RCP2.ABR), 0) AS ABRIL
                    , ISNULL(AVG(RCP2.MAY), 0) AS MAYO
                    , ISNULL(AVG(RCP2.JUN), 0) AS JUNIO
                    , ISNULL(AVG(RCP2.JUL), 0) AS JULIO
                    , ISNULL(AVG(RCP2.AGO), 0) AS AGOSTO
                    , ISNULL(AVG(RCP2.SEP), 0) AS SEPTIEMBRE
                    , ISNULL(AVG(RCP2.OCT), 0) AS OCTUBRE
                    , ISNULL(AVG(RCP2.NOV), 0) AS NOVIEMBRE
                    , ISNULL(AVG(RCP2.DIC), 0) AS DICIEMBRE
                From (
                    Select RCP.IDG AS IDG 
                        , RCP.GRUPO AS GRUPO 
                        , RCP.COD_PROV AS COD_PRO		
                        , RCP.PROVEEDOR AS PROVEEDOR
                        , SUM(RCP.ENTRADA) AS ENTRADAS
                        , Case When RCP.NUM_MES = '01' then AVG(RCP.CALF_U) else null end AS ENE
                        , Case When RCP.NUM_MES = '02' then AVG(RCP.CALF_U) else null end AS FEB
                        , Case When RCP.NUM_MES = '03' then AVG(RCP.CALF_U) else null end AS MAR
                        , Case When RCP.NUM_MES = '04' then AVG(RCP.CALF_U) else null end AS ABR
                        , Case When RCP.NUM_MES = '05' then AVG(RCP.CALF_U) else null end AS MAY
                        , Case When RCP.NUM_MES = '06' then AVG(RCP.CALF_U) else null end AS JUN
                        , Case When RCP.NUM_MES = '07' then AVG(RCP.CALF_U) else null end AS JUL
                        , Case When RCP.NUM_MES = '08' then AVG(RCP.CALF_U) else null end AS AGO
                        , Case When RCP.NUM_MES = '09' then AVG(RCP.CALF_U) else null end AS SEP
                        , Case When RCP.NUM_MES = '10' then AVG(RCP.CALF_U) else null end AS OCT
                        , Case When RCP.NUM_MES = '11' then AVG(RCP.CALF_U) else null end AS NOV
                        , Case When RCP.NUM_MES = '12' then AVG(RCP.CALF_U) else null end AS DIC
                    From (
                        Select SCC.MES AS NUM_MES
                            , ISNULL(OOND.IndDesc, '7') AS IDG
                            , ISNULL(OOND.IndName, 'SIN GRUPO') AS GRUPO
                            , SIC.INC_codProveedor AS COD_PROV
                            , OCRD.CardName AS PROVEEDOR
                            , Case When SIC.INC_esPiel = 'N' then
                              (Case When (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) = 0 Then
                              0.00001 else (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) end)
                              else
                              (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 +
                              (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 +
                              (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) +
                              (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else
                              ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4
                              ) end AS CALF_U
                            , 1 AS ENTRADA
                        From Siz_Incoming SIC
                        Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                        Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date)
                        Left Join OOND on OCRD.IndustryC = OOND.IndCode
                        Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                        Where Cast(SIC.INC_fechaInspeccion as date) between ? and ? and SIC.INC_borrado = 'N'
                        Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc,
                        SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES
                    ) RCP
                    Group By RCP.IDG, RCP.GRUPO, RCP.COD_PROV, RCP.PROVEEDOR, RCP.NUM_MES
                ) RCP2
                Group By RCP2.IDG, RCP2.GRUPO, RCP2.COD_PRO, RCP2.PROVEEDOR
                Order By RCP2.IDG, RCP2.PROVEEDOR
            ", [$fechaIS, $fechaFS]);
            
            // Calcular promedio anual para cada proveedor
            foreach ($proveedores as $proveedor) {
                $meses = [
                    $proveedor->ENERO, $proveedor->FEBRERO, $proveedor->MARZO,
                    $proveedor->ABRIL, $proveedor->MAYO, $proveedor->JUNIO,
                    $proveedor->JULIO, $proveedor->AGOSTO, $proveedor->SEPTIEMBRE,
                    $proveedor->OCTUBRE, $proveedor->NOVIEMBRE, $proveedor->DICIEMBRE
                ];
                
                $mesesConDatos = array_filter($meses, function($valor) {
                    return $valor > 0;
                });
                
                $proveedor->PROMEDIO = count($mesesConDatos) > 0 
                    ? (array_sum($mesesConDatos) / count($mesesConDatos)) 
                    : 0;
            }
            
            // Calcular promedio anual general
            $promedioAnualObj = $promedioAnual[0] ?? null;
            if ($promedioAnualObj) {
                $mesesPromedio = [
                    $promedioAnualObj->ENERO, $promedioAnualObj->FEBRERO, $promedioAnualObj->MARZO,
                    $promedioAnualObj->ABRIL, $promedioAnualObj->MAYO, $promedioAnualObj->JUNIO,
                    $promedioAnualObj->JULIO, $promedioAnualObj->AGOSTO, $promedioAnualObj->SEPTIEMBRE,
                    $promedioAnualObj->OCTUBRE, $promedioAnualObj->NOVIEMBRE, $promedioAnualObj->DICIEMBRE
                ];
                
                $mesesConDatosPromedio = array_filter($mesesPromedio, function($valor) {
                    return $valor > 0;
                });
                
                $promedioAnualObj->PROMEDIO = count($mesesConDatosPromedio) > 0 
                    ? (array_sum($mesesConDatosPromedio) / count($mesesConDatosPromedio)) 
                    : 0;
            }
            
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Reportes_IncomingController.pdfheader',
                [
                    'titulo' => 'R-143 Confiabilidad de Proveedores',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'ano' => $nCiclo,
                    'fechaIS' => $fechaIS,
                    'fechaFS' => $fechaFS
                ]
            )->render();
            
            $pdf = \SPDF::loadView('Reportes_IncomingController.pdf_rep04', compact(
                'promedioAnualObj', 
                'familias', 
                'proveedores', 
                'nCiclo',
                'fechaIS',
                'fechaFS'
            ));
            
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('orientation', 'Landscape');
            $pdf->setOption('margin-top', '40mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');
            
            return $pdf->inline('R-143_Confiabilidad_Proveedores_' . $nCiclo . '_' . date("Y-m-d") . '.pdf');
            
        } catch (\Exception $e) {
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        
        return view('Reportes_IncomingController.index', compact('actividades', 'ultimo'));
    }
    
    /**
     * AJAX: Buscar inspecciones de materiales con filtros de fecha
     */
    public function buscarInspecciones(Request $request)
    {
        try {
            $fechaDesde = $request->input('fecha_desde', '');
            $fechaHasta = $request->input('fecha_hasta', '');
           
           
            // Ejecutar la consulta SQL del archivo R140
            $inspecciones = DB::select("
                Select SIC.INC_docNum AS ID
                    , CAST(SIC.INC_fechaInspeccion as Date) AS FE_REV
                    , OCRD.CardName AS PROVEEDOR
                    , SIC.INC_codMaterial AS CODIGO
                    , SIC.INC_nomMaterial AS MATERIAL
                    , SIC.INC_unidadMedida AS UDM
                    , SIC.INC_cantRecibida AS RECIBIDO
                    , (SIC.INC_cantAceptada + SIC.INC_cantRechazada) AS REVISADA
                    , SIC.INC_cantAceptada AS ACEPTADA
                    , SIC.INC_cantRechazada AS RECHAZADA
                    , CASE 
                        WHEN SIC.INC_cantRecibida > 0 THEN 
                            CAST((SIC.INC_cantAceptada * 100.0 / SIC.INC_cantRecibida) AS DECIMAL(10,2))
                        ELSE NULL 
                      END AS PORC
                    , SIC.INC_nomInspector AS INSPECTOR
                    , SIC.INC_numFactura AS FACTURA
                    , SIC.INC_notas AS MOT_RECHAZO
                    , T1.Descr AS GRUPPLAN
                From Siz_Incoming SIC
                INNER JOIN OITM on OITM.ItemCode = SIC.INC_codMaterial
                Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                LEFT JOIN UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
                WHERE Cast(SIC.INC_fechaInspeccion as date) between ? and ? 
                    AND SIC.INC_borrado = 'N'
                Order By SIC.INC_fechaRecepcion, SIC.INC_nomMaterial
            ", [$fechaDesde, $fechaHasta]);
            
            return response()->json($inspecciones);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las inspecciones: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generar PDF del reporte R-140 en orientación horizontal
     */
    public function generarPdf(Request $request)
    {
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        
        try {
            $fechaDesde = $request->input('fecha_desde', '');
            $fechaHasta = $request->input('fecha_hasta', '');
            
            // Si no se envían fechas, usar los últimos 3 meses
            if (empty($fechaDesde)) {
                $fechaDesde = date('Y-m-d', strtotime('-3 months'));
            }
            
            if (empty($fechaHasta)) {
                $fechaHasta = date('Y-m-d');
            }
            
            // Ejecutar la consulta SQL
            $inspecciones = DB::select("
                Select SIC.INC_docNum AS ID
                    , CAST(SIC.INC_fechaInspeccion as Date) AS FE_REV
                    , OCRD.CardName AS PROVEEDOR
                    , SIC.INC_codMaterial AS CODIGO
                    , SIC.INC_nomMaterial AS MATERIAL
                    , SIC.INC_unidadMedida AS UDM
                    , SIC.INC_cantRecibida AS RECIBIDO
                    , (SIC.INC_cantAceptada + SIC.INC_cantRechazada) AS REVISADA
                    , SIC.INC_cantAceptada AS ACEPTADA
                    , SIC.INC_cantRechazada AS RECHAZADA
                    , CASE 
                        WHEN SIC.INC_cantRecibida > 0 THEN 
                            CAST((SIC.INC_cantAceptada * 100.0 / SIC.INC_cantRecibida) AS DECIMAL(10,2))
                        ELSE NULL 
                      END AS PORC
                    , SIC.INC_nomInspector AS INSPECTOR
                    , SIC.INC_numFactura AS FACTURA
                    , SIC.INC_notas AS MOT_RECHAZO
                    , T1.Descr AS GRUPPLAN
                From Siz_Incoming SIC
                INNER JOIN OITM on OITM.ItemCode = SIC.INC_codMaterial
                Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                LEFT JOIN UFD1 T1 on OITM.U_GrupoPlanea=T1.FldValue and T1.TableID='OITM' and T1.FieldID=9 
                WHERE Cast(SIC.INC_fechaInspeccion as date) between ? and ? 
                    AND SIC.INC_borrado = 'N'
                Order By SIC.INC_fechaRecepcion, SIC.INC_nomMaterial
            ", [$fechaDesde, $fechaHasta]);
            
            if (empty($inspecciones)) {
                abort(404, 'No se encontraron inspecciones para el rango de fechas seleccionado');
            }
            
            // Obtener información de la empresa
            $empresa = DB::table('OADM')
                ->select('CompnyName as RazonSocial')
                ->first();
            
            // Crear header HTML
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Mod_RechazosController.pdfheader',
                [
                    'titulo' => 'R-140 INCOMING - Inspección de Materiales',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'item' => 'Período: ' . date('d/m/Y', strtotime($fechaDesde)) . ' - ' . date('d/m/Y', strtotime($fechaHasta))
                ]
            )->render();
            
            $data = [
                'inspecciones' => $inspecciones,
                'empresa' => $empresa,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'fechaImpresion' => date('d/m/Y H:i:s')
            ];
            
            $pdf = \SPDF::loadView('Reportes_IncomingController.pdf', $data);
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('margin-top', '33mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');
            $pdf->setOption('orientation', 'Landscape'); // Orientación horizontal
            
            return $pdf->inline('R140_Inspeccion_Materiales_' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
}
