<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Siz_InspeccionProceso;
use App\Modelos\Siz_InspeccionProcesoDetalle;
use App\Modelos\Siz_InspeccionProcesoImagen;
use App\Modelos\Siz_Checklist;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Mod_InspeccionProcesoController extends Controller
{
    /**
     * Muestra la vista principal de inspección en proceso
     */
    public function index_inspeccion_en_proceso()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        
        return view('Mod_InspeccionProcesoController.index_inspeccion_en_proceso', compact('actividades', 'ultimo'));
    }
    
    /**
     * AJAX: Buscar OP y cargar automáticamente el checklist según el área actual
     */
    public function buscarInspeccionesEnProceso(Request $request)
    {
        //try {
            $op = $request->input('op');
            
            if (!$op) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Debe proporcionar el número de OP'
                ], 400);
            }
            
            // 1. Verificar que la OP existe y obtener información general
            $ordenProduccion = DB::table('OWOR')
                ->select(
                    'OWOR.DocNum as OP',
                    'OWOR.DocEntry',
                    'OWOR.ItemCode',
                    'OITM.ItemName',
                    'OWOR.PlannedQty as CantidadPlaneada',
                    'OWOR.Status',
                    'OWOR.PostDate as FechaCreacion',
                    'OWOR.DueDate as FechaEntrega',
                    'OWOR.U_Ruta as Ruta',
                    'OWOR.OriginNum as Pedido'
                )
                ->leftJoin('OITM', 'OWOR.ItemCode', '=', 'OITM.ItemCode')
                ->where('OWOR.DocNum', $op)
                ->first();
            
            if (!$ordenProduccion) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró la Orden de Producción'
                ], 404);
            }
            $cp_of = DB::table('@CP_OF')
                ->where('U_DocEntry', $ordenProduccion->DocEntry)
                ->first();
            // 2. Obtener la estación actual de la OP
            $estacionActual = $cp_of->U_CT;
            $cantidadEnCentro = $cp_of->U_Recibido;
            if (!$estacionActual) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La OP no tiene una estación actual asignada'
                ], 404);
            }
            
            // 3. Verificar que la estación actual sea de calidad
            $estacionActualInfo = DB::table('@PL_RUTAS')
                ->where('Code', $estacionActual)
                ->first();
            
            if (!$estacionActualInfo) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró información de la estación actual'
                ], 404);
            }
            
            if ($estacionActualInfo->U_Calidad !== 'S') {
                return response()->json([
                    'success' => false,
                    'msg' => 'La OP no está actualmente en un centro de inspección (Calidad). Actualmente se encuentra en: ' . $estacionActualInfo->Name
                ], 400);
            }
            
            // 4. Validar permisos del inspector
            $inspector_centros = Auth::user()->U_CP_CT;
            $centros_permitidos = $inspector_centros ? explode(",", str_replace(' ', '', $inspector_centros)) : [];
            //dd($centros_permitidos, $inspector_centros, $estacionActual, Auth::user());
            
            if (!in_array($estacionActual, $centros_permitidos)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No tiene permisos para inspeccionar en este centro de inspección: ' . $estacionActualInfo->Name
                ], 403);
            }
            
            // 5. Obtener historial completo de la OP
            $historial = DB::select("
                SELECT 
                    [@CP_LOGOF].U_CT,
                    [@PL_RUTAS].Name AS NombreEstacion,
                    [@PL_RUTAS].U_Calidad AS EsCalidad,
                    OHEM.firstName + ' ' + OHEM.lastName AS Empleado,
                    MIN([@CP_LOGOF].U_FechaHora) AS PrimeraFecha,
                    MAX([@CP_LOGOF].U_FechaHora) AS UltimaFecha,
                    SUM([@CP_LOGOF].U_Cantidad) AS CantidadElaborada
                FROM [@CP_LOGOF]
                INNER JOIN [@PL_RUTAS] ON [@CP_LOGOF].U_CT = [@PL_RUTAS].Code
                LEFT JOIN OHEM ON [@CP_LOGOF].U_idEmpleado = OHEM.empID
                WHERE [@CP_LOGOF].U_DocEntry = ?
                GROUP BY [@CP_LOGOF].U_CT, [@PL_RUTAS].Name, [@PL_RUTAS].U_Calidad, OHEM.firstName, OHEM.lastName
                ORDER BY MIN([@CP_LOGOF].U_FechaHora)
            ", [$op]);
            
            // 6. Validar que haya cantidad en el centro de inspección actual
            //$cantidadEnCentro = $estacionActualInfo->U_Recibido;
           
            if ($cantidadEnCentro <= 0) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No hay cantidad disponible en el centro de inspección actual. La OP debe pasar por este centro antes de poder inspeccionarla.'
                ], 400);
            }
            
            // 7. Obtener checklist específico para este centro de inspección
            $checklist = Siz_Checklist::on('siz')
                ->where('CHK_activo', 'S')
                ->where('CHK_area', $estacionActual)
                ->orderBy('CHK_orden')
                ->get();
            
            // 8. Obtener inspecciones previas para esta OP y centro
            $inspeccionesPrevias = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_centroInspeccion', $estacionActual)
                ->where('IPR_borrado', 'N')
                ->orderBy('IPR_id', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'op' => $ordenProduccion,
                'centro_inspeccion' => [
                    'id' => $estacionActual,
                    'nombre' => $estacionActualInfo->Name,
                    'cantidad_disponible' => $cantidadEnCentro
                ],
                'checklist' => $checklist,
                'historial' => $historial,
                'inspecciones_previas' => $inspeccionesPrevias,
                'id_inspeccion' => 0
            ]);
            
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'msg' => 'Error al buscar la OP: ' . $e->getMessage()
        //     ], 500);
        // }
    }
    
    /**
     * AJAX: Guardar inspección en proceso
     */
    public function guardarInspeccionProceso(Request $request)
    {
        DB::connection('siz')->beginTransaction();
        try {
            $op = $request->input('op');
            $docEntry = $request->input('doc_entry');
            $codArticulo = $request->input('cod_articulo');
            $nomArticulo = $request->input('nom_articulo');
            $cantPlaneada = $request->input('cant_planeada');
            $cantInspeccionada = $request->input('cant_inspeccionada');
            $cantRechazada = $request->input('cant_rechazada');
            $centroInspeccion = $request->input('centro_inspeccion');
            $nombreCentro = $request->input('nombre_centro');
            $observaciones = $request->input('observaciones');
            $fechaInspeccion = $request->input('fecha_inspeccion');
            
            // Crear nueva inspección
            $inspeccion = new Siz_InspeccionProceso();
            $inspeccion->setConnection('siz');
            $inspeccion->IPR_op = $op;
            $inspeccion->IPR_docEntry = $docEntry;
            $inspeccion->IPR_codArticulo = $codArticulo;
            $inspeccion->IPR_nomArticulo = $nomArticulo;
            $inspeccion->IPR_cantPlaneada = $cantPlaneada;
            $inspeccion->IPR_cantInspeccionada = $cantInspeccionada;
            $inspeccion->IPR_cantRechazada = $cantRechazada;
            $inspeccion->IPR_centroInspeccion = $centroInspeccion;
            $inspeccion->IPR_nombreCentro = $nombreCentro;
            $inspeccion->IPR_observaciones = $observaciones;
            
            // Obtener fecha de inspección del formulario o usar fecha actual
            if ($fechaInspeccion) {
                $inspeccion->IPR_fechaInspeccion = $fechaInspeccion . ' ' . date('H:i:s');
            } else {
                $inspeccion->IPR_fechaInspeccion = date("Y-m-d H:i:s");
            }
            
            $inspeccion->IPR_codInspector = Auth::user()->U_EmpGiro;
            $inspeccion->IPR_nomInspector = Auth::user()->getName();
            $inspeccion->IPR_borrado = 'N';
            $inspeccion->IPR_creadoEn = date("Y-m-d H:i:s");
            $inspeccion->IPR_actualizadoEn = date("Y-m-d H:i:s");
            $inspeccion->save();
            
            // Guardar respuestas del checklist (solo las que no son "No Aplica")
            if ($request->has('checklist')) {
                foreach ($request->input('checklist') as $chkId => $respuesta) {
                    if ($respuesta && $respuesta !== 'No Aplica') {
                        $detalle = new Siz_InspeccionProcesoDetalle();
                        $detalle->setConnection('siz');
                        $detalle->IPD_iprId = $inspeccion->IPR_id;
                        $detalle->IPD_chkId = $chkId;
                        
                        // Convertir respuesta completa a carácter
                        if ($respuesta === 'Cumple') {
                            $detalle->IPD_estado = 'C';
                        } elseif ($respuesta === 'No Cumple') {
                            $detalle->IPD_estado = 'N';
                        } else {
                            $detalle->IPD_estado = 'A';
                        }
                        
                        // Agregar observación si existe
                        if ($request->has('checklist_observacion.' . $chkId)) {
                            $detalle->IPD_observacion = $request->input('checklist_observacion.' . $chkId);
                        }
                        
                        // Si es "No Cumple", guardar la cantidad
                        if ($respuesta === 'No Cumple' && $request->has('checklist_cantidad.' . $chkId)) {
                            $detalle->IPD_cantidad = $request->input('checklist_cantidad.' . $chkId);
                        }
                        
                        $detalle->IPD_borrado = 'N';
                        $detalle->IPD_creadoEn = date("Y-m-d H:i:s");
                        $detalle->IPD_actualizadoEn = date("Y-m-d H:i:s");
                        $detalle->save();
                    }
                }
            }
            
            // Guardar imágenes de evidencia
            $archivosEncontrados = false;
            $archivosEvidencia = [];
            
            // Buscar archivos de evidencia en el request
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'checklist_evidencias') !== false) {
                    if ($request->hasFile($key)) {
                        $archivosEncontrados = true;
                        $archivosEvidencia[$key] = $request->file($key);
                    }
                }
            }
            
            // Verificar si hay archivos en el formato específico que está llegando
            if (isset($request->file()['checklist_evidencias']) && is_array($request->file()['checklist_evidencias'])) {
                $archivosEncontrados = true;
                $archivosEvidencia['checklist_evidencias'] = $request->file()['checklist_evidencias'];
            }
            
            if ($archivosEncontrados) {
                $so = env('DB_DATABASE');
                if($so == 'SBO_Pruebas') {
                    $directorioBase = 'D:\\QAS\\INSPECCION_PROCESO\\OP_' . $op;
                } else {
                    $directorioBase = 'D:\\INSPECCION_PROCESO\\OP_' . $op;
                }
                
                if (!file_exists($directorioBase)) {
                    mkdir($directorioBase, 0777, true);
                }
                
                // Procesar archivos encontrados
                foreach ($archivosEvidencia as $key => $archivos) {
                    if ($archivos) {
                        if ($key === 'checklist_evidencias' && is_array($archivos)) {
                            foreach ($archivos as $chk_id => $archivosChk) {
                                if ($archivosChk && is_array($archivosChk)) {
                                    foreach ($archivosChk as $img) {
                                        if ($img) {
                                            $extension = $img->getClientOriginalExtension();
                                            $chk = Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                                            $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                            $nombre = $inspeccion->IPR_id . '_' . $chkNombre . '_' . uniqid() . '.' . $extension;
                                            $rutaCompleta = $directorioBase . '\\' . $nombre;
                                            
                                            $img->move($directorioBase, $nombre);
                                            
                                            $imagen = new Siz_InspeccionProcesoImagen();
                                            $imagen->setConnection('siz');
                                            $imagen->IPI_iprId = $inspeccion->IPR_id;
                                            $imagen->IPI_ruta = $rutaCompleta;
                                            $imagen->IPI_descripcion = $chk_id;
                                            $imagen->IPI_cargadoPor = auth()->check() ? auth()->user()->name : 'sistema';
                                            $imagen->IPI_cargadoEn = date("Y-m-d H:i:s");
                                            $imagen->IPI_borrado = 'N';
                                            $imagen->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            DB::connection('siz')->commit();
            
            return response()->json([
                'success' => true,
                'msg' => 'Inspección guardada correctamente',
                'id_inspeccion' => $inspeccion->IPR_id
            ]);
            
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * AJAX: Ver inspección previa (solo lectura)
     */
    public function verInspeccionProceso(Request $request)
    {
        try {
            $iprId = $request->input('ipr_id');
            
            // Obtener datos de la inspección
            $inspeccion = Siz_InspeccionProceso::on('siz')
                ->where('IPR_id', $iprId)
                ->where('IPR_borrado', 'N')
                ->first();
            
            if (!$inspeccion) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Inspección no encontrada'
                ], 404);
            }
            
            // Obtener respuestas del checklist
            $respuestas = Siz_InspeccionProcesoDetalle::on('siz')
                ->where('IPD_iprId', $iprId)
                ->where('IPD_borrado', 'N')
                ->get()
                ->keyBy('IPD_chkId');
            
            // Obtener checklist
            $checklist = Siz_Checklist::on('siz')
                ->where('CHK_activo', 'S')
                ->where('CHK_area', $inspeccion->IPR_centroInspeccion)
                ->orderBy('CHK_orden')
                ->get();
            
            // Preparar respuestas para el frontend
            $respuestasFormateadas = [];
            foreach ($checklist as $item) {
                if (isset($respuestas[$item->CHK_id])) {
                    $estado = $respuestas[$item->CHK_id]->IPD_estado;
                    if ($estado === 'C') {
                        $respuestasFormateadas[$item->CHK_id] = 'Cumple';
                    } elseif ($estado === 'N') {
                        $respuestasFormateadas[$item->CHK_id] = 'No Cumple';
                    } else {
                        $respuestasFormateadas[$item->CHK_id] = 'No Aplica';
                    }
                    
                    if ($respuestas[$item->CHK_id]->IPD_observacion) {
                        $respuestasFormateadas[$item->CHK_id . '_observacion'] = $respuestas[$item->CHK_id]->IPD_observacion;
                    }
                    
                    if ($respuestas[$item->CHK_id]->IPD_cantidad) {
                        $respuestasFormateadas[$item->CHK_id . '_cantidad'] = $respuestas[$item->CHK_id]->IPD_cantidad;
                    }
                } else {
                    $respuestasFormateadas[$item->CHK_id] = 'No Aplica';
                }
            }
            
            // Obtener imágenes agrupadas por CHK_id
            $imagenes = Siz_InspeccionProcesoImagen::on('siz')
                ->where('IPI_iprId', $iprId)
                ->where('IPI_borrado', 'N')
                ->get();
            
            $imagenesPorChk = [];
            foreach ($imagenes as $img) {
                $chkId = $img->IPI_descripcion;
                if (!isset($imagenesPorChk[$chkId])) {
                    $imagenesPorChk[$chkId] = [];
                }
                $imagenesPorChk[$chkId][] = [
                    'ruta' => $img->IPI_ruta,
                    'id' => $img->IPI_id,
                    'archivo' => basename($img->IPI_ruta)
                ];
            }
            
            return response()->json([
                'success' => true,
                'inspeccion' => $inspeccion,
                'checklist' => $checklist,
                'respuestas' => $respuestasFormateadas,
                'imagenes' => $imagenesPorChk
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al cargar la inspección: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Descargar/ver imagen de evidencia
     */
    public function verImagenProceso($id)
    {
        try {
            $img = Siz_InspeccionProcesoImagen::on('siz')
                ->where('IPI_id', $id)
                ->where('IPI_borrado', 'N')
                ->first();
            
            if (!$img) {
                abort(404, 'Imagen no encontrada');
            }
            
            if (!file_exists($img->IPI_ruta)) {
                abort(404, 'Archivo de imagen no encontrado');
            }
            
            $path = $img->IPI_ruta;
            $filename = basename($path);
            
            $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
            if ($mime === 'application/octet-stream' && function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detected = finfo_file($finfo, $path);
                if ($detected) {
                    $mime = $detected;
                }
                finfo_close($finfo);
            }
            
            $headers = [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Cache-Control' => 'public, max-age=3600'
            ];
            
            return response()->make(file_get_contents($path), 200, $headers);
            
        } catch (\Exception $e) {
            abort(404, 'Error al cargar la imagen: ' . $e->getMessage());
        }
    }
}

