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
    public function buscarRechazos()
    {
        try {
            // Ejecutar el procedimiento almacenado SIZ_Calidad_RechazosMaterial
            $rechazos = DB::connection("siz")->select("EXEC SIZ_Calidad_RechazosMaterial");
            
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
        DB::connection("siz")->beginTransaction();
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
            $rechazo->IR_Eliminado = "N";
            $rechazo->save();
            
            // Actualizar Siz_Incoming para marcar que se generó el rechazo
            $inspeccion->INC_reporteRechazo = 1;
            $inspeccion->INC_actualizadoEn = date("Y-m-d H:i:s");
            $inspeccion->save();
            
            DB::connection("siz")->commit();
            
            // Obtener datos adicionales para el email
            $proveedor = DB::connection("siz")->select("
                SELECT TOP 1 
                    CARDNAME as nombre_proveedor,
                    CARDCODE as codigo_proveedor
                FROM OCRD 
                WHERE CARDCODE = ?
            ", [$inspeccion->INC_proveedor]);
            
            $proveedor_nombre = !empty($proveedor) ? $proveedor[0]->nombre_proveedor : 'Proveedor no encontrado';
            $proveedor_codigo = !empty($proveedor) ? $proveedor[0]->codigo_proveedor : $inspeccion->INC_proveedor;
            
            // Obtener datos del inspector
            $inspector = DB::connection("siz")->select("
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
            
            // Obtener datos de la orden de compra
            $orden_compra = DB::connection("siz")->select("
                SELECT TOP 1 
                    DocNum as numero_oc,
                    DocDate as fecha_oc,
                    Comments as comentarios_oc
                FROM OPOR 
                WHERE DocEntry = ?
            ", [$inspeccion->INC_ordenCompra]);
            
            $numero_oc = !empty($orden_compra) ? $orden_compra[0]->numero_oc : 'N/A';
            $fecha_oc = !empty($orden_compra) ? date('d/m/Y', strtotime($orden_compra[0]->fecha_oc)) : 'N/A';
            //crear pdf
            
            // Envío de correo
            $correos_db = DB::connection("siz")->select("
                SELECT 
                CASE WHEN email like '%@%' THEN email ELSE email + cast('@zarkin.com' as varchar) END AS correo
                FROM OHEM
                INNER JOIN Siz_Email AS se ON se.No_Nomina = OHEM.U_EmpGiro
                WHERE se.Rechazos = 1 AND OHEM.status = 1 AND email IS NOT NULL
                GROUP BY email
            ");
            $correos = array_pluck($correos_db, 'correo');
            
            if (count($correos) > 0) {
                Mail::send('Emails.Rechazos', [
                    'id_rechazo' => $rechazo->IR_id,
                    'fecha_rechazo' => date('d/m/Y H:i:s', strtotime($rechazo->IR_FechaReporte)),
                    'nota_entrada' => $inspeccion->INC_notaEntrada,
                    'proveedor_nombre' => $proveedor_nombre,
                    'proveedor_codigo' => $proveedor_codigo,
                    'numero_oc' => $numero_oc,
                    'fecha_oc' => $fecha_oc,
                    'codigo_material' => $inspeccion->INC_codMaterial,
                    'nombre_material' => $inspeccion->INC_nomMaterial,
                    'cantidad_rechazada' => number_format($inspeccion->INC_cantRechazada, 2),
                    'udm' => $inspeccion->INC_udm,
                    'lote' => $inspeccion->INC_lote,
                    'notas_generales' => $notasGenerales,
                    'inspector_nombre' => $inspector_nombre,
                    'inspector_codigo' => $inspector_codigo,
                    'inspector_correo' => $inspector_correo
                ], function ($msj) use ($correos, $rechazo) {
                    $msj->subject('Notificación de Rechazo #' . $rechazo->IR_id . ' - ' . date('d/m/Y'));
                    $msj->to($correos);
                });
            }
            
            
            return response()->json([
                "success" => true,
                "msg" => "Rechazo generado correctamente",
                "id_rechazo" => $rechazo->IR_id
            ]);
            
        } catch (\Exception $e) {
            DB::connection("siz")->rollBack();
            return response()->json([
                "success" => false,
                "msg" => "Error al generar el rechazo: " . $e->getMessage()
            ]);
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
    
}

