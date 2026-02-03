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
     * Muestra la vista principal del reporte REP-05 Historial por Proveedor
     */
    public function index_rep05()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);

            // Fechas por defecto: último año
            $fechaDesdeDefault = date('Y-m-d', strtotime('-1 year'));
            $fechaHastaDefault = date('Y-m-d');
            
            // Inicializar gráfica vacía (se actualizará vía AJAX)
            $graficaAceptadoRechazado = null;

            return view('Reportes_IncomingController.index_rep05', compact('user', 'actividades', 'ultimo', 'fechaDesdeDefault', 'fechaHastaDefault', 'graficaAceptadoRechazado'));
        } else {
            return redirect()->route('auth/login');
        }
    }

    /**
     * AJAX: Historial por proveedor (REP-05)
     * - Detalle: rechazos/entradas por proveedor (agrupado)
     * - Resumen: calificación por mes del proveedor (AVG CALF_U)
     */
    public function buscarHistorialProveedor(Request $request)
    {
        try {
            // Obtener fechas del request
            $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 year')));
            $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));
            $codProv = trim((string)$request->input('cod_prov', ''));

            if ($codProv === '') {
                return response()->json([
                    'success' => false,
                    'msg' => 'El código de proveedor es requerido'
                ]);
            }

            // Validar fechas
            if (empty($fechaDesde) || empty($fechaHasta)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Las fechas son requeridas'
                ]);
            }

            // Validar que fecha desde sea menor o igual a fecha hasta
            if (strtotime($fechaDesde) > strtotime($fechaHasta)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La fecha desde debe ser menor o igual a la fecha hasta'
                ]);
            }

            $fechaIS = date('Y-m-d', strtotime($fechaDesde));
            $fechaFS = date('Y-m-d', strtotime($fechaHasta));

            // Detalle (macro VMA_R143_D)
            $detalle = DB::connection('siz')->select("
                Select
                    ISNULL(SIR.IR_id, 0) AS RECHAZO,
                    SIC.INC_id AS INC_ID,
                    OCRD.CardName AS PROVEEDOR,
                    SIC.INC_docNum AS NE,
                    SIC.INC_codMaterial AS COD_MAT,
                    SIC.INC_nomMaterial AS MATERIAL,
                    SIC.INC_unidadMedida AS UDM,
                    SUM(SIC.INC_cantRecibida) AS RECIBIDO,
                    SUM(SIC.INC_cantRechazada) AS RECHAZADA
                From Siz_Incoming SIC
                Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                Inner Join OITM on OITM.ItemCode = SIC.INC_codMaterial
                Left Join Siz_IncomRechazos SIR on SIR.IR_INC_incomld = SIC.INC_id
                Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?
                    and SIC.INC_codProveedor = ?
                    and SIC.INC_borrado = 'N'
                Group By
                    SIC.INC_docNum,
                    OCRD.CardName,
                    SIC.INC_codMaterial,
                    SIC.INC_nomMaterial,
                    SIC.INC_unidadMedida,
                    SIR.IR_id,
                    SIC.INC_id
                Order By SIC.INC_docNum, SIC.INC_nomMaterial
            ", [$fechaIS, $fechaFS, $codProv]);

            $proveedorNombre = '';
            if (!empty($detalle)) {
                $proveedorNombre = (string)$detalle[0]->PROVEEDOR;
            } else {
                // Si no hay detalle, intentar traer nombre del proveedor
                $prov = DB::select("SELECT TOP 1 CardName FROM OCRD WHERE CardCode = ?", [$codProv]);
                if (!empty($prov)) {
                    $proveedorNombre = (string)$prov[0]->CardName;
                }
            }

            // Calcular totales de aceptado y rechazado
            $totales = DB::connection('siz')->select("
                Select 
                    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO,
                    SUM(ISNULL(SIC.INC_cantAceptada, 0)) AS TOTAL_ACEPTADO,
                    SUM(ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_RECHAZADO
                From Siz_Incoming SIC
                Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?
                    and SIC.INC_codProveedor = ?
                    and SIC.INC_borrado = 'N'
            ", [$fechaIS, $fechaFS, $codProv]);

            $totalRecibido = isset($totales[0]) && $totales[0]->TOTAL_RECIBIDO > 0 ? (float)$totales[0]->TOTAL_RECIBIDO : 0;
            $totalAceptado = isset($totales[0]) ? (float)$totales[0]->TOTAL_ACEPTADO : 0;
            $totalRechazado = isset($totales[0]) ? (float)$totales[0]->TOTAL_RECHAZADO : 0;
            
            // Calcular cantidad por revisar
            $totalPorRevisar = $totalRecibido - $totalAceptado - $totalRechazado;
            if ($totalPorRevisar < 0) {
                $totalPorRevisar = 0; // Evitar valores negativos por errores de datos
            }

            // Calcular porcentajes basados en el total recibido
            $porcAceptado = $totalRecibido > 0 ? ($totalAceptado / $totalRecibido) * 100 : 0;
            $porcRechazado = $totalRecibido > 0 ? ($totalRechazado / $totalRecibido) * 100 : 0;
            $porcPorRevisar = $totalRecibido > 0 ? ($totalPorRevisar / $totalRecibido) * 100 : 0;

            return response()->json([
                'success' => true,
                'fechaIS' => $fechaIS,
                'fechaFS' => $fechaFS,
                'codProv' => $codProv,
                'proveedorNombre' => $proveedorNombre,
                'detalle' => $detalle,
                'totalRecibido' => $totalRecibido,
                'totalAceptado' => $totalAceptado,
                'totalRechazado' => $totalRechazado,
                'totalPorRevisar' => $totalPorRevisar,
                'porcAceptado' => round($porcAceptado, 2),
                'porcRechazado' => round($porcRechazado, 2),
                'porcPorRevisar' => round($porcPorRevisar, 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener el historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PDF REP-05 Historial por Proveedor (horizontal)
     */
    public function generarPdfRep05(Request $request)
    {
        try {
            // Obtener fechas del request
            $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 year')));
            $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));
            $codProv = trim((string)$request->input('cod_prov', ''));

            if ($codProv === '') {
                abort(400, 'El código de proveedor es requerido');
            }

            // Validar fechas
            if (empty($fechaDesde) || empty($fechaHasta)) {
                abort(400, 'Las fechas son requeridas');
            }

            // Validar que fecha desde sea menor o igual a fecha hasta
            if (strtotime($fechaDesde) > strtotime($fechaHasta)) {
                abort(400, 'La fecha desde debe ser menor o igual a la fecha hasta');
            }

            $fechaIS = date('Y-m-d', strtotime($fechaDesde));
            $fechaFS = date('Y-m-d', strtotime($fechaHasta));

            $detalle = DB::connection('siz')->select("
                Select
                    ISNULL(SIR.IR_id, 0) AS RECHAZO,
                    SIC.INC_id AS INC_ID,
                    OCRD.CardName AS PROVEEDOR,
                    SIC.INC_docNum AS NE,
                    SIC.INC_codMaterial AS COD_MAT,
                    SIC.INC_nomMaterial AS MATERIAL,
                    SIC.INC_unidadMedida AS UDM,
                    SUM(SIC.INC_cantRecibida) AS RECIBIDO,
                    SUM(SIC.INC_cantRechazada) AS RECHAZADA
                From Siz_Incoming SIC
                Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode
                Inner Join OITM on OITM.ItemCode = SIC.INC_codMaterial
                Left Join Siz_IncomRechazos SIR on SIR.IR_INC_incomld = SIC.INC_id
                Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?
                    and SIC.INC_codProveedor = ?
                    and SIC.INC_borrado = 'N'
                Group By
                    SIC.INC_docNum,
                    OCRD.CardName,
                    SIC.INC_codMaterial,
                    SIC.INC_nomMaterial,
                    SIC.INC_unidadMedida,
                    SIR.IR_id,
                    SIC.INC_id
                Order By SIC.INC_docNum, SIC.INC_nomMaterial
            ", [$fechaIS, $fechaFS, $codProv]);

            $proveedorNombre = '';
            if (!empty($detalle)) {
                $proveedorNombre = (string)$detalle[0]->PROVEEDOR;
            } else {
                $prov = DB::select("SELECT TOP 1 CardName FROM OCRD WHERE CardCode = ?", [$codProv]);
                $proveedorNombre = !empty($prov) ? (string)$prov[0]->CardName : '';
            }

            // Calcular totales para el PDF
            $totales = DB::connection('siz')->select("
                Select 
                    SUM(SIC.INC_cantRecibida) AS TOTAL_RECIBIDO,
                    SUM(ISNULL(SIC.INC_cantAceptada, 0)) AS TOTAL_ACEPTADO,
                    SUM(ISNULL(SIC.INC_cantRechazada, 0)) AS TOTAL_RECHAZADO
                From Siz_Incoming SIC
                Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?
                    and SIC.INC_codProveedor = ?
                    and SIC.INC_borrado = 'N'
            ", [$fechaIS, $fechaFS, $codProv]);

            $totalRecibido = isset($totales[0]) && $totales[0]->TOTAL_RECIBIDO > 0 ? (float)$totales[0]->TOTAL_RECIBIDO : 0;
            $totalAceptado = isset($totales[0]) ? (float)$totales[0]->TOTAL_ACEPTADO : 0;
            $totalRechazado = isset($totales[0]) ? (float)$totales[0]->TOTAL_RECHAZADO : 0;
            
            // Calcular cantidad por revisar
            $totalPorRevisar = $totalRecibido - $totalAceptado - $totalRechazado;
            if ($totalPorRevisar < 0) {
                $totalPorRevisar = 0; // Evitar valores negativos por errores de datos
            }

            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Reportes_IncomingController.pdfheader_rep05',
                [
                    'titulo' => 'REP-05 HISTORIAL POR PROVEEDOR',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'fechaIS' => $fechaIS,
                    'fechaFS' => $fechaFS,
                    'codProv' => $codProv,
                    'proveedorNombre' => $proveedorNombre,
                ]
            )->render();

            $pdf = \SPDF::loadView('Reportes_IncomingController.pdf_rep05', compact(
                'fechaIS',
                'fechaFS',
                'codProv',
                'proveedorNombre',
                'detalle',
                'totalRecibido',
                'totalAceptado',
                'totalRechazado',
                'totalPorRevisar'
            ));

            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('orientation', 'Landscape');
            $pdf->setOption('margin-top', '40mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');

            return $pdf->inline('REP-05_Historial_Proveedor_' . $codProv . '_' . date("Y-m-d", strtotime($fechaIS)) . '_' . date("Y-m-d", strtotime($fechaFS)) . '.pdf');
        } catch (\Exception $e) {
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista principal del reporte REP-06 Historial por Material
     */
    public function index_rep06()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);

            // Fechas por defecto: último año
            $fechaDesdeDefault = date('Y-m-d', strtotime('-1 year'));
            $fechaHastaDefault = date('Y-m-d');

            return view('Reportes_IncomingController.index_rep06', compact('user', 'actividades', 'ultimo', 'fechaDesdeDefault', 'fechaHastaDefault'));
        } else {
            return redirect()->route('auth/login');
        }
    }

    /**
     * AJAX: Historial por material (REP-06)
     * Basado en macro VMA_R143_E
     */
    public function buscarHistorialMaterial(Request $request)
    {
        try {
            // Obtener fechas del request
            $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 year')));
            $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));
            $codMaterial = trim((string)$request->input('cod_material', ''));

            if ($codMaterial === '') {
                return response()->json([
                    'success' => false,
                    'msg' => 'El código de material es requerido'
                ]);
            }

            // Validar fechas
            if (empty($fechaDesde) || empty($fechaHasta)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Las fechas son requeridas'
                ]);
            }

            // Validar que fecha desde sea menor o igual a fecha hasta
            if (strtotime($fechaDesde) > strtotime($fechaHasta)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La fecha desde debe ser menor o igual a la fecha hasta'
                ]);
            }

            $fechaIS = date('Y-m-d', strtotime($fechaDesde));
            $fechaFS = date('Y-m-d', strtotime($fechaHasta));

            // Consulta basada en macro VMA_R143_E
            $datos = DB::connection('siz')->select("
                Select  
                    RCP.COD_MAT AS COD_MATE, 
                    RCP.UDM AS UDM, 
                    RCP.MATERIAL AS MATERIAL, 
                    RCP.COD_PROV AS COD_PROV, 
                    RCP.PROVEEDOR AS PROVEEDOR, 
                    SUM(RCP.ACEPTADO) AS ACEPTADO, 
                    AVG(RCP.CALF_U) AS CALIFA 
                From (
                    Select 
                        SCC.MES AS NUM_MES, 
                        SIC.INC_codMaterial AS COD_MAT, 
                        SIC.INC_nomMaterial AS MATERIAL, 
                        OITM.InvntryUom AS UDM,  
                        SIC.INC_codProveedor AS COD_PROV, 
                        OCRD.CardName AS PROVEEDOR, 
                        SUM(SIC.INC_cantAceptada) AS ACEPTADO, 
                        Case When SIC.INC_esPiel = 'N' then 
                            (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) 
                        else 
                            (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 + 
                            (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 + 
                            (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) + 
                            (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else 
                            ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4) 
                        end AS CALF_U 
                    From Siz_Incoming SIC 
                    Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode 
                    Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date) 
                    Inner Join OITM on OITM.ItemCode = SIC.INC_codMaterial 
                    Left Join OOND on OCRD.IndustryC = OOND.IndCode 
                    Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                    Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?  
                    and SIC.INC_borrado = 'N' 
                    and SIC.INC_codMaterial = ? 
                    Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc, SIC.INC_codMaterial, SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES, SIC.INC_nomMaterial, OITM.InvntryUom
                ) RCP 
                Group By RCP.COD_PROV,  RCP.PROVEEDOR, RCP.COD_MAT,  RCP.MATERIAL, RCP.UDM 
                Order By RCP.PROVEEDOR
            ", [$fechaIS, $fechaFS, $codMaterial]);

            $materialNombre = '';
            $udm = '';
            if (!empty($datos)) {
                $materialNombre = (string)$datos[0]->MATERIAL;
                $udm = (string)$datos[0]->UDM;
            } else {
                // Si no hay datos, intentar traer nombre del material
                $mat = DB::connection('siz')->select("SELECT TOP 1 ItemName, InvntryUom FROM OITM WHERE ItemCode = ?", [$codMaterial]);
                if (!empty($mat)) {
                    $materialNombre = (string)$mat[0]->ItemName;
                    $udm = (string)$mat[0]->InvntryUom;
                }
            }

            return response()->json([
                'success' => true,
                'fechaIS' => $fechaIS,
                'fechaFS' => $fechaFS,
                'codMaterial' => $codMaterial,
                'materialNombre' => $materialNombre,
                'udm' => $udm,
                'datos' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener el historial: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PDF REP-06 Historial por Material
     */
    public function generarPdfRep06(Request $request)
    {
        try {
            // Obtener fechas del request
            $fechaDesde = $request->input('fecha_desde', date('Y-m-d', strtotime('-1 year')));
            $fechaHasta = $request->input('fecha_hasta', date('Y-m-d'));
            $codMaterial = trim((string)$request->input('cod_material', ''));

            if ($codMaterial === '') {
                abort(400, 'El código de material es requerido');
            }

            // Validar fechas
            if (empty($fechaDesde) || empty($fechaHasta)) {
                abort(400, 'Las fechas son requeridas');
            }

            // Validar que fecha desde sea menor o igual a fecha hasta
            if (strtotime($fechaDesde) > strtotime($fechaHasta)) {
                abort(400, 'La fecha desde debe ser menor o igual a la fecha hasta');
            }

            $fechaIS = date('Y-m-d', strtotime($fechaDesde));
            $fechaFS = date('Y-m-d', strtotime($fechaHasta));

            // Consulta basada en macro VMA_R143_E
            $datos = DB::connection('siz')->select("
                Select  
                    RCP.COD_MAT AS COD_MATE, 
                    RCP.UDM AS UDM, 
                    RCP.MATERIAL AS MATERIAL, 
                    RCP.COD_PROV AS COD_PROV, 
                    RCP.PROVEEDOR AS PROVEEDOR, 
                    SUM(RCP.ACEPTADO) AS ACEPTADO, 
                    AVG(RCP.CALF_U) AS CALIFA 
                From (
                    Select 
                        SCC.MES AS NUM_MES, 
                        SIC.INC_codMaterial AS COD_MAT, 
                        SIC.INC_nomMaterial AS MATERIAL, 
                        OITM.InvntryUom AS UDM,  
                        SIC.INC_codProveedor AS COD_PROV, 
                        OCRD.CardName AS PROVEEDOR, 
                        SUM(SIC.INC_cantAceptada) AS ACEPTADO, 
                        Case When SIC.INC_esPiel = 'N' then 
                            (SUM(SIC.INC_cantAceptada) / SUM(SIC.INC_cantRecibida)) 
                        else 
                            (ISNULL((SUM(SPC.PLC_claseA)/SUM(SIC.INC_cantRecibida))/.3 + 
                            (SUM(SPC.PLC_claseB)/SUM(SIC.INC_cantRecibida))/.5 + 
                            (1-((SUM(SPC.PLC_claseC)/SUM(SIC.INC_cantRecibida))-.2)) + 
                            (Case When SUM(SPC.PLC_claseD) = 0 Then 1 else 
                            ((SUM(SPC.PLC_claseD)/SUM(SIC.INC_cantRecibida))*-1) end), 0)/4) 
                        end AS CALF_U 
                    From Siz_Incoming SIC 
                    Inner Join OCRD on SIC.INC_codProveedor = OCRD.CardCode 
                    Inner Join  Siz_Calendario_Cierre SCC on CAST(SIC.INC_fechaInspeccion as Date) between Cast(SCC.FEC_INI as date) and Cast(SCC.FEC_FIN as date) 
                    Inner Join OITM on OITM.ItemCode = SIC.INC_codMaterial 
                    Left Join OOND on OCRD.IndustryC = OOND.IndCode 
                    Left Join Siz_PielClases SPC on SIC.INC_id = SPC.PLC_incId 
                    Where Cast(SIC.INC_fechaInspeccion as date) between ? and ?  
                    and SIC.INC_borrado = 'N' 
                    and SIC.INC_codMaterial = ? 
                    Group By SIC.INC_codProveedor, OCRD.CardName, OOND.IndDesc, SIC.INC_codMaterial, SIC.INC_docNum, SIC.INC_fechaInspeccion, SIC.INC_esPiel, OOND.IndName, SCC.MES, SIC.INC_nomMaterial, OITM.InvntryUom
                ) RCP 
                Group By RCP.COD_PROV,  RCP.PROVEEDOR, RCP.COD_MAT,  RCP.MATERIAL, RCP.UDM 
                Order By RCP.PROVEEDOR
            ", [$fechaIS, $fechaFS, $codMaterial]);

            $materialNombre = '';
            $udm = '';
            if (!empty($datos)) {
                $materialNombre = (string)$datos[0]->MATERIAL;
                $udm = (string)$datos[0]->UDM;
            } else {
                $mat = DB::connection('siz')->select("SELECT TOP 1 ItemName, InvntryUom FROM OITM WHERE ItemCode = ?", [$codMaterial]);
                $materialNombre = !empty($mat) ? (string)$mat[0]->ItemName : '';
                $udm = !empty($mat) ? (string)$mat[0]->InvntryUom : '';
            }

            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Reportes_IncomingController.pdfheader_rep06',
                [
                    'titulo' => 'REP-06 HISTORIAL POR MATERIAL',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'fechaIS' => $fechaIS,
                    'fechaFS' => $fechaFS,
                    'codMaterial' => $codMaterial,
                    'materialNombre' => $materialNombre,
                    'udm' => $udm,
                ]
            )->render();

            $pdf = \SPDF::loadView('Reportes_IncomingController.pdf_rep06', compact(
                'fechaIS',
                'fechaFS',
                'codMaterial',
                'materialNombre',
                'udm',
                'datos'
            ));

            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('orientation', 'Landscape');
            $pdf->setOption('margin-top', '40mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');

            return $pdf->inline('REP-06_Historial_Material_' . $codMaterial . '_' . date("Y-m-d", strtotime($fechaIS)) . '_' . date("Y-m-d", strtotime($fechaFS)) . '.pdf');
        } catch (\Exception $e) {
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
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
            
            // Calcular promedio anual para cada familia
            foreach ($familias as $familia) {
                $meses = [
                    $familia->ENERO, $familia->FEBRERO, $familia->MARZO,
                    $familia->ABRIL, $familia->MAYO, $familia->JUNIO,
                    $familia->JULIO, $familia->AGOSTO, $familia->SEPTIEMBRE,
                    $familia->OCTUBRE, $familia->NOVIEMBRE, $familia->DICIEMBRE
                ];
                
                // Filtrar valores mayores a 0 para calcular promedio
                $mesesConDatos = array_filter($meses, function($valor) {
                    return $valor > 0;
                });
                
                $familia->PROMEDIO = count($mesesConDatos) > 0 
                    ? (array_sum($mesesConDatos) / count($mesesConDatos)) 
                    : 0;
            }
            
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
            
            return response()->json([
                'success' => true,
                'promedioAnual' => $promedioAnualObj,
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
            
            // Calcular promedio anual para cada familia
            foreach ($familias as $familia) {
                $meses = [
                    $familia->ENERO, $familia->FEBRERO, $familia->MARZO,
                    $familia->ABRIL, $familia->MAYO, $familia->JUNIO,
                    $familia->JULIO, $familia->AGOSTO, $familia->SEPTIEMBRE,
                    $familia->OCTUBRE, $familia->NOVIEMBRE, $familia->DICIEMBRE
                ];
                
                $mesesConDatos = array_filter($meses, function($valor) {
                    return $valor > 0;
                });
                
                $familia->PROMEDIO = count($mesesConDatos) > 0 
                    ? (array_sum($mesesConDatos) / count($mesesConDatos)) 
                    : 0;
            }
            
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
