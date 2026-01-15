<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Reportes_IncomingController extends Controller
{
    /**
     * Muestra la vista principal del reporte R-140
     */
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
