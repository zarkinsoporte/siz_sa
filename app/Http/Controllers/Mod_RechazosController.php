<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Siz_Incoming;
use App\Modelos\Siz_IncomRechazo;
use App\Modelos\Siz_Checklist;
use App\Modelos\Siz_PielClases;
use App\Modelos\Siz_IncomImagen;
use App\Modelos\Siz_IncomDetalle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Mail;

class Mod_RechazosController extends Controller
{
    
    // Muestra la vista principal de RECHAZOS
    public function index_rechazos()
    {
        $actividades = session("userActividades");
        $ultimo = count($actividades);
        return view("Mod_RechazosController.index_rechazos", compact("actividades", "ultimo"));
    }
    
    // AJAX: Obtener lista de rechazos desde el procedimiento almacenado
public function buscarRechazos(Request $request)
{
    try {
        $estado = $request->input('estado', ''); // '' = todos, '0' = pendientes, '1' = generados
        $fechaDesde = $request->input('fecha_desde', '');
        $fechaHasta = $request->input('fecha_hasta', '');
        
        // Si no se envían fechas, usar los últimos 3 meses
        if (empty($fechaDesde)) {
            $fechaDesde = date('Y-m-d', strtotime('-3 months'));
        }
        
        if (empty($fechaHasta)) {
            $fechaHasta = date('Y-m-d');
        }
        
        // Convertir estado vacío a NULL para el procedimiento
        $estadoParam = ($estado === '') ? null : (int)$estado;
        
        // Ejecutar el procedimiento almacenado con parámetros
        $rechazos = DB::select("EXEC SIZ_Calidad_RechazosMaterial ?, ?, ?", [
            $estadoParam,
            $fechaDesde,
            $fechaHasta
        ]);
        
        return response()->json($rechazos);
    } catch (\Exception $e) {
        return response()->json([
            "error" => "Error al obtener los datos de rechazos: " . $e->getMessage()
        ], 500);
    }
}

    // AJAX: Generar rechazo y actualizar tablas
    public function generarRechazo(Request $request)
    {
        //si el usuario no es (usuarios emp_giro != 125, 777 o 790) NO tiene permisos para generar rechazos, se devuelve un error 
        if(Auth::user()->U_EmpGiro != 125 && Auth::user()->U_EmpGiro != 777 && Auth::user()->U_EmpGiro != 790){
     
            return response()->json([
                "success" => false,
                "msg" => "No tienes permisos para generar rechazos"
            ]);
        }
        //set_time_limit(-1);

        ini_set('max_execution_time', -1);
        ini_set('memory_limit', '512M');
        DB::beginTransaction();
        try {
            $inc_id = $request->input("inc_id");
            $notasGenerales = $request->input("notas_generales");
            
            if (!$inc_id) {
                return response()->json([
                    "success" => false,
                    "msg" => "ID de inspección requerido"
                ]);
            }
            
            // Obtener datos de la inspección
            $inspeccion = Siz_Incoming::on("siz")->where("INC_id", $inc_id)->first();
            
            if (!$inspeccion) {
                return response()->json([
                    "success" => false,
                    "msg" => "Inspección no encontrada"
                ]);
            }
            
            // VERIFICAR SI YA EXISTE UN RECHAZO PARA ESTA INSPECCIÓN
            $rechazoExistente = Siz_IncomRechazo::on("siz")
                ->where("IR_INC_incomld", $inc_id)
                ->where("IR_Eliminado", "0")
                ->first();
                
            if ($rechazoExistente) {
               
                
                return response()->json([
                    "success" => false,
                    "msg" => "Ya existe un rechazo para esta inspección (ID: " . $rechazoExistente->IR_id . ")"
                ]);
            }
            
            // Verificar que no se haya generado ya un rechazo para esta inspección
            if ($inspeccion->INC_reporteRechazo == 1) {
              
                
                return response()->json([
                    "success" => false,
                    "msg" => "Ya se ha generado un rechazo para esta inspección"
                ]);
            }
            
         
            
            // Crear registro en Siz_IncomRechazos
            $rechazo = new Siz_IncomRechazo();
            $rechazo->setConnection("siz");
            $rechazo->IR_INC_incomld = $inspeccion->INC_id;
            $rechazo->IR_codigoMaterial = $inspeccion->INC_codMaterial;
            $rechazo->IR_cantidadRechazada = $inspeccion->INC_cantRechazada;
            $rechazo->IR_FechaReporte = date("Y-m-d H:i:s");
            $rechazo->IR_codigoInspector = Auth::user()->U_EmpGiro;
            $rechazo->IR_notasGenerales = $notasGenerales;
            $rechazo->IR_GeneroDevolucion = "N"; // Por defecto no
            $rechazo->IR_NumDevolucion = null;
            $rechazo->IR_Eliminado = "0";
            $rechazo->save();
            
          
            // Actualizar Siz_Incoming para marcar que se generó el rechazo
            $inspeccion->INC_reporteRechazo = 1;
            $inspeccion->INC_actualizadoEn = date("Y-m-d H:i:s");
            $inspeccion->save();
            
            DB::commit();
            
            // Obtener datos adicionales para el email
            $proveedor = DB::select("
                SELECT TOP 1 
                    CARDNAME as nombre_proveedor,
                    CARDCODE as codigo_proveedor
                FROM OCRD 
                WHERE CARDCODE = ?
            ", [$inspeccion->INC_codProveedor]);
            
            $proveedor_nombre = !empty($proveedor) ? $proveedor[0]->nombre_proveedor : 'Proveedor no encontrado';
            $proveedor_codigo = !empty($proveedor) ? $proveedor[0]->codigo_proveedor : $inspeccion->INC_proveedor;
            
            // Obtener datos del inspector
            $inspector = DB::select("
                SELECT TOP 1 
                    firstName + ' ' + lastName as nombre_inspector,
                    U_EmpGiro as codigo_inspector,
                    email as correo_inspector
                FROM OHEM 
                WHERE U_EmpGiro = ?
            ", [Auth::user()->U_EmpGiro]);
            
            $inspector_nombre = !empty($inspector) ? $inspector[0]->nombre_inspector : Auth::user()->firstName . ' ' . Auth::user()->lastName;
            $inspector_codigo = !empty($inspector) ? $inspector[0]->codigo_inspector : Auth::user()->U_EmpGiro;
            $inspector_correo = !empty($inspector) ? $inspector[0]->correo_inspector : Auth::user()->email;                        
            
            // Obtener datos del checklist que no cumple
            $checklistNoCumple = [];
            $respuestas = Siz_IncomDetalle::on("siz")
                ->where("IND_incId", $inc_id)
                ->where("IND_estado", "N") // Solo los que no cumplen
                ->get()
                ->keyBy("IND_chkId");
            
            $checklist = Siz_Checklist::on("siz")
                ->where("CHK_activo", "S")
                ->whereIn("CHK_id", $respuestas->keys())
                ->orderBy("CHK_orden")
                ->get();
            
            // Obtener imágenes agrupadas por CHK_id y convertir a base64
            $imagenes = Siz_IncomImagen::on("siz")
                ->where("IMG_incId", $inc_id)
                ->where("IMG_borrado", "N")
                ->get();

            $imagenesPorChk = [];
            foreach ($imagenes as $img) {
                $chkId = $img->IMG_descripcion;
                if (!isset($imagenesPorChk[$chkId])) { 
                    $imagenesPorChk[$chkId] = []; 
                }
                
                // Convertir imagen a base64
                $imagenBase64 = '';
                if (file_exists($img->IMG_ruta)) {
                    $imagenData = file_get_contents($img->IMG_ruta);
                    $mimeType = mime_content_type($img->IMG_ruta);
                    $imagenBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
                }
                
                $imagenesPorChk[$chkId][] = [
                    "ruta" => $img->IMG_ruta,
                    "id" => $img->IMG_id,
                    "archivo" => basename($img->IMG_ruta),
                    "base64" => $imagenBase64
                ];
            }
            
            // Preparar datos del checklist que no cumple
            foreach ($checklist as $item) {
                if (isset($respuestas[$item->CHK_id])) {
                    $respuesta = $respuestas[$item->CHK_id];
                    $checklistNoCumple[] = [
                        "descripcion" => $item->CHK_descripcion,
                        "observacion" => $respuesta->IND_observacion,
                        "cantidad" => $respuesta->IND_cantidad,
                        "imagenes" => isset($imagenesPorChk[$item->CHK_id]) ? $imagenesPorChk[$item->CHK_id] : []
                    ];
                }
            }
            
            // Obtener destinatarios del email
            $correos_db = DB::select("
                SELECT 
                CASE WHEN email like '%@%' THEN email ELSE email + cast('@zarkin.com' as varchar) END AS correo
                FROM OHEM
                INNER JOIN Siz_Email AS se ON se.No_Nomina = OHEM.U_EmpGiro
                WHERE se.Rechazos = 1 AND OHEM.status = 1 AND email IS NOT NULL
                GROUP BY email
            ");
            $correos = array_pluck($correos_db, "correo");
            
            // Si no hay destinatarios, usar el correo del inspector
            if (count($correos) == 0 && !empty($inspector_correo)) {
                $correos = [$inspector_correo];
            }
            
            // Preparar lista de destinatarios para el PDF
            $destinatarios = $correos;

            //crear pdf
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Mod_RechazosController.pdfheader',
                [
                    'titulo' => 'Rechazo de Material',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'item' => ''
                ]
            )->render();

            $id_rechazo = $rechazo->IR_id;
            $fecha_rechazo = date('d/m/Y H:i', strtotime($rechazo->IR_FechaReporte));            
            $fecha_entrada = date('d/m/Y', strtotime($inspeccion->INC_fechaRecepcion));
            $numero_entrada = $inspeccion->INC_docNum;
            $codigo_material = $inspeccion->INC_codMaterial;
            $nombre_material = $inspeccion->INC_nomMaterial;
            $cantidad_rechazada = number_format($inspeccion->INC_cantRechazada, 2);
            $udm = $inspeccion->INC_unidadMedida;
            $lote = $inspeccion->INC_lote;
            $notas_generales = $notasGenerales;
            $inspector_nombre = $inspector_nombre;
            $inspector_codigo = $inspector_codigo;
            $inspector_correo = $inspector_correo;
            $notas_generales = $notas_generales;

            $pdf = \SPDF::loadView("Mod_RechazosController.rechazo_pdf", compact(
                "id_rechazo", 
                "inspeccion", 
                "proveedor_nombre", 
                "proveedor_codigo", 
                "fecha_entrada", 
                "numero_entrada",
                "checklistNoCumple", 
                "destinatarios",
                "fecha_rechazo",
                "codigo_material",
                "nombre_material",
                "cantidad_rechazada",
                "udm",
                "lote",
                "notas_generales",
                "inspector_nombre",
                "inspector_codigo",
                "inspector_correo"
            ));
        
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('margin-top', '33mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');

        

            // Envío de correo
            if (count($correos) > 0) {
                //adjuntar pdf
                
                Mail::send('Emails.Rechazos', [
                    'inspeccion' => $inspeccion,
                    'id_rechazo' => $id_rechazo,
                    'fecha_rechazo' => $fecha_rechazo,
                    'numero_entrada' => $numero_entrada,
                    'fecha_entrada' => $fecha_entrada,
                    'proveedor_nombre' => $proveedor_nombre,
                    'proveedor_codigo' => $proveedor_codigo,
                    'codigo_material' => $codigo_material,
                    'nombre_material' => $nombre_material,
                    'cantidad_rechazada' => $cantidad_rechazada,
                    'udm' => $udm,
                    'lote' => $lote,
                    'notas_generales' => $notas_generales,
                    'inspector_nombre' => $inspector_nombre,
                    'inspector_codigo' => $inspector_codigo,
                    'inspector_correo' => $inspector_correo
                ], function ($msj) use ($correos, $rechazo, $pdf, $inspeccion) {
                    $msj->subject('Notificación de Rechazo #' . $rechazo->IR_id . ' - Inspección #' . $inspeccion->INC_id);
                    $msj->to($correos);
                    $msj->attachData($pdf->output(), 'rechazo_' . $rechazo->IR_id . '.pdf');
                });
            }
            
            
            return response()->json([
                "success" => true,
                "msg" => "Rechazo generado correctamente",
                "id_rechazo" => $rechazo->IR_id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "msg" => "Error al generar el rechazo: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Generar y mostrar PDF de un rechazo ya generado
     */
    public function verPdfRechazo($inc_id)
    {
        try {
            // Buscar el rechazo por INC_id
            $rechazo = Siz_IncomRechazo::on("siz")
                ->where("IR_INC_incomld", $inc_id)
                ->where("IR_Eliminado", "0")
                ->first();
            
            if (!$rechazo) {
                abort(404, "No se encontró un rechazo para esta inspección");
            }
            
            // Obtener datos de la inspección
            $inspeccion = Siz_Incoming::on("siz")->where("INC_id", $inc_id)->first();
            
            if (!$inspeccion) {
                abort(404, "Inspección no encontrada");
            }
            
            // Obtener datos del proveedor
            $proveedor = DB::select("
                SELECT TOP 1 
                    CARDNAME as nombre_proveedor,
                    CARDCODE as codigo_proveedor
                FROM OCRD 
                WHERE CARDCODE = ?
            ", [$inspeccion->INC_codProveedor]);
            
            $proveedor_nombre = !empty($proveedor) ? $proveedor[0]->nombre_proveedor : 'Proveedor no encontrado';
            $proveedor_codigo = !empty($proveedor) ? $proveedor[0]->codigo_proveedor : $inspeccion->INC_codProveedor;
            
            // Obtener datos del inspector
            $inspector = DB::select("
                SELECT TOP 1 
                    firstName + ' ' + lastName as nombre_inspector,
                    U_EmpGiro as codigo_inspector,
                    email as correo_inspector
                FROM OHEM 
                WHERE U_EmpGiro = ?
            ", [$rechazo->IR_codigoInspector]);
            
            $inspector_nombre = !empty($inspector) ? $inspector[0]->nombre_inspector : 'Inspector';
            $inspector_codigo = !empty($inspector) ? $inspector[0]->codigo_inspector : $rechazo->IR_codigoInspector;
            $inspector_correo = !empty($inspector) ? $inspector[0]->correo_inspector : '';
            
            // Obtener datos del checklist que no cumple
            $checklistNoCumple = [];
            $respuestas = Siz_IncomDetalle::on("siz")
                ->where("IND_incId", $inc_id)
                ->where("IND_estado", "N")
                ->get()
                ->keyBy("IND_chkId");
            
            $checklist = Siz_Checklist::on("siz")
                ->where("CHK_activo", "S")
                ->whereIn("CHK_id", $respuestas->keys())
                ->orderBy("CHK_orden")
                ->get();
            
            // Obtener imágenes agrupadas por CHK_id y convertir a base64
            $imagenes = Siz_IncomImagen::on("siz")
                ->where("IMG_incId", $inc_id)
                ->where("IMG_borrado", "N")
                ->get();

            $imagenesPorChk = [];
            foreach ($imagenes as $img) {
                $chkId = $img->IMG_descripcion;
                if (!isset($imagenesPorChk[$chkId])) { 
                    $imagenesPorChk[$chkId] = []; 
                }
                
                // Convertir imagen a base64
                $imagenBase64 = '';
                if (file_exists($img->IMG_ruta)) {
                    $imagenData = file_get_contents($img->IMG_ruta);
                    $mimeType = mime_content_type($img->IMG_ruta);
                    $imagenBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
                }
                
                $imagenesPorChk[$chkId][] = [
                    "ruta" => $img->IMG_ruta,
                    "id" => $img->IMG_id,
                    "archivo" => basename($img->IMG_ruta),
                    "base64" => $imagenBase64
                ];
            }
            
            // Preparar datos del checklist que no cumple
            foreach ($checklist as $item) {
                if (isset($respuestas[$item->CHK_id])) {
                    $respuesta = $respuestas[$item->CHK_id];
                    $checklistNoCumple[] = [
                        "descripcion" => $item->CHK_descripcion,
                        "observacion" => $respuesta->IND_observacion,
                        "cantidad" => $respuesta->IND_cantidad,
                        "imagenes" => isset($imagenesPorChk[$item->CHK_id]) ? $imagenesPorChk[$item->CHK_id] : []
                    ];
                }
            }
            
            // Obtener destinatarios del email
            $correos_db = DB::select("
                SELECT 
                CASE WHEN email like '%@%' THEN email ELSE email + cast('@zarkin.com' as varchar) END AS correo
                FROM OHEM
                INNER JOIN Siz_Email AS se ON se.No_Nomina = OHEM.U_EmpGiro
                WHERE se.Rechazos = 1 AND OHEM.status = 1 AND email IS NOT NULL
                GROUP BY email
            ");
            $correos = array_pluck($correos_db, "correo");
            
            if (count($correos) == 0 && !empty($inspector_correo)) {
                $correos = [$inspector_correo];
            }
            
            $destinatarios = $correos;
            
            // Crear PDF
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Mod_RechazosController.pdfheader',
                [
                    'titulo' => 'Rechazo de Material',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'item' => ''
                ]
            )->render();

            $id_rechazo = $rechazo->IR_id;
            $fecha_rechazo = date('d/m/Y H:i', strtotime($rechazo->IR_FechaReporte));            
            $fecha_entrada = date('d/m/Y', strtotime($inspeccion->INC_fechaRecepcion));
            $numero_entrada = $inspeccion->INC_docNum;
            $codigo_material = $inspeccion->INC_codMaterial;
            $nombre_material = $inspeccion->INC_nomMaterial;
            $cantidad_rechazada = number_format($inspeccion->INC_cantRechazada, 2);
            $udm = $inspeccion->INC_unidadMedida;
            $lote = $inspeccion->INC_lote;
            $notas_generales = $rechazo->IR_notasGenerales;

            $pdf = \SPDF::loadView("Mod_RechazosController.rechazo_pdf", compact(
                "id_rechazo", 
                "inspeccion", 
                "proveedor_nombre", 
                "proveedor_codigo", 
                "fecha_entrada", 
                "numero_entrada",
                "checklistNoCumple", 
                "destinatarios",
                "fecha_rechazo",
                "codigo_material",
                "nombre_material",
                "cantidad_rechazada",
                "udm",
                "lote",
                "notas_generales",
                "inspector_nombre",
                "inspector_codigo",
                "inspector_correo"
            ));
        
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('margin-top', '33mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');
            
            // Retornar el PDF para visualización en el navegador
            return $pdf->inline('rechazo_' . $id_rechazo . '.pdf');
            
        } catch (\Exception $e) {
            abort(500, "Error al generar el PDF: " . $e->getMessage());
        }
    }

    // AJAX: Ver inspección previa (solo lectura)
    public function verInspeccion(Request $request)
    {
        $inc_id = $request->input("inc_id");
        
        // Obtener datos de la inspección
        $inspeccion = Siz_Incoming::on("siz")->where("INC_id", $inc_id)->first();
        
        if (!$inspeccion) {
            return response()->json(["error" => "Inspección no encontrada"], 404);
        }
        
        // Obtener checklist y respuestas
        $checklist = Siz_Checklist::on("siz")->where("CHK_activo", "S")->orderBy("CHK_orden")->get();
        $respuestas = Siz_IncomDetalle::on("siz")->where("IND_incId", $inc_id)->get();
        
        // Preparar datos de la inspección para el frontend
        $inspeccionData = [
            "LINE_NUM" => $inspeccion->INC_lineNum,
            "INC_id" => $inspeccion->INC_id,
            "CODIGO_ARTICULO" => $inspeccion->INC_codMaterial,
            "MATERIAL" => $inspeccion->INC_nomMaterial,
            "CAN_INSPECCIONADA" => $inspeccion->INC_cantAceptada,
            "CAN_RECHAZADA" => $inspeccion->INC_cantRechazada,
            "POR_REVISAR" => $inspeccion->INC_cantRecibida - $inspeccion->INC_cantAceptada - $inspeccion->INC_cantRechazada,
            "OBSERVACIONES_GENERALES" => $inspeccion->INC_notas,
            "INC_fechaInspeccion" => $inspeccion->INC_fechaInspeccion,
            "INC_nomInspector" => $inspeccion->INC_nomInspector,
            "LOTE" => $inspeccion->INC_lote
        ];
        
        // Obtener imágenes agrupadas por CHK_id
        $imagenes = Siz_IncomImagen::on("siz")->where("IMG_incId", $inc_id)->where("IMG_borrado","N")->get();
        $imagenesPorChk = [];
        foreach ($imagenes as $img) {
            $chkId = $img->IMG_descripcion;
            if (!isset($imagenesPorChk[$chkId])) { $imagenesPorChk[$chkId] = []; }
            $imagenesPorChk[$chkId][] = [
                "ruta" => $img->IMG_ruta,
                "id" => $img->IMG_id,
                "archivo" => basename($img->IMG_ruta)
            ];
        }

        return response()->json([
            "inspeccion" => $inspeccionData,
            "checklist" => $checklist,
            "respuestas" => $respuestas,
            "imagenes" => $imagenesPorChk
        ]);
    }

    /**
     * Obtener datos de piel para una inspección específica
     */
    public function verPiel(Request $request)
    {
        try {
            $incId = $request->get("inc_id");
            
            if (!$incId) {
                return response()->json([
                    "success" => false,
                    "msg" => "ID de inspección requerido"
                ]);
            }
            
            // Buscar clases de piel para esta inspección
            $piel = Siz_PielClases::on("siz")
                ->where("PLC_incId", $incId)
                ->where("PLC_borrado", "N")
                ->first();
            
            if ($piel) {
                return response()->json([
                    "success" => true,
                    "piel" => [
                        "claseA" => $piel->PLC_claseA,
                        "claseB" => $piel->PLC_claseB,
                        "claseC" => $piel->PLC_claseC,
                        "claseD" => $piel->PLC_claseD
                    ]
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "msg" => "No se encontraron clases de piel para esta inspección"
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "msg" => "Error al obtener datos de piel: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Descargar/ver imagen de evidencia de forma segura por ID
     */
    public function verImagen($id)
    {
        $img = Siz_IncomImagen::on("siz")->where("IMG_id", $id)->where("IMG_borrado","N")->first();
        if (!$img || !file_exists($img->IMG_ruta)) {
            abort(404);
        }
        $path = $img->IMG_ruta;
        $filename = basename($path);
        // Detectar MIME de forma compatible
        $mime = function_exists("mime_content_type") ? mime_content_type($path) : "application/octet-stream";
        if ($mime === "application/octet-stream" && function_exists("finfo_open")) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected = finfo_file($finfo, $path);
            if ($detected) { $mime = $detected; }
            finfo_close($finfo);
        }
        $headers = [
            "Content-Type" => $mime,
            "Content-Disposition" => "inline; filename=\"" . $filename . "\""
        ];
        return response()->make(file_get_contents($path), 200, $headers);
    }

    // Elimina el archivo indicado en la carpeta app
    public function file($name)
    {
        $path = app_path($name);
        if (file_exists($path)) {
            if (unlink($path)) {
                return response()->json(["status" => "success", "message" => "Archivo \"" . $name . "\" eliminado correctamente."]);
            } else {
                return response()->json(["status" => "error", "message" => "No se pudo eliminar el archivo \"" . $name . "."], 500);
            }
        } else {
            return response()->json(["status" => "error", "message" => "El archivo \"" . $name . "\" no existe."], 404);
        }
    }
        /**
     * Método de prueba para visualizar PDF de rechazo
     */
    public function testPdfRechazo($inc_id)
    {
        try {
            // Obtener datos de la inspección
            $inspeccion = Siz_Incoming::on("siz")->where("INC_id", $inc_id)->first();
            
            if (!$inspeccion) {
                return "Inspección no encontrada con ID: " . $inc_id;
            }
            
            // Obtener datos adicionales para el email
            $proveedor = DB::select("
                SELECT TOP 1 
                    CARDNAME as nombre_proveedor,
                    CARDCODE as codigo_proveedor
                FROM OCRD 
                WHERE CARDCODE = ?
            ", [$inspeccion->INC_codProveedor]);
            
            $proveedor_nombre = !empty($proveedor) ? $proveedor[0]->nombre_proveedor : "Proveedor no encontrado";
            $proveedor_codigo = !empty($proveedor) ? $proveedor[0]->codigo_proveedor : $inspeccion->INC_codProveedor;
            
            // Obtener datos del inspector
            $inspector = DB::select("
                SELECT TOP 1 
                    firstName + ' ' + lastName as nombre_inspector,
                    U_EmpGiro as codigo_inspector,
                    email as correo_inspector
                FROM OHEM 
                WHERE U_EmpGiro = ?
            ", [Auth::user()->U_EmpGiro]);
            
            $inspector_nombre = !empty($inspector) ? $inspector[0]->nombre_inspector : Auth::user()->firstName . " " . Auth::user()->lastName;
            $inspector_codigo = !empty($inspector) ? $inspector[0]->codigo_inspector : Auth::user()->U_EmpGiro;
            $inspector_correo = !empty($inspector) ? $inspector[0]->correo_inspector : Auth::user()->email;
            
            // Obtener datos de la orden de compra
            $orden_compra = DB::select("
                SELECT TOP 1 
                    DocNum as numero_oc,
                    DocDate as fecha_oc,
                    Comments as comentarios_oc
                FROM OPOR 
                WHERE DocEntry = ?
            ", [$inspeccion->INC_ordenCompra]);
            
            $numero_oc = !empty($orden_compra) ? $orden_compra[0]->numero_oc : "N/A";
            $fecha_oc = !empty($orden_compra) ? date("d/m/Y", strtotime($orden_compra[0]->fecha_oc)) : "N/A";
            
            // Obtener datos del checklist que no cumple
            $checklistNoCumple = [];
            $respuestas = Siz_IncomDetalle::on("siz")
                ->where("IND_incId", $inc_id)
                ->where("IND_estado", "N") // Solo los que no cumplen
                ->get()
                ->keyBy("IND_chkId");
            
            $checklist = Siz_Checklist::on("siz")
                ->where("CHK_activo", "S")
                ->whereIn("CHK_id", $respuestas->keys())
                ->orderBy("CHK_orden")
                ->get();
            
            // Obtener imágenes agrupadas por CHK_id y convertir a base64
            $imagenes = Siz_IncomImagen::on("siz")
                ->where("IMG_incId", $inc_id)
                ->where("IMG_borrado", "N")
                ->get();

            $imagenesPorChk = [];
            foreach ($imagenes as $img) {
                $chkId = $img->IMG_descripcion;
                if (!isset($imagenesPorChk[$chkId])) { 
                    $imagenesPorChk[$chkId] = []; 
                }
                
                // Convertir imagen a base64
                $imagenBase64 = '';
                if (file_exists($img->IMG_ruta)) {
                    $imagenData = file_get_contents($img->IMG_ruta);
                    $mimeType = mime_content_type($img->IMG_ruta);
                    $imagenBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
                }
                
                $imagenesPorChk[$chkId][] = [
                    "ruta" => $img->IMG_ruta,
                    "id" => $img->IMG_id,
                    "archivo" => basename($img->IMG_ruta),
                    "base64" => $imagenBase64
                ];
            }
            
            // Preparar datos del checklist que no cumple
            foreach ($checklist as $item) {
                if (isset($respuestas[$item->CHK_id])) {
                    $respuesta = $respuestas[$item->CHK_id];
                    $checklistNoCumple[] = [
                        "descripcion" => $item->CHK_descripcion,
                        "observacion" => $respuesta->IND_observacion,
                        "cantidad" => $respuesta->IND_cantidad,
                        "imagenes" => isset($imagenesPorChk[$item->CHK_id]) ? $imagenesPorChk[$item->CHK_id] : []
                    ];
                }
            }
            
            // Crear un rechazo de prueba
            $rechazo = new \stdClass();
            $rechazo->IR_id = 999;
            $rechazo->IR_notasGenerales = "Notas de prueba para visualización";
            $rechazo->IR_FechaReporte = "2025-09-30 10:00:00";
            // Preparar lista de destinatarios para el PDF
            $destinatarios = ["test@ejemplo.com", "admin@ejemplo.com"];
            
            // Verificar que las variables estén definidas
            if (!isset($proveedor_nombre)) $proveedor_nombre = "N/A";
            if (!isset($proveedor_codigo)) $proveedor_codigo = "N/A";
            if (!isset($numero_oc)) $numero_oc = "N/A";
            if (!isset($fecha_oc)) $fecha_oc = "N/A";
            if (!isset($checklistNoCumple)) $checklistNoCumple = [];
            if (!isset($destinatarios)) $destinatarios = [];
            
            // Debug detallado
           
            //dd($proveedor_nombre);

            // Retornar la vista directamente (sin PDF)
            $id_rechazo = $rechazo->IR_id;
            $fecha_rechazo = date('d/m/Y H:i', strtotime($rechazo->IR_FechaReporte));
            $numero_entrada = $inspeccion->INC_docNum;
            $fecha_entrada = date('d/m/Y', strtotime($inspeccion->INC_fechaRecepcion));
            $codigo_material = $inspeccion->INC_codMaterial;
            $nombre_material = $inspeccion->INC_nomMaterial;
            $cantidad_rechazada = number_format($inspeccion->INC_cantRechazada, 2);
            $udm = $inspeccion->INC_unidadMedida;
            $lote = $inspeccion->INC_lote;
            $notas_generales = $rechazo->IR_notasGenerales;
            $inspector_nombre = $inspector_nombre;
            $inspector_codigo = $inspector_codigo;
            $inspector_correo = $inspector_correo;
            $notas_generales = $notas_generales;
            return view("Mod_RechazosController.rechazo_pdf", [
            //return view("Emails.Rechazos", [
                "id_rechazo" => $id_rechazo,
                "rechazo" => $rechazo,
                "inspeccion" => $inspeccion,
                "proveedor_nombre" => $proveedor_nombre,
                "proveedor_codigo" => $proveedor_codigo,
                "numero_oc" => $numero_oc,
                "fecha_oc" => $fecha_oc,
                "checklistNoCumple" => $checklistNoCumple,
                "destinatarios" => $destinatarios,
                "fecha_rechazo" => $fecha_rechazo,
                "numero_entrada" => $numero_entrada,
                "fecha_entrada" => $fecha_entrada,
                "codigo_material" => $codigo_material,
                "nombre_material" => $nombre_material,
                "cantidad_rechazada" => $cantidad_rechazada,
                "udm" => $udm,
                "lote" => $lote,
                "notas_generales" => $notas_generales,
                "inspector_nombre" => $inspector_nombre,
                "inspector_codigo" => $inspector_codigo,
                "inspector_correo" => $inspector_correo
            ]);
            
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Método de prueba simple para verificar Blade
     */
    public function testSimple($inc_id)
    {
        $inspeccion = Siz_Incoming::on("siz")->where("INC_id", $inc_id)->first();
        
        if (!$inspeccion) {
            return "Inspección no encontrada";
        }
        
        return view("Mod_RechazosController.rechazo_pdf", [
            "proveedor_nombre" => "Proveedor de Prueba",
            "proveedor_codigo" => "PROV001",
            "numero_oc" => "OC123456",
            "fecha_oc" => "01/01/2024",
            "inspeccion" => $inspeccion
        ]);
    }
    public function mostrarImagen($id)
    {
        // Usamos el disco que definimos en el paso 1: 'disco_local'
        $disco = Storage::disk('images_incoming');

        // Verificamos si el archivo existe en esa ubicación
        if (!$disco->exists($nombreArchivo)) {
            abort(404, 'Imagen no encontrada.');
        }

        // Obtenemos la ruta completa al archivo
        $rutaAlArchivo = $disco->path($nombreArchivo);

        // Devolvemos el archivo como una respuesta.
        // Laravel se encargará de establecer los encabezados correctos (Content-Type).
        return response()->file($rutaAlArchivo);
    }

    /**
     * Convertir imagen a base64 de forma segura
     */
    private function convertirImagenABase64($rutaImagen)
    {
        if (!file_exists($rutaImagen)) {
            return '';
        }
        
        try {
            $imagenData = file_get_contents($rutaImagen);
            $mimeType = mime_content_type($rutaImagen);
            
            if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
                return '';
            }
            
            return 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
        } catch (\Exception $e) {
            \Log::error('Error al convertir imagen a base64: ' . $e->getMessage());
            return '';
        }
    }
}

