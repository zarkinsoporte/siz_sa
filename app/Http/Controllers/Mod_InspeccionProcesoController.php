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
use Illuminate\Support\Facades\Mail;
use App\User;

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

            if (!$cp_of) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró la Orden en control de piso'
                ], 404);
            }
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
            //dd($historial);
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
            
            // Para cada ítem del checklist, obtener empleados con permisos en el área inspeccionada
            // y el empleado responsable del historial
            foreach ($checklist as $item) {
                $areaInspeccionada = $item->CHK_area_inspeccionada;
                
                // Obtener empleados con permiso en esta área
                $empleadosConPermiso = DB::table('OHEM')
                    ->select('empID', 'firstName', 'lastName', 'U_CP_CT')
                    ->whereRaw("CHARINDEX(?, U_CP_CT) > 0", [$areaInspeccionada])
                    ->where('Active', 'Y')
                    ->orderBy('firstName')
                    ->get();
                
                $item->empleados_permitidos = $empleadosConPermiso;
                
                // Buscar en el historial el empleado que trabajó en esta área
                $empleadoResponsable = null;
                foreach ($historial as $h) {
                    if ($h->U_CT == $areaInspeccionada) {
                        // Obtener el empID del empleado
                        $empleadoHist = DB::table('OHEM')
                            ->where(DB::raw("firstName + ' ' + lastName"), $h->Empleado)
                            ->first();
                        if ($empleadoHist) {
                            $empleadoResponsable = $empleadoHist->empID;
                        }
                        break;
                    }
                }
                
                $item->empleado_responsable_default = $empleadoResponsable;
            }
            
            // 8. Obtener inspecciones previas para esta OP y centro
            $inspeccionesPrevias = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_centroInspeccion', $estacionActual)
                ->where('IPR_borrado', 'N')
                ->orderBy('IPR_id', 'desc')
                ->get();
            
            // 9. Calcular cantidad disponible para inspeccionar
            // Cantidad Planeada - Suma de inspecciones ACEPTADAS
            $cantidadAceptada = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_centroInspeccion', $estacionActual)
                ->where('IPR_borrado', 'N')
                ->where('IPR_estado', 'ACEPTADO')
                ->sum('IPR_cantInspeccionada');
            
            // sum() retorna null si no hay registros, convertir a 0
            $cantidadAceptada = $cantidadAceptada ?? 0;
            
            $cantidadDisponible = $ordenProduccion->CantidadPlaneada - $cantidadAceptada;
            
            return response()->json([
                'success' => true,
                'op' => $ordenProduccion,
                'centro_inspeccion' => [
                    'id' => $estacionActual,
                    'nombre' => $estacionActualInfo->Name,
                    'cantidad_disponible' => $cantidadDisponible,
                    'cantidad_en_centro' => $cantidadEnCentro,
                    'cantidad_aceptada' => $cantidadAceptada
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
            $estado = $request->input('estado'); // 'ACEPTADO' o 'RECHAZADO'
            
            // Validar estado
            if (!in_array($estado, ['ACEPTADO', 'RECHAZADO'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Estado de inspección inválido'
                ], 400);
            }
            
            // Validar cantidad inspeccionada
            if ($cantInspeccionada <= 0) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La cantidad a inspeccionar debe ser mayor a cero'
                ], 400);
            }
            
            // Si el estado es RECHAZADO, validar que la suma de rechazos no supere la cantidad disponible
            if ($estado === 'RECHAZADO') {
                // Obtener cantidad planeada de la OP
                $ordenProduccion = DB::table('OWOR')
                    ->where('DocNum', $op)
                    ->value('PlannedQty');
                
                if (!$ordenProduccion) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'No se encontró la Orden de Producción'
                    ], 404);
                }
                
                // Calcular suma de inspecciones ACEPTADAS en esta estación
                $cantidadAceptada = Siz_InspeccionProceso::on('siz')
                    ->where('IPR_op', $op)
                    ->where('IPR_centroInspeccion', $centroInspeccion)
                    ->where('IPR_borrado', 'N')
                    ->where('IPR_estado', 'ACEPTADO')
                    ->sum('IPR_cantInspeccionada');
                
                $cantidadAceptada = $cantidadAceptada ?? 0;
                
                // Calcular suma de inspecciones RECHAZADAS en esta estación (sin contar la actual si se está editando)
                $cantidadRechazada = Siz_InspeccionProceso::on('siz')
                    ->where('IPR_op', $op)
                    ->where('IPR_centroInspeccion', $centroInspeccion)
                    ->where('IPR_borrado', 'N')
                    ->where('IPR_estado', 'RECHAZADO');
                
                // Si hay un ID de inspección (edición), excluirla del cálculo
                $iprId = $request->input('ipr_id');
                if ($iprId) {
                    $cantidadRechazada->where('IPR_id', '!=', $iprId);
                }
                
                $cantidadRechazada = $cantidadRechazada->sum('IPR_cantInspeccionada');
                $cantidadRechazada = $cantidadRechazada ?? 0;
                
                // Calcular cantidad disponible
                $cantidadDisponible = $ordenProduccion - $cantidadAceptada;
                
                // Validar que la suma de rechazos no supere la cantidad disponible
                $totalRechazos = $cantidadRechazada + $cantInspeccionada;
                
                if ($totalRechazos > $cantidadDisponible) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'La suma de rechazos (' . number_format($totalRechazos, 2) . ') no puede superar la cantidad disponible (' . number_format($cantidadDisponible, 2) . '). Cantidad Planeada: ' . number_format($ordenProduccion, 2) . ', Cantidad Aceptada: ' . number_format($cantidadAceptada, 2)
                    ], 400);
                }
            }
            
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
            $inspeccion->IPR_estado = $estado; // 'ACEPTADO' o 'RECHAZADO'
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
                        
                        // Guardar el empleado responsable del defecto
                        if ($request->has('checklist_empleado.' . $chkId)) {
                            $detalle->IPD_empID = $request->input('checklist_empleado.' . $chkId);
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
                                            //$chkNombre = $chk ? preg_replace('/[^A-Za-z0-9_-]+/', '', str_replace(' ', '_', $chk->CHK_descripcion)) : ('CHK_'.$chk_id);
                                            $nombre = $inspeccion->IPR_id . '_' . uniqid() . '.' . $extension;
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
            
            // Si la inspección fue rechazada, enviar correo de notificación
            if ($estado === 'RECHAZADO') {
                try {
                    // 1. Obtener supervisores de la estación de inspección
                    $supervisor = User::where('position', 4)
                        ->where('U_CP_CT', 'like', '%' . $centroInspeccion . '%')
                        ->where('status', 1)
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->get();
                    //dd($supervisor);
                    $correos = [];
                    foreach ($supervisor as $s) {
                        if(!is_null($s->email)) {
                          $correos[] = $s->email. '@zarkin.com';
                        }
                    }
                    //dd($correos);
                    // 2. Obtener correos de usuarios con Reprocesos = 1
                    $correos_db = DB::select("
                        SELECT 
                        CASE WHEN email like '%@%' THEN email ELSE email + cast('@zarkin.com' as varchar) END AS correo
                        FROM OHEM
                        INNER JOIN Siz_Email AS se ON se.No_Nomina = OHEM.U_EmpGiro
                        WHERE se.Reprocesos = 1 AND OHEM.status = 1 AND email IS NOT NULL
                        GROUP BY email
                    ");
                    
                    $correosUsuarios = array_pluck($correos_db, 'correo');
                    $correos = array_merge($correos, $correosUsuarios);
                    
                    // Eliminar duplicados
                    $correos = array_unique($correos);
                    
                    // 3. Obtener defectos del checklist (solo los que son "No Cumple")
                    $defectos = [];
                    if ($request->has('checklist')) {
                        foreach ($request->input('checklist') as $chkId => $respuesta) {
                            if ($respuesta === 'No Cumple') {
                                $chk = Siz_Checklist::on('siz')->where('CHK_id', $chkId)->first();
                                $defecto = [
                                    'punto' => $chk ? $chk->CHK_descripcion : 'Punto ' . $chkId,
                                    'observacion' => $request->input('checklist_observacion.' . $chkId, ''),
                                    'empleado' => null
                                ];
                                
                                // Obtener nombre del empleado responsable si existe
                                if ($request->has('checklist_empleado.' . $chkId)) {
                                    $empID = $request->input('checklist_empleado.' . $chkId);
                                    $empleado = DB::table('OHEM')
                                        ->where('empID', $empID)
                                        ->first();
                                    if ($empleado) {
                                        $defecto['empleado'] = $empleado->firstName . ' ' . $empleado->lastName;
                                    }
                                }
                                
                                $defectos[] = $defecto;
                            }
                        }
                    }
                    
                    // 4. Enviar correo si hay destinatarios
                    if (count($correos) > 0) {
                        Mail::send('Emails.RechazoInspeccionProceso', [
                            'dt' => date('d/M/Y h:m:s'),
                            'No_Nomina' => Auth::user()->U_EmpGiro,
                            'Nom_Inspector' => Auth::user()->getName(),
                            'op' => $op,
                            'cod_articulo' => $codArticulo,
                            'nom_articulo' => $nomArticulo,
                            'cant_inspeccionada' => $cantInspeccionada,
                            'nombre_centro' => $nombreCentro,
                            'observaciones' => $observaciones ? $observaciones : 'Sin observaciones',
                            'defectos' => $defectos
                        ], function ($msj) use ($correos) {
                            $msj->subject('Notificación SIZ - Rechazo de Inspección en Proceso');
                            $msj->to($correos);
                        });
                    }
                } catch (\Exception $e) {
                    // No fallar el guardado si hay error en el correo, solo loguear
                    \Log::error('Error al enviar correo de rechazo de inspección: ' . $e->getMessage());
                }
            }
            
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
    
    /**
     * AJAX: Obtener historial completo de rechazos con detalles
     */
    public function getHistorialCompleto(Request $request)
    {
        try {
            $op = $request->input('op');
            $centro = $request->input('centro');
            
            if (!$op) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Debe proporcionar el número de OP'
                ], 400);
            }
            
            // Obtener solo inspecciones rechazadas
            $inspecciones = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_centroInspeccion', $centro)
                ->where('IPR_estado', 'RECHAZADO')
                ->where('IPR_borrado', 'N')
                ->orderBy('IPR_fechaInspeccion', 'desc')
                ->get();
            
            // Para cada inspección, obtener sus detalles
            foreach ($inspecciones as $insp) {
                // Obtener detalles del checklist
                $detalles = DB::connection('siz')
                    ->table('Siz_InspeccionProcesoDetalle')
                    ->join('Siz_Checklist', 'Siz_InspeccionProcesoDetalle.IPD_chkId', '=', 'Siz_Checklist.CHK_id')
                    ->leftJoin(DB::raw('dbo.OHEM'), 'Siz_InspeccionProcesoDetalle.IPD_empID', '=', 'OHEM.empID')
                    ->select(
                        'Siz_InspeccionProcesoDetalle.*',
                        'Siz_Checklist.CHK_descripcion',
                        DB::raw("OHEM.firstName + ' ' + OHEM.lastName as empleado_nombre")
                    )
                    ->where('Siz_InspeccionProcesoDetalle.IPD_iprId', $insp->IPR_id)
                    ->where('Siz_InspeccionProcesoDetalle.IPD_borrado', 'N')
                    ->orderBy('Siz_Checklist.CHK_orden')
                    ->get();
                
                $insp->detalles = $detalles;
            }
            
            return response()->json([
                'success' => true,
                'inspecciones' => $inspecciones
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al cargar el historial: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * AJAX: Obtener defectivos de una estación de calidad específica
     */
    public function getDefectivosPorEstacion(Request $request)
    {
        try {
            $area = $request->input('area');
            $op = $request->input('op');
            
            if (!$area) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Debe proporcionar el código del área'
                ], 400);
            }
            
            // Obtener defectivos de la estación
            $defectivos = Siz_Checklist::on('siz')
                ->where('CHK_activo', 'S')
                ->where('CHK_area', $area)
                ->orderBy('CHK_orden')
                ->get();
            
            // Obtener historial de la OP para determinar empleados responsables
            $historial = [];
            if ($op) {
                $historial = DB::select("
                    SELECT 
                        [@CP_LOGOF].U_CT,
                        [@PL_RUTAS].Name AS NombreEstacion,
                        [@PL_RUTAS].U_Calidad AS EsCalidad,
                        OHEM.empID,
                        OHEM.firstName + ' ' + OHEM.lastName AS Empleado,
                        MIN([@CP_LOGOF].U_FechaHora) AS PrimeraFecha,
                        MAX([@CP_LOGOF].U_FechaHora) AS UltimaFecha,
                        SUM([@CP_LOGOF].U_Cantidad) AS CantidadElaborada
                    FROM [@CP_LOGOF]
                    INNER JOIN [@PL_RUTAS] ON [@CP_LOGOF].U_CT = [@PL_RUTAS].Code
                    LEFT JOIN OHEM ON [@CP_LOGOF].U_idEmpleado = OHEM.empID
                    WHERE [@CP_LOGOF].U_DocEntry = ?
                    GROUP BY [@CP_LOGOF].U_CT, [@PL_RUTAS].Name, [@PL_RUTAS].U_Calidad, OHEM.empID, OHEM.firstName, OHEM.lastName
                    ORDER BY MIN([@CP_LOGOF].U_FechaHora)
                ", [$op]);
            }
            
            // Para cada defectivo, obtener empleados con permisos en el área inspeccionada
            foreach ($defectivos as $item) {
                $areaInspeccionada = $item->CHK_area_inspeccionada;
                
                // Obtener empleados con permiso en esta área
                $empleadosConPermiso = DB::table('OHEM')
                    ->select('empID', 'firstName', 'lastName', 'U_CP_CT')
                    ->whereRaw("CHARINDEX(?, U_CP_CT) > 0", [$areaInspeccionada])
                    ->where('Active', 'Y')
                    ->orderBy('firstName')
                    ->get();
                
                $item->empleados_permitidos = $empleadosConPermiso;
                
                // Buscar en el historial el empleado que trabajó en esta área
                $empleadoResponsable = null;
                foreach ($historial as $h) {
                    if ($h->U_CT == $areaInspeccionada && $h->empID) {
                        $empleadoResponsable = $h->empID;
                        break;
                    }
                }
                
                $item->empleado_responsable_default = $empleadoResponsable;
            }
            
            return response()->json([
                'success' => true,
                'defectivos' => $defectivos,
                'msg' => 'Defectivos cargados correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al cargar los defectivos: ' . $e->getMessage()
            ], 500);
        }
    }
}

