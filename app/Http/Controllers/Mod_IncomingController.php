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
    
    // Muestra la vista principal de RECHAZOS
    public function index_rechazos()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        return view('Mod_IncomingController.index_rechazos', compact('actividades', 'ultimo'));
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
                if ($ins->CODIGO_ARTICULO == $materialSAP->CODIGO_ARTICULO) {
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
                $inspeccionesPrevias = \App\Modelos\Siz_Incoming::on('siz')
                    ->where('INC_docNum', $numeroEntrada)
                    ->where('INC_codMaterial', $materialSAP->CODIGO_ARTICULO)
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
                //try {
                    
                    $lote = DB::select('SELECT TOP (1) OIBT.BatchNum FROM OIBT WHERE OIBT.ItemCode = ? AND OIBT.BaseEntry = ?', 
                        [$materialSAP->CODIGO_ARTICULO, $materialSAP->BASE_ENTRY]);

                        //dd($lote, $materialSAP);
                    $materialSAP->LOTE = $lote ? $lote[0]->BatchNum : 'N/A';
                //} catch (\Exception $e) {
                //    $materialSAP->LOTE = 'N/A';
                //}
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
        
        $checklist = \App\Modelos\Siz_Checklist::on('siz')->where('CHK_activo', 'S')->orderBy('CHK_orden')->get();
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
        $inspeccion = \App\Modelos\Siz_Incoming::on('siz')->where('INC_id', $inc_id)->first();
        
        if (!$inspeccion) {
            return response()->json(['error' => 'Inspección no encontrada'], 404);
        }
        
        // Obtener checklist y respuestas
        $checklist = \App\Modelos\Siz_Checklist::on('siz')->where('CHK_activo', 'S')->orderBy('CHK_orden')->get();
        $respuestas = \App\Modelos\Siz_IncomDetalle::on('siz')->where('IND_incId', $inc_id)->get();
        
        // Preparar datos de la inspección para el frontend
        $inspeccionData = [
            'INC_id' => $inspeccion->INC_id,
            'CODIGO_ARTICULO' => $inspeccion->INC_codMaterial,
            'MATERIAL' => $inspeccion->INC_nomMaterial,
            'CAN_INSPECCIONADA' => $inspeccion->INC_cantAceptada,
            'CAN_RECHAZADA' => $inspeccion->INC_cantRechazada,
            'POR_REVISAR' => $inspeccion->INC_cantRecibida - $inspeccion->INC_cantAceptada - $inspeccion->INC_cantRechazada,
            'OBSERVACIONES_GENERALES' => $inspeccion->INC_notas,
            'INC_fechaInspeccion' => $inspeccion->INC_fechaInspeccion,
            'INC_nomInspector' => $inspeccion->INC_nomInspector
        ];
        
                    // Obtener imágenes agrupadas por CHK_id
            $imagenes = \App\Modelos\Siz_IncomImagen::on('siz')->where('IMG_incId', $inc_id)->where('IMG_borrado','N')->get();
            $imagenesPorChk = [];
            foreach ($imagenes as $img) {
                $chkId = $img->IMG_descripcion; // En este campo guardamos el CHK_id
                if (!isset($imagenesPorChk[$chkId])) { $imagenesPorChk[$chkId] = []; }
                $imagenesPorChk[$chkId][] = [
                    'ruta' => $img->IMG_ruta,
                    'id' => $img->IMG_id,
                    'archivo' => basename($img->IMG_ruta)
                ];
            }

            return response()->json([
            'inspeccion' => $inspeccionData,
            'checklist' => $checklist,
            'respuestas' => $respuestas,
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

            // Crear nueva inspección parcial (no actualizar existente)
            $incoming = new \App\Modelos\Siz_Incoming();
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
            $incoming->save();

            // Guardar checklist
            if ($checklist) {
                foreach ($checklist as $chk_id => $item) {
                    $detalle = new \App\Modelos\Siz_IncomDetalle();
                    $detalle->setConnection('siz');
                    $detalle->IND_incId = $incoming->INC_id;
                    $detalle->IND_chkId = $chk_id;
                    $detalle->IND_estado = isset($item['estado']) ? $item['estado'] : 'A';
                    $detalle->IND_observacion = isset($item['obs']) ? $item['obs'] : null;
                    $detalle->IND_borrado = 'N';
                    $detalle->IND_creadoEn = date("Y-m-d H:i:s");
                    $detalle->IND_actualizadoEn = date("Y-m-d H:i:s");
                    $detalle->save();
                }
            }

            // Guardar clases de piel si aplica
            if ($incoming->INC_esPiel == 'S' && $piel) {
                $pielClases = new \App\Modelos\Siz_PielClases();
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
            if ($imagenes) {
                $directorioBase = 'D:\\INCOMING\\' . $material['NOTA_ENTRADA'];
                if (!file_exists($directorioBase)) {
                    mkdir($directorioBase, 0777, true);
                }
                
                foreach ($imagenes as $chk_id => $archivos) {
                    if ($archivos) {
                        // Si es un array de archivos (múltiples imágenes)
                        if (is_array($archivos)) {
                            foreach ($archivos as $img) {
                                if ($img) {
                                    $extension = $img->getClientOriginalExtension();
                                    // Obtener nombre del checklist por CHK_id
                                    $chk = \App\Modelos\Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                                    $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                    $nombre = $incoming->INC_id . '_' . $chkNombre . '_' . uniqid() . '.' . $extension;
                                    $rutaCompleta = $directorioBase . '\\' . $nombre;
                                    
                                    // Guardar archivo
                                    $img->move($directorioBase, $nombre);
                                    
                                    $imagen = new \App\Modelos\Siz_IncomImagen();
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
                            $chk = \App\Modelos\Siz_Checklist::on('siz')->where('CHK_id', $chk_id)->first();
                            $chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                            $nombre = $incoming->INC_id . '_' . $chkNombre . '.' . $extension;
                            $rutaCompleta = $directorioBase . '\\' . $nombre;
                            
                            // Guardar archivo
                            $archivos->move($directorioBase, $nombre);
                            
                            $imagen = new \App\Modelos\Siz_IncomImagen();
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

            DB::connection('siz')->commit();
            
            // Recargar datos de materiales usando ambos procedimientos por separado
            $materialesSAP = DB::select('EXEC SIZ_Calidad_EntradaMaterial @NumeroEntrada = ?', [$material['NOTA_ENTRADA']]);
            $inspecciones = DB::connection('siz')->select('EXEC SIZ_Calidad_InspeccionMaterial @NumeroEntrada = ?', [$material['NOTA_ENTRADA']]);
            
            // Combinar datos
            $materialesActualizados = [];
            foreach ($materialesSAP as $materialSAP) {
                $inspeccion = null;
                foreach ($inspecciones as $ins) {
                    if ($ins->CODIGO_ARTICULO == $materialSAP->CODIGO_ARTICULO) {
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
                    $inspeccionesPrevias = \App\Modelos\Siz_Incoming::on('siz')
                        ->where('INC_docNum', $material['NOTA_ENTRADA'])
                        ->where('INC_codMaterial', $materialSAP->CODIGO_ARTICULO)
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
        $img = \App\Modelos\Siz_IncomImagen::on('siz')->where('IMG_id', $id)->where('IMG_borrado','N')->first();
        if (!$img || !file_exists($img->IMG_ruta)) {
            abort(404);
        }
        $path = $img->IMG_ruta;
        $filename = basename($path);
        // Detectar MIME de forma compatible
        $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
        if ($mime === 'application/octet-stream' && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected = finfo_file($finfo, $path);
            if ($detected) { $mime = $detected; }
            finfo_close($finfo);
        }
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ];
        return response()->make(file_get_contents($path), 200, $headers);
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