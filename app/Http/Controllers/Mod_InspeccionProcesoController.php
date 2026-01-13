<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Siz_InspeccionProceso;
use App\Modelos\Siz_InspeccionProcesoDetalle;
use App\Modelos\Siz_InspeccionProcesoImagen;
use App\Modelos\Siz_Checklist;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\LOGOT;
use App\OP;
use App\SAP;
use App\SAPi;
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
            // Buscar el registro de @CP_OF que corresponde a una estación de calidad
            // Puede haber múltiples registros para la misma OP en diferentes estaciones
            $cp_of = DB::table('@CP_OF')
                ->join('@PL_RUTAS', '@CP_OF.U_CT', '=', '@PL_RUTAS.Code')
                ->where('@CP_OF.U_DocEntry', $ordenProduccion->DocEntry)
                ->where('@PL_RUTAS.U_Calidad', 'S')
                ->select('@CP_OF.*')
                ->first();
            //dd($cp_of, DB::getQueryLog());
            if (!$cp_of) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontró la Orden en control de piso en una estación de calidad'
                ], 404);
            }
            // 2. Obtener la estación actual de la OP
            $estacionActual = $cp_of->U_CT;
            // La cantidad disponible es lo recibido menos lo procesado (para traslados parciales)
            $cantidadEnCentro = $cp_of->U_Recibido - ($cp_of->U_Procesado ?? 0);
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
            // La cantidad disponible es: (U_Recibido - U_Procesado) - Suma de inspecciones ACEPTADAS
            // Esto considera traslados parciales: si ya se procesó parte de lo recibido, solo queda disponible lo no procesado
            $cantidadAceptada = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_centroInspeccion', $estacionActual)
                ->where('IPR_borrado', 'N')
                ->where('IPR_estado', 'ACEPTADO')
                ->sum('IPR_cantInspeccionada');
            
            // sum() retorna null si no hay registros, convertir a 0
            $cantidadAceptada = $cantidadAceptada ?? 0;
            
            // La cantidad disponible es: (Recibido - Procesado) - Inspecciones Aceptadas
            // $cantidadEnCentro ya tiene el cálculo de (U_Recibido - U_Procesado)
            $cantidadDisponible = $cantidadEnCentro;//- $cantidadAceptada;
            
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
            
            // Validaciones básicas de campos requeridos
            if (empty($op)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'El número de OP es requerido'
                ], 400);
            }
            
            if (empty($docEntry)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'El DocEntry de la OP es requerido'
                ], 400);
            }
            
            if (empty($centroInspeccion)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'El centro de inspección es requerido'
                ], 400);
            }
            
            // Validar estado
            if (empty($estado) || !in_array($estado, ['ACEPTADO', 'RECHAZADO'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'El estado de inspección es requerido y debe ser ACEPTADO o RECHAZADO'
                ], 400);
            }
            
            // Validar cantidad inspeccionada
            if (empty($cantInspeccionada) || $cantInspeccionada <= 0) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La cantidad a inspeccionar debe ser mayor a cero'
                ], 400);
            }
            
            // Validar que la cantidad sea numérica
            if (!is_numeric($cantInspeccionada)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'La cantidad a inspeccionar debe ser un número válido'
                ], 400);
            }
            
            // NOTA: Validación de rechazos comentada - Se permite rechazar múltiples veces sin restricción
            // Los rechazos no se contabilizan para limitar la cantidad disponible (puede implementarse en el futuro)
            /*
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
                //dd($cantidadRechazada->get());
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
            */
            
            // Verificar si todo el checklist está en "No Aplica"
            $checklistData = $request->input('checklist', []);
            $todosNoAplica = true;
            $tieneNoCumple = false;
            if (!empty($checklistData)) {
                foreach ($checklistData as $chkId => $respuesta) {
                    if ($respuesta && $respuesta !== 'No Aplica') {
                        $todosNoAplica = false;
                        if ($respuesta === 'No Cumple') {
                            $tieneNoCumple = true;
                        }
                    }
                }
            }
            \Log::info("INSPECCION_PROCESO: Estado del checklist - OP: {$op}, Estado inspección: {$estado}, Todos 'No Aplica': " . ($todosNoAplica ? 'Sí' : 'No') . ", Tiene 'No Cumple': " . ($tieneNoCumple ? 'Sí' : 'No') . ", Items checklist: " . count($checklistData));
            
            // ============================================================
            // PAUSA TEMPORAL: Traslado comentado para permitir capturar solo la inspección
            // TODO: Descomentar cuando se haya capturado la inspección faltante
            // ============================================================
            
            // IMPORTANTE: Si la inspección es ACEPTADA, PRIMERO intentar avanzar la OP
            // Si el avance falla, NO se guardará la inspección (rollback completo)
            $avanceExitoso = false;
            /* COMENTADO TEMPORALMENTE - INICIO
            if ($estado === 'ACEPTADO') {
                \Log::info("INSPECCION_PROCESO: Iniciando avance de OP antes de guardar inspección. OP: {$op}, Estado: {$estado}, Centro: {$centroInspeccion}, Cantidad: {$cantInspeccionada}, Todos 'No Aplica': " . ($todosNoAplica ? 'Sí' : 'No'));
                
                try {
                    // Obtener el registro de @CP_OF para la estación actual
                    // IMPORTANTE: Buscar el registro específico de la estación de calidad actual
                    $cp_of_actual = DB::table('@CP_OF')
                        ->where('U_DocEntry', $docEntry)
                        ->where('U_CT', $centroInspeccion)
                        ->first();
                    
                    \Log::info("INSPECCION_PROCESO: Búsqueda de @CP_OF - DocEntry: {$docEntry}, Centro: {$centroInspeccion}, Encontrado: " . ($cp_of_actual ? "Sí (Code: {$cp_of_actual->Code})" : "No"));
                    
                    // Si no se encuentra, intentar buscar cualquier registro de la OP en esta estación
                    if (!$cp_of_actual) {
                        $cp_of_actual = DB::table('@CP_OF')
                            ->where('U_DocEntry', $docEntry)
                            ->where('U_CT', $centroInspeccion)
                            ->first();
                        \Log::info("INSPECCION_PROCESO: Segunda búsqueda de @CP_OF (sin filtro U_Reproceso) - Encontrado: " . ($cp_of_actual ? "Sí (Code: {$cp_of_actual->Code})" : "No"));
                    }
                    
                    if ($cp_of_actual) {
                        $Code_actual = OP::find($cp_of_actual->Code);
                        
                        // Validar que el registro existe
                        if (!$Code_actual) {
                            \Log::error("INSPECCION_PROCESO: No se encontró el registro OP con Code: {$cp_of_actual->Code}");
                            throw new \Exception('No se encontró el registro de control de piso (Code: ' . $cp_of_actual->Code . ') para avanzar la OP ' . $op);
                        }
                        
                        \Log::info("INSPECCION_PROCESO: Registro OP encontrado - Code: {$Code_actual->Code}, U_CT: {$Code_actual->U_CT}, U_Recibido: {$Code_actual->U_Recibido}, U_Procesado: {$Code_actual->U_Procesado}, U_Entregado: {$Code_actual->U_Entregado}");
                        
                        // Proceder con el avance de la OP
                        // Obtener la estación siguiente (formato número)
                        $U_CT_siguiente = OP::getEstacionSiguiente($Code_actual->Code, 2);
                        
                        \Log::info("INSPECCION_PROCESO: Estación siguiente obtenida: {$U_CT_siguiente}");
                        
                        // Remover comillas simples si las tiene para comparación
                        $U_CT_siguiente_clean = str_replace("'", "", $U_CT_siguiente);
                        
                        // Validar que no sea error en ruta
                        if ($U_CT_siguiente == "'Error en ruta'") {
                            \Log::error("INSPECCION_PROCESO: Error en ruta para OP: {$op}");
                            throw new \Exception('Error en ruta: La OP no tiene una estación siguiente válida. OP: ' . $op);
                        }
                        // Si la siguiente estación es "Terminar OP", generar recibo de producción
                        else if ($U_CT_siguiente == "'Terminar OP'") {
                            \Log::info("INSPECCION_PROCESO: La siguiente estación es 'Terminar OP' para OP: {$op}");
                            
                            // Obtener información de la OP
                            $orden_owor = DB::table('OWOR')
                                ->where('DocNum', $op)
                                ->first();
                            
                            if (!$orden_owor) {
                                \Log::error("INSPECCION_PROCESO: No se encontró la OP {$op} para terminar");
                                throw new \Exception('No se encontró la OP ' . $op . ' para terminar desde inspección');
                            }
                            
                            // Verificar tipos de cambio en SAP
                            $rates = DB::table('ORTT')->where('RateDate', date('Y-m-d'))->get();
                           
                            if (count($rates) < 3) {
                                \Log::error("INSPECCION_PROCESO: Faltan tipos de cambio en SAP para OP: {$op}");
                                throw new \Exception('No están capturados todos los "Tipos de Cambio" en SAP. Se requieren al menos 3 tipos de cambio para el día de hoy.');
                            }
                            
                            // Obtener nombre del usuario que reporta
                            $apellido = $this->getApellidoPaternoUsuario(explode(' ', Auth::user()->lastName));
                            $usuario_reporta = explode(' ', Auth::user()->firstName)[0] . ' ' . $apellido;
                            
                            // Validar cantidad
                            if (($orden_owor->PlannedQty) >= (floatval($orden_owor->CmpltQty) + floatval($cantInspeccionada))) {
                                // Generar recibo de producción en SAP
                                \Log::info("INSPECCION_PROCESO: Generando recibo de producción en SAP para OP: {$op}, Cantidad: {$cantInspeccionada}");
                                $result = SAPi::ReciboProduccion($op, $orden_owor->Warehouse, $cantInspeccionada, "Reportado por: " . $usuario_reporta, "Recibo de producción - Inspección en Proceso");
                                \Log::info("INSPECCION_PROCESO: Resultado de recibo de producción: {$result}");
                            } else if (($orden_owor->PlannedQty) == floatval($orden_owor->CmpltQty)) {
                                $result = 'Recibo creado SIZ';
                                \Log::info("INSPECCION_PROCESO: OP ya completada, no se genera recibo. Resultado: {$result}");
                            } else {
                                \Log::error("INSPECCION_PROCESO: Error de cantidad - OP: {$op}, Planeada: {$orden_owor->PlannedQty}, Completada: {$orden_owor->CmpltQty}, A inspeccionar: {$cantInspeccionada}");
                                throw new \Exception('La cantidad Completada no puede ser mayor a la Planeada. OP: ' . $op . ', Planeada: ' . $orden_owor->PlannedQty . ', Completada: ' . floatval($orden_owor->CmpltQty) . ', A inspeccionar: ' . $cantInspeccionada);
                            }
                            
                            // Si el recibo se creó correctamente
                            if (strpos($result, 'Recibo') !== false) {
                                // Crear registro en @CP_LOGOF
                                $dt = date('Ymd H:i:s');
                                $consecutivologof = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                                $log = new LOGOF();
                                $log->Code = ((int) $consecutivologof[0]->Code) + 1;
                                $log->Name = ((int) $consecutivologof[0]->Code) + 1;
                                $log->U_idEmpleado = Auth::user()->empID;
                                $log->U_CT = $Code_actual->U_CT;
                                $log->U_Status = "T";
                                $log->U_FechaHora = $dt;
                                $log->U_DocEntry = $docEntry;
                                $log->U_Cantidad = $cantInspeccionada;
                                $log->U_Reproceso = 'N';
                                $log->save();
                                \Log::info("INSPECCION_PROCESO: Registro LOGOF creado - Code: {$log->Code}, U_CT: {$log->U_CT}, Cantidad: {$log->U_Cantidad}");
                                
                                // Actualizar información de la OP después del recibo
                                $orden_owor = DB::table('OWOR')->where('DocNum', $op)->first();
                                
                                // Si la OP quedó completa, eliminar de control de piso y cerrar en SAP
                                if ($orden_owor->PlannedQty == floatval($orden_owor->CmpltQty)) {
                                    \Log::info("INSPECCION_PROCESO: OP {$op} completada, eliminando de control de piso");
                                    $Code_actual->delete();
                                    
                                    // Validar si se puede cerrar la OP en SAP
                                    $cerrar = DB::select("
                                        SELECT T0.[DocNum] as Orden, T0.[ItemCode] as Codigo, T0.[PlannedQty] as Planeado, T0.[CmpltQty] as Terminado ,T0.UpdateDate as Actualizado 
                                        FROM OWOR T0 
                                        LEFT JOIN (
                                            SELECT OWOR.DocNum as OP, sum(WOR1.PlannedQty) as Cantidad 
                                            FROM OWOR 
                                            inner join WOR1 on WOR1.DocEntry = OWOR.DocEntry 
                                            inner join OITM A1 on WOR1.ItemCode = A1.ItemCode 
                                            WHERE OWOR.[PlannedQty] <= OWOR.[CmpltQty] 
                                            and OWOR.[status] <> 'L' 
                                            and WOR1.IssueType = 'M' 
                                            and WOR1.IssuedQty < WOR1.PlannedQty 
                                            and A1.ItmsGrpCod <> 113 
                                            group by OWOR.DocNum
                                        ) VAL on T0.DocNum = VAL.OP 
                                        WHERE T0.DocNum = ? 
                                        and T0.[PlannedQty] <= T0.[CmpltQty] 
                                        and T0.[status] <> 'L' 
                                        and VAL.Cantidad is null
                                    ", [$op]);
                                    
                                    if (count($cerrar) > 0) {
                                        \Log::info("INSPECCION_PROCESO: Cerrando OP {$op} en SAP");
                                        SAP::ProductionOrderStatus($op, 2); // Cerrar Orden en SAP
                                    }
                                } else if ($orden_owor->PlannedQty > floatval($orden_owor->CmpltQty)) {
                                    // Si aún hay cantidad pendiente, actualizar la estación actual
                                    $Code_actual->U_Entregado += floatval($cantInspeccionada);
                                    $Code_actual->U_Procesado += floatval($cantInspeccionada);
                                    $Code_actual->save();
                                    \Log::info("INSPECCION_PROCESO: Actualizando estación actual - U_Procesado: {$Code_actual->U_Procesado}, U_Entregado: {$Code_actual->U_Entregado}");
                                }
                                
                                \Log::info('INSPECCION_PROCESO: OP terminada desde inspección: ' . $op . ' - Resultado: ' . $result);
                                $avanceExitoso = true;
                            } else {
                                \Log::error("INSPECCION_PROCESO: Error al generar recibo de producción - Resultado: {$result}");
                                throw new \Exception('Error al generar recibo de producción en SAP: ' . $result);
                            }
                        }
                        // Si hay estación siguiente normal, avanzar la OP
                        else if ($U_CT_siguiente != $Code_actual->U_CT) {
                            \Log::info("INSPECCION_PROCESO: Avanzando a estación siguiente - Estación actual: {$Code_actual->U_CT}, Estación siguiente: {$U_CT_siguiente_clean}");
                            
                            // Obtener cantidad planeada de la OP
                            $CantOrden = DB::table('OWOR')
                                ->where('DocEntry', $docEntry)
                                ->first();
                            
                            if (!$CantOrden) {
                                \Log::error("INSPECCION_PROCESO: No se encontró la OP con DocEntry {$docEntry}");
                                throw new \Exception('No se encontró la OP con DocEntry ' . $docEntry . ' para avanzar desde inspección');
                            }
                            
                            $cantO = (int) $CantOrden->PlannedQty;
                            \Log::info("INSPECCION_PROCESO: Cantidad planeada de OP: {$cantO}");
                            
                            // Buscar si ya existe registro para la estación siguiente
                            $Code_siguiente = OP::where('U_CT', $U_CT_siguiente_clean)
                                ->where('U_DocEntry', $docEntry)
                                ->get();
                            
                            \Log::info("INSPECCION_PROCESO: Búsqueda de estación siguiente - Encontrados: " . count($Code_siguiente) . " registros");
                            
                            $dt = date('Ymd H:i');
                            
                            if (count($Code_siguiente) == 1) {
                                // La estación siguiente ya existe, actualizar cantidad recibida
                                $Code_siguiente = OP::where('U_CT', $U_CT_siguiente_clean)
                                    ->where('U_DocEntry', $docEntry)
                                    ->first();
                                
                                \Log::info("INSPECCION_PROCESO: Estación siguiente existe - Code: {$Code_siguiente->Code}, U_Recibido actual: {$Code_siguiente->U_Recibido}, A agregar: {$cantInspeccionada}");
                                
                                if (($cantInspeccionada + $Code_siguiente->U_Recibido) <= $cantO) {
                                    $Code_siguiente->U_Recibido = $Code_siguiente->U_Recibido + $cantInspeccionada;
                                    $Code_siguiente->save();
                                    \Log::info("INSPECCION_PROCESO: Estación siguiente actualizada - U_Recibido: {$Code_siguiente->U_Recibido}");
                                } else {
                                    \Log::error("INSPECCION_PROCESO: Error de cantidad en estación siguiente - Cantidad Orden: {$cantO}, Recibida actual: {$Code_siguiente->U_Recibido}, A agregar: {$cantInspeccionada}");
                                    throw new \Exception('La cantidad total recibida no debe ser mayor a la cantidad de la Orden. OP: ' . $op . ', Cantidad Orden: ' . $cantO . ', Recibida actual: ' . $Code_siguiente->U_Recibido . ', A agregar: ' . $cantInspeccionada);
                                }
                            } else if (count($Code_siguiente) == 0) {
                                // La estación siguiente no existe, crear nuevo registro
                                \Log::info("INSPECCION_PROCESO: Creando nueva estación siguiente - U_CT: {$U_CT_siguiente_clean}");
                                
                                $consecutivo = DB::select('select max (CONVERT(INT,Code)) as Code from [@CP_Of]');
                                $newCode = new OP();
                                $newCode->Code = ((int) $consecutivo[0]->Code) + 1;
                                $newCode->Name = ((int) $consecutivo[0]->Code) + 1;
                                $newCode->U_DocEntry = $docEntry;
                                $newCode->U_CT = $U_CT_siguiente_clean;
                                $newCode->U_Entregado = 0;
                                $newCode->U_Orden = $U_CT_siguiente_clean;
                                $newCode->U_Procesado = 0;
                                $newCode->U_Recibido = $cantInspeccionada;
                                $newCode->U_Reproceso = "N";
                                $newCode->U_Defectuoso = 0;
                                $newCode->U_Comentarios = "";
                                $newCode->U_CTCalidad = 0;
                                $newCode->save();
                                \Log::info("INSPECCION_PROCESO: Nueva estación creada - Code: {$newCode->Code}, U_CT: {$newCode->U_CT}, U_Recibido: {$newCode->U_Recibido}");
                                
                                // Crear registro en @CP_LOGOT (log de inicio de estación)
                                $consecutivologot = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                                $lot = new LOGOT();
                                $lot->Code = ((int) $consecutivologot[0]->Code) + 1;
                                $lot->Name = ((int) $consecutivologot[0]->Code) + 1;
                                $lot->U_idEmpleado = Auth::user()->empID;
                                $lot->U_CT = $Code_actual->U_CT;
                                $lot->U_Status = "O";
                                $lot->U_FechaHora = $dt;
                                $lot->U_OP = $docEntry; // U_OP en LOGOT es el DocEntry de OWOR
                                $lot->save();
                                \Log::info("INSPECCION_PROCESO: Registro LOGOT creado - Code: {$lot->Code}, U_CT: {$lot->U_CT}");
                            } else {
                                // Hay múltiples registros duplicados
                                \Log::error("INSPECCION_PROCESO: Registros duplicados en estación siguiente - OP: {$op}, Estación: {$U_CT_siguiente_clean}, Cantidad: " . count($Code_siguiente));
                                throw new \Exception('Existen registros duplicados en la siguiente estación. OP: ' . $op . ', Estación siguiente: ' . $U_CT_siguiente_clean);
                            }
                            
                            // Actualizar estación actual: incrementar procesado y entregado
                            $Code_actual->U_Procesado = $Code_actual->U_Procesado + $cantInspeccionada;
                            $Code_actual->U_Entregado = $Code_actual->U_Entregado + $cantInspeccionada;
                            $Code_actual->save();
                            \Log::info("INSPECCION_PROCESO: Estación actual actualizada - U_Procesado: {$Code_actual->U_Procesado}, U_Entregado: {$Code_actual->U_Entregado}");
                            
                            // Crear registro en @CP_LOGOF (log de finalización de estación)
                            $consecutivologof = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                            $log = new LOGOF();
                            $log->Code = ((int) $consecutivologof[0]->Code) + 1;
                            $log->Name = ((int) $consecutivologof[0]->Code) + 1;
                            $log->U_idEmpleado = Auth::user()->empID;
                            $log->U_CT = $Code_actual->U_CT;
                            $log->U_Status = "T";
                            $log->U_FechaHora = $dt;
                            $log->U_DocEntry = $docEntry;
                            $log->U_Cantidad = $cantInspeccionada;
                            $log->U_Reproceso = 'N';
                            $log->save();
                            \Log::info("INSPECCION_PROCESO: Registro LOGOF creado - Code: {$log->Code}, U_CT: {$log->U_CT}, Cantidad: {$log->U_Cantidad}");
                            
                            // Si la estación actual ya procesó todo, eliminarla
                            if (($Code_actual->U_Recibido > 0 && $cantO == $Code_actual->U_Procesado) ||
                                ($Code_actual->U_Recibido == $Code_actual->U_Procesado && $Code_actual->U_Recibido == $Code_actual->U_Entregado)) {
                                \Log::info("INSPECCION_PROCESO: Estación actual procesó todo, eliminando - Code: {$Code_actual->Code}");
                                $Code_actual->delete();
                            }
                            
                            $avanceExitoso = true;
                        } else {
                            \Log::warning("INSPECCION_PROCESO: La estación siguiente es igual a la actual - U_CT: {$Code_actual->U_CT}, Siguiente: {$U_CT_siguiente_clean}. No se avanza.");
                        }
                    } else {
                        // Si no se encuentra el registro de @CP_OF, lanzar excepción
                        \Log::error("INSPECCION_PROCESO: No se encontró el registro de @CP_OF para la OP {$op} en la estación {$centroInspeccion}");
                        throw new \Exception('No se encontró el registro de control de piso (@CP_OF) para la OP ' . $op . ' en la estación ' . $centroInspeccion . '. No se puede avanzar la orden.');
                    }
                } catch (\Exception $e) {
                    \Log::error("INSPECCION_PROCESO: Error al intentar avanzar OP {$op}: " . $e->getMessage());
                    \Log::error("INSPECCION_PROCESO: Stack trace: " . $e->getTraceAsString());
                    throw $e; // Re-lanzar la excepción para que se haga rollback
                }
                
                if (!$avanceExitoso) {
                    \Log::error("INSPECCION_PROCESO: El avance no fue exitoso pero no se lanzó excepción. OP: {$op}");
                    throw new \Exception('No se pudo avanzar la OP ' . $op . '. El avance no se completó correctamente.');
                }
                
                \Log::info("INSPECCION_PROCESO: Avance de OP completado exitosamente. OP: {$op}");
            }
            */ // COMENTADO TEMPORALMENTE - FIN
            
            // Para la pausa temporal, marcamos el avance como exitoso para permitir guardar la inspección
            if ($estado === 'ACEPTADO') {
                $avanceExitoso = true;
                \Log::info("INSPECCION_PROCESO: Modo pausa - Se omite el traslado, solo se guardará la inspección. OP: {$op}");
            }
            
            // Crear nueva inspección (solo si el avance fue exitoso o si es RECHAZADO)
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
            
            // Commit de la transacción de inspección (solo si todo salió bien)
            DB::connection('siz')->commit();
            
            // ============================================================
            // NUEVA LÓGICA: Intentar traslado DESPUÉS de guardar la inspección
            // Si el traslado falla, eliminar la inspección guardada
            // ============================================================
            if ($estado === 'ACEPTADO' && !$avanceExitoso) {
                \Log::info("INSPECCION_PROCESO: Iniciando traslado después de guardar inspección. OP: {$op}, Inspección ID: {$inspeccion->IPR_id}");
                
                try {
                    // Obtener el registro de @CP_OF para la estación actual
                    $cp_of_actual = DB::table('@CP_OF')
                        ->where('U_DocEntry', $docEntry)
                        ->where('U_CT', $centroInspeccion)
                        ->first();
                    
                    \Log::info("INSPECCION_PROCESO: Búsqueda de @CP_OF para traslado - DocEntry: {$docEntry}, Centro: {$centroInspeccion}, Encontrado: " . ($cp_of_actual ? "Sí (Code: {$cp_of_actual->Code})" : "No"));
                    
                    if (!$cp_of_actual) {
                        // Si no se encuentra el registro, lanzar excepción para eliminar la inspección
                        \Log::error("INSPECCION_PROCESO: No se encontró el registro de @CP_OF para la OP {$op} en la estación {$centroInspeccion} después de guardar inspección");
                        throw new \Exception('No se encontró el registro de control de piso (@CP_OF) para la OP ' . $op . ' en la estación ' . $centroInspeccion . '. Se eliminará la inspección guardada.');
                    }
                    
                    $Code_actual = OP::find($cp_of_actual->Code);
                    
                    if (!$Code_actual) {
                        \Log::error("INSPECCION_PROCESO: No se encontró el registro OP con Code: {$cp_of_actual->Code} después de guardar inspección");
                        throw new \Exception('No se encontró el registro de control de piso (Code: ' . $cp_of_actual->Code . ') para avanzar la OP ' . $op . '. Se eliminará la inspección guardada.');
                    }
                    
                    // Obtener la estación siguiente
                    $U_CT_siguiente = OP::getEstacionSiguiente($Code_actual->Code, 2);
                    $U_CT_siguiente_clean = str_replace("'", "", $U_CT_siguiente);
                    
                    if ($U_CT_siguiente == "'Error en ruta'") {
                        \Log::error("INSPECCION_PROCESO: Error en ruta para OP: {$op} después de guardar inspección");
                        throw new \Exception('Error en ruta: La OP no tiene una estación siguiente válida. OP: ' . $op . '. Se eliminará la inspección guardada.');
                    }
                    
                    // Si la siguiente estación es "Terminar OP", generar recibo de producción
                    if ($U_CT_siguiente == "'Terminar OP'") {
                        \Log::info("INSPECCION_PROCESO: La siguiente estación es 'Terminar OP' para OP: {$op} (después de guardar inspección)");
                        
                        $orden_owor = DB::table('OWOR')
                            ->where('DocNum', $op)
                            ->first();
                        
                        if (!$orden_owor) {
                            throw new \Exception('No se encontró la OP ' . $op . ' para terminar. Se eliminará la inspección guardada.');
                        }
                        
                        $rates = DB::table('ORTT')->where('RateDate', date('Y-m-d'))->get();
                        if (count($rates) < 3) {
                            throw new \Exception('No están capturados todos los "Tipos de Cambio" en SAP. Se requieren al menos 3 tipos de cambio para el día de hoy. Se eliminará la inspección guardada.');
                        }
                        
                        $apellido = $this->getApellidoPaternoUsuario(explode(' ', Auth::user()->lastName));
                        $usuario_reporta = explode(' ', Auth::user()->firstName)[0] . ' ' . $apellido;
                        
                        if (($orden_owor->PlannedQty) >= (floatval($orden_owor->CmpltQty) + floatval($cantInspeccionada))) {
                            \Log::info("INSPECCION_PROCESO: Generando recibo de producción en SAP para OP: {$op}, Cantidad: {$cantInspeccionada} (después de guardar inspección)");
                            $result = SAPi::ReciboProduccion($op, $orden_owor->Warehouse, $cantInspeccionada, "Reportado por: " . $usuario_reporta, "Recibo de producción - Inspección en Proceso");
                            \Log::info("INSPECCION_PROCESO: Resultado de recibo de producción: {$result}");
                        } else if (($orden_owor->PlannedQty) == floatval($orden_owor->CmpltQty)) {
                            $result = 'Recibo creado SIZ';
                        } else {
                            throw new \Exception('La cantidad Completada no puede ser mayor a la Planeada. OP: ' . $op . '. Se eliminará la inspección guardada.');
                        }
                        
                        if (strpos($result, 'Recibo') !== false) {
                            $dt = date('Ymd H:i:s');
                            $consecutivologof = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                            $log = new LOGOF();
                            $log->Code = ((int) $consecutivologof[0]->Code) + 1;
                            $log->Name = ((int) $consecutivologof[0]->Code) + 1;
                            $log->U_idEmpleado = Auth::user()->empID;
                            $log->U_CT = $Code_actual->U_CT;
                            $log->U_Status = "T";
                            $log->U_FechaHora = $dt;
                            $log->U_DocEntry = $docEntry;
                            $log->U_Cantidad = $cantInspeccionada;
                            $log->U_Reproceso = 'N';
                            $log->save();
                            
                            $orden_owor = DB::table('OWOR')->where('DocNum', $op)->first();
                            
                            // IMPORTANTE: NO eliminar CP_OF aquí si es la estación final
                            // Solo actualizar si aún hay cantidad pendiente
                            if ($orden_owor->PlannedQty == floatval($orden_owor->CmpltQty)) {
                                \Log::info("INSPECCION_PROCESO: OP {$op} completada, pero NO se elimina CP_OF aquí para evitar problemas");
                                // NO eliminar aquí: $Code_actual->delete();
                                
                                $cerrar = DB::select("
                                    SELECT T0.[DocNum] as Orden, T0.[ItemCode] as Codigo, T0.[PlannedQty] as Planeado, T0.[CmpltQty] as Terminado ,T0.UpdateDate as Actualizado 
                                    FROM OWOR T0 
                                    LEFT JOIN (
                                        SELECT OWOR.DocNum as OP, sum(WOR1.PlannedQty) as Cantidad 
                                        FROM OWOR 
                                        inner join WOR1 on WOR1.DocEntry = OWOR.DocEntry 
                                        inner join OITM A1 on WOR1.ItemCode = A1.ItemCode 
                                        WHERE OWOR.[PlannedQty] <= OWOR.[CmpltQty] 
                                        and OWOR.[status] <> 'L' 
                                        and WOR1.IssueType = 'M' 
                                        and WOR1.IssuedQty < WOR1.PlannedQty 
                                        and A1.ItmsGrpCod <> 113 
                                        group by OWOR.DocNum
                                    ) VAL on T0.DocNum = VAL.OP 
                                    WHERE T0.DocNum = ? 
                                    and T0.[PlannedQty] <= T0.[CmpltQty] 
                                    and T0.[status] <> 'L' 
                                    and VAL.Cantidad is null
                                ", [$op]);
                                
                                if (count($cerrar) > 0) {
                                    \Log::info("INSPECCION_PROCESO: Cerrando OP {$op} en SAP");
                                    SAP::ProductionOrderStatus($op, 2);
                                }
                            } else if ($orden_owor->PlannedQty > floatval($orden_owor->CmpltQty)) {
                                $Code_actual->U_Entregado += floatval($cantInspeccionada);
                                $Code_actual->U_Procesado += floatval($cantInspeccionada);
                                $Code_actual->save();
                            }
                            
                            \Log::info('INSPECCION_PROCESO: OP terminada desde inspección: ' . $op . ' - Resultado: ' . $result);
                        } else {
                            throw new \Exception('Error al generar recibo de producción en SAP: ' . $result . '. Se eliminará la inspección guardada.');
                        }
                    }
                    // Si hay estación siguiente normal, avanzar la OP
                    else if ($U_CT_siguiente != $Code_actual->U_CT) {
                        \Log::info("INSPECCION_PROCESO: Avanzando a estación siguiente - Estación actual: {$Code_actual->U_CT}, Estación siguiente: {$U_CT_siguiente_clean} (después de guardar inspección)");
                        
                        $CantOrden = DB::table('OWOR')
                            ->where('DocEntry', $docEntry)
                            ->first();
                        
                        if (!$CantOrden) {
                            throw new \Exception('No se encontró la OP con DocEntry ' . $docEntry . '. Se eliminará la inspección guardada.');
                        }
                        
                        $cantO = (int) $CantOrden->PlannedQty;
                        $Code_siguiente = OP::where('U_CT', $U_CT_siguiente_clean)
                            ->where('U_DocEntry', $docEntry)
                            ->get();
                        
                        $dt = date('Ymd H:i');
                        
                        if (count($Code_siguiente) == 1) {
                            $Code_siguiente = OP::where('U_CT', $U_CT_siguiente_clean)
                                ->where('U_DocEntry', $docEntry)
                                ->first();
                            
                            if (($cantInspeccionada + $Code_siguiente->U_Recibido) <= $cantO) {
                                $Code_siguiente->U_Recibido = $Code_siguiente->U_Recibido + $cantInspeccionada;
                                $Code_siguiente->save();
                            } else {
                                throw new \Exception('La cantidad total recibida no debe ser mayor a la cantidad de la Orden. OP: ' . $op . '. Se eliminará la inspección guardada.');
                            }
                        } else if (count($Code_siguiente) == 0) {
                            $consecutivo = DB::select('select max (CONVERT(INT,Code)) as Code from [@CP_Of]');
                            $newCode = new OP();
                            $newCode->Code = ((int) $consecutivo[0]->Code) + 1;
                            $newCode->Name = ((int) $consecutivo[0]->Code) + 1;
                            $newCode->U_DocEntry = $docEntry;
                            $newCode->U_CT = $U_CT_siguiente_clean;
                            $newCode->U_Entregado = 0;
                            $newCode->U_Orden = $U_CT_siguiente_clean;
                            $newCode->U_Procesado = 0;
                            $newCode->U_Recibido = $cantInspeccionada;
                            $newCode->U_Reproceso = "N";
                            $newCode->U_Defectuoso = 0;
                            $newCode->U_Comentarios = "";
                            $newCode->U_CTCalidad = 0;
                            $newCode->save();
                            
                            $consecutivologot = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                            $lot = new LOGOT();
                            $lot->Code = ((int) $consecutivologot[0]->Code) + 1;
                            $lot->Name = ((int) $consecutivologot[0]->Code) + 1;
                            $lot->U_idEmpleado = Auth::user()->empID;
                            $lot->U_CT = $Code_actual->U_CT;
                            $lot->U_Status = "O";
                            $lot->U_FechaHora = $dt;
                            $lot->U_OP = $docEntry;
                            $lot->save();
                        } else {
                            throw new \Exception('Existen registros duplicados en la siguiente estación. OP: ' . $op . '. Se eliminará la inspección guardada.');
                        }
                        
                        $Code_actual->U_Procesado = $Code_actual->U_Procesado + $cantInspeccionada;
                        $Code_actual->U_Entregado = $Code_actual->U_Entregado + $cantInspeccionada;
                        $Code_actual->save();
                        
                        $consecutivologof = DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                        $log = new LOGOF();
                        $log->Code = ((int) $consecutivologof[0]->Code) + 1;
                        $log->Name = ((int) $consecutivologof[0]->Code) + 1;
                        $log->U_idEmpleado = Auth::user()->empID;
                        $log->U_CT = $Code_actual->U_CT;
                        $log->U_Status = "T";
                        $log->U_FechaHora = $dt;
                        $log->U_DocEntry = $docEntry;
                        $log->U_Cantidad = $cantInspeccionada;
                        $log->U_Reproceso = 'N';
                        $log->save();
                        
                        // IMPORTANTE: NO eliminar CP_OF aquí si procesó todo
                        // Solo marcar como procesado, pero mantener el registro
                        if (($Code_actual->U_Recibido > 0 && $cantO == $Code_actual->U_Procesado) ||
                            ($Code_actual->U_Recibido == $Code_actual->U_Procesado && $Code_actual->U_Recibido == $Code_actual->U_Entregado)) {
                            \Log::info("INSPECCION_PROCESO: Estación actual procesó todo, pero NO se elimina CP_OF para evitar problemas. Code: {$Code_actual->Code}");
                            // NO eliminar aquí: $Code_actual->delete();
                        }
                    }
                    
                    \Log::info("INSPECCION_PROCESO: Traslado completado exitosamente después de guardar inspección. OP: {$op}");
                    
                } catch (\Exception $e) {
                    // Si el traslado falla, eliminar la inspección guardada
                    \Log::error("INSPECCION_PROCESO: Error en traslado después de guardar inspección. OP: {$op}, Error: " . $e->getMessage());
                    \Log::error("INSPECCION_PROCESO: Eliminando inspección guardada debido a error en traslado. Inspección ID: {$inspeccion->IPR_id}");
                    
                    try {
                        // Marcar la inspección como borrada
                        $inspeccion->IPR_borrado = 'S';
                        $inspeccion->IPR_actualizadoEn = date("Y-m-d H:i:s");
                        $inspeccion->save();
                        
                        // También marcar detalles e imágenes como borrados
                        Siz_InspeccionProcesoDetalle::on('siz')
                            ->where('IPD_iprId', $inspeccion->IPR_id)
                            ->update(['IPD_borrado' => 'S', 'IPD_actualizadoEn' => date("Y-m-d H:i:s")]);
                        
                        Siz_InspeccionProcesoImagen::on('siz')
                            ->where('IPI_iprId', $inspeccion->IPR_id)
                            ->update(['IPI_borrado' => 'S']);
                        
                        \Log::info("INSPECCION_PROCESO: Inspección marcada como borrada debido a error en traslado. Inspección ID: {$inspeccion->IPR_id}");
                    } catch (\Exception $deleteException) {
                        \Log::error("INSPECCION_PROCESO: Error al eliminar inspección después de fallo en traslado: " . $deleteException->getMessage());
                    }
                    
                    // Re-lanzar la excepción original para que el usuario vea el error
                    throw $e;
                }
            }
            
            // Si la inspección fue rechazada, enviar correo de notificación (fuera de la transacción)
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
                    
                    // 4. Obtener imágenes asociadas a la inspección rechazada
                    $imagenesEvidenciaQuery = DB::connection('siz')
                        ->table('Siz_InspeccionProcesoImagen as img')
                        ->leftJoin('Siz_Checklist', 'img.IPI_descripcion', '=', 'Siz_Checklist.CHK_id')
                        ->select(
                            'img.IPI_ruta as ruta',
                            'img.IPI_descripcion as chk_id',
                            'Siz_Checklist.CHK_descripcion as checklist'
                        )
                        ->where('img.IPI_iprId', $inspeccion->IPR_id)
                        ->where('img.IPI_borrado', 'N')
                        ->get();
                    
                    $imagenesEvidencia = collect($imagenesEvidenciaQuery)
                        ->map(function ($img) {
                            return [
                                'ruta' => $img->ruta,
                                'checklist' => $img->checklist ?? null,
                                'chk_id' => $img->chk_id
                            ];
                        })
                        ->values()
                        ->toArray();
                    
                    // 5. Enviar correo si hay destinatarios
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
                            'defectos' => $defectos,
                            'imagenes' => $imagenesEvidencia
                        ], function ($msj) use ($correos) {
                            $msj->subject('Notificación SIZ - Rechazo de Inspección en Proceso');
                            $msj->to($correos);
                            //$msj->to('albert91.me.d@gmail.com');
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
            
        } catch (\Illuminate\Database\QueryException $e) {
            DB::connection('siz')->rollBack();
            \Log::error('INSPECCION_PROCESO: Error de base de datos al guardar inspección: ' . $e->getMessage());
            \Log::error('INSPECCION_PROCESO: Query: ' . $e->getSql());
            \Log::error('INSPECCION_PROCESO: Bindings: ' . json_encode($e->getBindings()));
            
            // Determinar mensaje más específico según el código de error
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            if (strpos($errorMessage, 'timeout') !== false || strpos($errorMessage, 'timed out') !== false) {
                $mensaje = 'La operación tardó demasiado tiempo. Por favor, intente nuevamente.';
            } elseif (strpos($errorMessage, 'connection') !== false || strpos($errorMessage, 'connect') !== false) {
                $mensaje = 'Error de conexión con la base de datos. Por favor, contacte al administrador del sistema.';
            } elseif (strpos($errorMessage, 'foreign key') !== false || strpos($errorMessage, 'FOREIGN KEY') !== false) {
                $mensaje = 'Error de integridad de datos: Existe una referencia a otro registro. Por favor, verifique la información.';
            } elseif (strpos($errorMessage, 'duplicate') !== false || strpos($errorMessage, 'UNIQUE') !== false) {
                $mensaje = 'Error: Ya existe un registro con esta información. Por favor, verifique los datos.';
            } else {
                $mensaje = 'Error de base de datos al guardar la inspección. Por favor, contacte al administrador del sistema.';
            }
            
            return response()->json([
                'success' => false,
                'msg' => $mensaje
            ], 500);
        } catch (\PDOException $e) {
            DB::connection('siz')->rollBack();
            \Log::error('INSPECCION_PROCESO: Error PDO al guardar inspección: ' . $e->getMessage());
            
            $mensaje = 'Error de conexión con la base de datos. Por favor, intente nuevamente o contacte al administrador del sistema.';
            
            return response()->json([
                'success' => false,
                'msg' => $mensaje
            ], 500);
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            \Log::error('INSPECCION_PROCESO: Error al guardar inspección: ' . $e->getMessage());
            \Log::error('INSPECCION_PROCESO: Stack trace: ' . $e->getTraceAsString());
            \Log::error('INSPECCION_PROCESO: Archivo: ' . $e->getFile() . ' - Línea: ' . $e->getLine());
            
            // Determinar mensaje más específico según el tipo de excepción
            $errorMessage = $e->getMessage();
            $mensaje = 'Error al guardar la inspección';
            
            // Mensajes específicos para errores comunes
            if (strpos($errorMessage, 'No se encontró') !== false || strpos($errorMessage, 'not found') !== false) {
                $mensaje = $errorMessage;
            } elseif (strpos($errorMessage, 'permisos') !== false || strpos($errorMessage, 'permission') !== false) {
                $mensaje = 'No tiene permisos para realizar esta acción.';
            } elseif (strpos($errorMessage, 'SAP') !== false) {
                $mensaje = 'Error al comunicarse con SAP: ' . $errorMessage;
            } elseif (strpos($errorMessage, 'avanzar') !== false || strpos($errorMessage, 'avance') !== false) {
                $mensaje = $errorMessage;
            } elseif (strpos($errorMessage, 'ruta') !== false) {
                $mensaje = $errorMessage;
            } elseif (strpos($errorMessage, 'cantidad') !== false) {
                $mensaje = $errorMessage;
            } else {
                // Si el mensaje es descriptivo, usarlo; si no, usar uno genérico
                if (strlen($errorMessage) > 20 && strpos($errorMessage, 'SQLSTATE') === false) {
                    $mensaje = $errorMessage;
                } else {
                    $mensaje = 'Error al guardar la inspección. Por favor, verifique la información e intente nuevamente. Si el problema persiste, contacte al administrador del sistema.';
                }
            }
            
            return response()->json([
                'success' => false,
                'msg' => $mensaje
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
    
    /**
     * Método auxiliar para obtener el apellido paterno del usuario
     * (copiado de Mod01_ProduccionController)
     */
    private function getApellidoPaternoUsuario($apellido)
    {
        $preposiciones = ["DE", "LA", "LAS", "D", "LOS", "DEL"];
        if (in_array($apellido[0], $preposiciones) && count($apellido) > 1) {
            if (in_array($apellido[1], $preposiciones) && count($apellido) > 2) {
                return $apellido[0] . ' ' . $apellido[1] . ' ' . $apellido[2];
            } else {
                return $apellido[0] . ' ' . $apellido[1];
            }
        } else {
            return $apellido[0];
        }
    }
    
    /**
     * Muestra la vista principal de Evidencia de Clientes
     */
    public function index_evidencia_cliente()
    {
        $actividades = session('userActividades');
        $ultimo = count($actividades);
        
        return view('Mod_InspeccionProcesoController.index_evidencia_cliente', compact('actividades', 'ultimo'));
    }
    
    /**
     * AJAX: Buscar órdenes de producción finalizadas con inspecciones de estaciones 169 y 175
     */
    public function buscarEvidenciasCliente(Request $request)
    {
        try {
            $op = $request->input('op', '');
            $fechaDesde = $request->input('fecha_desde', '');
            $fechaHasta = $request->input('fecha_hasta', '');
            
            // Si se busca por OP específica, ignorar filtros de fecha
            if (!empty($op)) {
                // Buscar inspecciones de estación 175 aceptadas para la OP específica
                $evidencias = DB::select("
                    SELECT DISTINCT
                        IPR.IPR_op AS OP,
                        OWOR.ItemCode AS CodigoArticulo,
                        OITM.ItemName AS NombreArticulo,
                        OWOR.OriginNum AS Pedido,
                        ORDR.CardName AS Cliente,
                        OWOR.DueDate AS FechaFinalizacion,
                        OWOR.PlannedQty AS Cantidad
                    FROM Siz_InspeccionProceso IPR
                    INNER JOIN OWOR ON IPR.IPR_op = OWOR.DocNum
                    INNER JOIN OITM ON OWOR.ItemCode = OITM.ItemCode
                    LEFT JOIN ORDR ON OWOR.OriginNum = ORDR.DocNum
                    WHERE IPR.IPR_centroInspeccion = '175'
                        AND IPR.IPR_estado = 'ACEPTADO'
                        AND IPR.IPR_borrado = 'N'
                        AND IPR.IPR_op = ?
                        AND (OWOR.Status = 'C' OR OWOR.Status = 'L' OR OWOR.CmpltQty >= OWOR.PlannedQty)
                    ORDER BY OWOR.DueDate DESC, IPR.IPR_op DESC
                ", [$op]);
            } else {
                // Si no se envían fechas, usar los últimos 3 meses
                if (empty($fechaDesde)) {
                    $fechaDesde = date('Y-m-d', strtotime('-3 months'));
                }
                
                if (empty($fechaHasta)) {
                    $fechaHasta = date('Y-m-d');
                }
                
                // Buscar inspecciones de estación 175 aceptadas y luego complementar con información de SAP
                $evidencias = DB::select("
                    SELECT DISTINCT
                        IPR.IPR_op AS OP,
                        OWOR.ItemCode AS CodigoArticulo,
                        OITM.ItemName AS NombreArticulo,
                        OWOR.OriginNum AS Pedido,
                        ORDR.CardName AS Cliente,
                        OWOR.DueDate AS FechaFinalizacion,
                        OWOR.PlannedQty AS Cantidad
                    FROM Siz_InspeccionProceso IPR
                    INNER JOIN OWOR ON IPR.IPR_op = OWOR.DocNum
                    INNER JOIN OITM ON OWOR.ItemCode = OITM.ItemCode
                    LEFT JOIN ORDR ON OWOR.OriginNum = ORDR.DocNum
                    WHERE IPR.IPR_centroInspeccion = '175'
                        AND IPR.IPR_estado = 'ACEPTADO'
                        AND IPR.IPR_borrado = 'N'
                        AND OWOR.DueDate BETWEEN ? AND ?
                        AND (OWOR.Status = 'C' OR OWOR.Status = 'L' OR OWOR.CmpltQty >= OWOR.PlannedQty)
                    ORDER BY OWOR.DueDate DESC, IPR.IPR_op DESC
                ", [$fechaDesde, $fechaHasta]);
            }
            
            // Para cada OP, verificar si tiene videos
            foreach ($evidencias as $evidencia) {
                // Verificar si hay videos para esta OP en estaciones 169 o 175
                $tieneVideo = DB::select("
                    SELECT TOP 1 1 as tiene_video
                    FROM Siz_InspeccionProcesoImagen IPI
                    INNER JOIN Siz_InspeccionProceso IPR ON IPI.IPI_iprId = IPR.IPR_id
                    INNER JOIN Siz_Checklist CHK ON IPI.IPI_descripcion = CHK.CHK_id
                    WHERE IPR.IPR_op = ?
                        AND IPR.IPR_centroInspeccion IN ('169', '175')
                        AND IPR.IPR_estado = 'ACEPTADO'
                        AND IPR.IPR_borrado = 'N'
                        AND IPI.IPI_borrado = 'N'
                        AND CHK.CHK_descripcion LIKE 'Video%'
                ", [$evidencia->OP]);
                
                $evidencia->tiene_video = !empty($tieneVideo);
            }
            
            return response()->json($evidencias);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las evidencias: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generar PDF de evidencia de cliente para una OP
     */
    public function generarPdfEvidenciaCliente($op)
    {
        // Aumentar límites de tiempo y memoria para PDFs grandes
        set_time_limit(600); // 10 minutos
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');
        
        //\Log::info("INSPECCION_PROCESO_PDF: Iniciando generación de PDF para OP: {$op}");
        
        try {
            // Obtener información de la OP
            $ordenProduccion = DB::table('OWOR')
                ->select(
                    'OWOR.DocNum as OP',
                    'OWOR.ItemCode',
                    'OITM.ItemName as NombreArticulo',
                    'OWOR.OriginNum as Pedido',
                    'ORDR.CardName as Cliente',
                    'OWOR.DueDate as FechaFinalizacion',
                    'OWOR.PlannedQty as Cantidad',
                    'OWOR.CmpltQty as CantidadCompletada'
                )
                ->leftJoin('OITM', 'OWOR.ItemCode', '=', 'OITM.ItemCode')
                ->leftJoin('ORDR', 'OWOR.OriginNum', '=', 'ORDR.DocNum')
                ->where('OWOR.DocNum', $op)
                ->first();
            
            if (!$ordenProduccion) {
                abort(404, 'Orden de Producción no encontrada');
            }
            
            // Obtener inspecciones de las estaciones 169 y 175
            $inspecciones = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->whereIn('IPR_centroInspeccion', ['169', '175'])
                ->where('IPR_borrado', 'N')
                ->where('IPR_estado', 'ACEPTADO')
                ->orderBy('IPR_centroInspeccion')
                ->orderBy('IPR_fechaInspeccion')
                ->get();
            
            if ($inspecciones->isEmpty()) {
                abort(404, 'No se encontraron inspecciones para las estaciones 169 y 175');
            }
            
            // Para cada inspección, obtener detalles del checklist e imágenes
            foreach ($inspecciones as $inspeccion) {
                // Obtener detalles del checklist
                $detalles = Siz_InspeccionProcesoDetalle::on('siz')
                    ->join('Siz_Checklist', 'Siz_InspeccionProcesoDetalle.IPD_chkId', '=', 'Siz_Checklist.CHK_id')
                    ->select(
                        'Siz_InspeccionProcesoDetalle.*',
                        'Siz_Checklist.CHK_descripcion',
                        'Siz_Checklist.CHK_orden'
                    )
                    ->where('Siz_InspeccionProcesoDetalle.IPD_iprId', $inspeccion->IPR_id)
                    ->where('Siz_InspeccionProcesoDetalle.IPD_borrado', 'N')
                    ->where('Siz_Checklist.CHK_descripcion', 'LIKE', '%Foto%')
                    ->orderBy('Siz_Checklist.CHK_orden')
                    ->get();
                
                $inspeccion->detalles = $detalles;
                
                // Obtener imágenes agrupadas por CHK_id y convertir a base64
                $imagenes = Siz_InspeccionProcesoImagen::on('siz')
                    ->where('IPI_iprId', $inspeccion->IPR_id)
                    ->where('IPI_borrado', 'N')
                    ->get();
                
                // Crear un mapa de chkId a descripción para acceso rápido
                $chkDescripciones = [];
                foreach ($detalles as $detalle) {
                    $chkDescripciones[$detalle->IPD_chkId] = $detalle->CHK_descripcion;
                }
                
                $imagenesPorChk = [];
                $imagenCount = 0;
                foreach ($imagenes as $img) {
                    $chkId = $img->IPI_descripcion;
                    if (!isset($imagenesPorChk[$chkId])) {
                        $imagenesPorChk[$chkId] = [];
                    }
                    
                    // Convertir imagen a base64 (optimizado)
                    $imagenBase64 = '';
                    if (file_exists($img->IPI_ruta)) {
                        $mimeType = mime_content_type($img->IPI_ruta);
                        // Solo embebemos imágenes en el PDF (ignorar videos u otros archivos)
                        if ($mimeType && strpos($mimeType, 'image/') === 0) {
                            // Verificar tamaño del archivo antes de procesarlo
                            $fileSize = filesize($img->IPI_ruta);
                            if ($fileSize > 0 && $fileSize < 10485760) { // Máximo 10MB por imagen
                                try {
                                    $imagenData = file_get_contents($img->IPI_ruta);
                                    if ($imagenData !== false) {
                                        $imagenBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
                                        $imagenCount++;
                                    }
                                } catch (\Exception $e) {
                                    \Log::warning("INSPECCION_PROCESO_PDF: Error al leer imagen {$img->IPI_ruta}: " . $e->getMessage());
                                }
                            } else {
                                \Log::warning("INSPECCION_PROCESO_PDF: Imagen muy grande o inválida: {$img->IPI_ruta} (Tamaño: {$fileSize} bytes)");
                            }
                        }
                    }
                    
                    // Obtener descripción del checklist
                    $chkDescripcion = isset($chkDescripciones[$chkId]) 
                        ? $chkDescripciones[$chkId] 
                        : 'Item ' . $chkId;
                    
                    $imagenesPorChk[$chkId][] = [
                        'ruta' => $img->IPI_ruta,
                        'id' => $img->IPI_id,
                        'archivo' => basename($img->IPI_ruta),
                        'base64' => $imagenBase64,
                        'chk_descripcion' => $chkDescripcion
                    ];
                }
                
                \Log::info("INSPECCION_PROCESO_PDF: Procesadas {$imagenCount} imágenes para inspección ID: {$inspeccion->IPR_id}");
                $inspeccion->imagenes = $imagenesPorChk;
            }
            
            \Log::info("INSPECCION_PROCESO_PDF: Total de inspecciones procesadas: " . $inspecciones->count());
            
            // Obtener información de la empresa
            $empresa = DB::table('OADM')
                ->select('CompnyName as RazonSocial')
                ->first();
            
            // Crear header HTML similar al de rechazos
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Mod_RechazosController.pdfheader',
                [
                    'titulo' => 'Evidencia de Cliente - Inspección en Proceso',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'item' => 'OP: ' . $op
                ]
            )->render();
            
            $data = [
                'orden' => $ordenProduccion,
                'inspecciones' => $inspecciones,
                'empresa' => $empresa,
                'fechaImpresion' => date('d/m/Y H:i:s'),
                'imageCount' => 0,
                'chkDesc' => '',
                'titulo_pdf' => 'Evidencia de Cliente'
            ];
            
            \Log::info("INSPECCION_PROCESO_PDF: Iniciando generación de PDF con Snappy para OP: {$op}");
            
            $pdf = \SPDF::loadView('Mod_InspeccionProcesoController.pdf_evidencia_cliente', $data);
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('margin-top', '33mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');
            
            \Log::info("INSPECCION_PROCESO_PDF: PDF generado exitosamente para OP: {$op}");
            
            return $pdf->inline('Evidencia_Cliente_OP_' . $op . '_' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error("INSPECCION_PROCESO_PDF: Error al generar PDF para OP {$op}: " . $e->getMessage());
            \Log::error("INSPECCION_PROCESO_PDF: Stack trace: " . $e->getTraceAsString());
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Generar PDF de evidencia interno para una OP (todas las estaciones y todos los checklist)
     */
    public function generarPdfEvidenciaInterno($op)
    {
        set_time_limit(600); // 10 minutos
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');
        try {
            // Obtener información de la OP
            $ordenProduccion = DB::table('OWOR')
                ->select(
                    'OWOR.DocNum as OP',
                    'OWOR.ItemCode',
                    'OITM.ItemName as NombreArticulo',
                    'OWOR.OriginNum as Pedido',
                    'ORDR.CardName as Cliente',
                    'OWOR.DueDate as FechaFinalizacion',
                    'OWOR.PlannedQty as Cantidad',
                    'OWOR.CmpltQty as CantidadCompletada'
                )
                ->leftJoin('OITM', 'OWOR.ItemCode', '=', 'OITM.ItemCode')
                ->leftJoin('ORDR', 'OWOR.OriginNum', '=', 'ORDR.DocNum')
                ->where('OWOR.DocNum', $op)
                ->first();
            
            if (!$ordenProduccion) {
                abort(404, 'Orden de Producción no encontrada');
            }
            
            // Obtener TODAS las inspecciones (todas las estaciones) incluyendo ACEPTADO y RECHAZADO
            $inspecciones = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->where('IPR_borrado', 'N')
                ->whereIn('IPR_estado', ['ACEPTADO', 'RECHAZADO'])
                ->orderBy('IPR_centroInspeccion')
                ->orderBy('IPR_fechaInspeccion')
                ->get();
            
            if ($inspecciones->isEmpty()) {
                abort(404, 'No se encontraron inspecciones para esta OP');
            }
            
            // Para cada inspección, obtener detalles del checklist e imágenes
            foreach ($inspecciones as $inspeccion) {
                // Obtener TODOS los detalles del checklist (no solo los que contienen "Foto")
                $detalles = Siz_InspeccionProcesoDetalle::on('siz')
                    ->join('Siz_Checklist', 'Siz_InspeccionProcesoDetalle.IPD_chkId', '=', 'Siz_Checklist.CHK_id')
                    ->select(
                        'Siz_InspeccionProcesoDetalle.*',
                        'Siz_Checklist.CHK_descripcion',
                        'Siz_Checklist.CHK_orden'
                    )
                    ->where('Siz_InspeccionProcesoDetalle.IPD_iprId', $inspeccion->IPR_id)
                    ->where('Siz_InspeccionProcesoDetalle.IPD_borrado', 'N')
                    ->orderBy('Siz_Checklist.CHK_orden')
                    ->get();
                
                $inspeccion->detalles = $detalles;
                
                // Obtener TODAS las imágenes agrupadas por CHK_id y convertir a base64
                $imagenes = Siz_InspeccionProcesoImagen::on('siz')
                    ->where('IPI_iprId', $inspeccion->IPR_id)
                    ->where('IPI_borrado', 'N')
                    ->get();
                
                // Crear un mapa de chkId a descripción para acceso rápido
                $chkDescripciones = [];
                foreach ($detalles as $detalle) {
                    $chkDescripciones[$detalle->IPD_chkId] = $detalle->CHK_descripcion;
                }
                
                $imagenesPorChk = [];
                foreach ($imagenes as $img) {
                    $chkId = $img->IPI_descripcion;
                    if (!isset($imagenesPorChk[$chkId])) {
                        $imagenesPorChk[$chkId] = [];
                    }
                    
                    // Convertir imagen a base64
                    $imagenBase64 = '';
                    if (file_exists($img->IPI_ruta)) {
                        $imagenData = file_get_contents($img->IPI_ruta);
                        $mimeType = mime_content_type($img->IPI_ruta);
                        // Solo embebemos imágenes en el PDF (ignorar videos u otros archivos)
                        if ($mimeType && strpos($mimeType, 'image/') === 0) {
                            $imagenBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imagenData);
                        }
                    }
                    
                    // Obtener descripción del checklist
                    $chkDescripcion = isset($chkDescripciones[$chkId]) 
                        ? $chkDescripciones[$chkId] 
                        : 'Item ' . $chkId;
                    
                    $imagenesPorChk[$chkId][] = [
                        'ruta' => $img->IPI_ruta,
                        'id' => $img->IPI_id,
                        'archivo' => basename($img->IPI_ruta),
                        'base64' => $imagenBase64,
                        'chk_descripcion' => $chkDescripcion
                    ];
                }
                $inspeccion->imagenes = $imagenesPorChk;
            }
            
            // Obtener información de la empresa
            $empresa = DB::table('OADM')
                ->select('CompnyName as RazonSocial')
                ->first();
            
            // Crear header HTML similar al de rechazos
            $fechaImpresion = date("d-m-Y H:i:s");
            $headerHtml = view()->make(
                'Mod_RechazosController.pdfheader',
                [
                    'titulo' => 'Evidencia Interna - Inspección en Proceso',
                    'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
                    'item' => 'OP: ' . $op
                ]
            )->render();
            
            $data = [
                'orden' => $ordenProduccion,
                'inspecciones' => $inspecciones,
                'empresa' => $empresa,
                'fechaImpresion' => date('d/m/Y H:i:s'),
                'imageCount' => 0,
                'chkDesc' => '',
                'titulo_pdf' => 'Evidencia Interna'
            ];
            
            $pdf = \SPDF::loadView('Mod_InspeccionProcesoController.pdf_evidencia_interno', $data);
            $pdf->setOption('header-html', $headerHtml);
            $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
            $pdf->setOption('footer-left', 'SIZ');
            $pdf->setOption('margin-top', '33mm');
            $pdf->setOption('margin-left', '5mm');
            $pdf->setOption('margin-right', '5mm');
            $pdf->setOption('page-size', 'Letter');
            
            return $pdf->inline('Evidencia_Interna_OP_' . $op . '_' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            abort(500, 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * AJAX: Obtener videos de evidencia para una OP
     */
    public function obtenerVideosEvidenciaCliente($op)
    {
        try {
            // Obtener inspecciones de las estaciones 169 y 175
            $inspecciones = Siz_InspeccionProceso::on('siz')
                ->where('IPR_op', $op)
                ->whereIn('IPR_centroInspeccion', ['169', '175'])
                ->where('IPR_borrado', 'N')
                ->where('IPR_estado', 'ACEPTADO')
                ->orderBy('IPR_centroInspeccion')
                ->orderBy('IPR_fechaInspeccion')
                ->get();
            
            if ($inspecciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No se encontraron inspecciones para las estaciones 169 y 175'
                ], 404);
            }
            
            $videosPorInspeccion = [];
            
            // Para cada inspección, obtener videos (rubros que empiezan con "Video")
            foreach ($inspecciones as $inspeccion) {
                // Obtener detalles del checklist que son videos
                $detallesVideo = Siz_InspeccionProcesoDetalle::on('siz')
                    ->join('Siz_Checklist', 'Siz_InspeccionProcesoDetalle.IPD_chkId', '=', 'Siz_Checklist.CHK_id')
                    ->select(
                        'Siz_InspeccionProcesoDetalle.*',
                        'Siz_Checklist.CHK_descripcion',
                        'Siz_Checklist.CHK_orden'
                    )
                    ->where('Siz_InspeccionProcesoDetalle.IPD_iprId', $inspeccion->IPR_id)
                    ->where('Siz_InspeccionProcesoDetalle.IPD_borrado', 'N')
                    ->where('Siz_Checklist.CHK_descripcion', 'LIKE', 'Video%')
                    ->orderBy('Siz_Checklist.CHK_orden')
                    ->get();
                
                // Obtener videos agrupados por CHK_id
                $videos = Siz_InspeccionProcesoImagen::on('siz')
                    ->where('IPI_iprId', $inspeccion->IPR_id)
                    ->where('IPI_borrado', 'N')
                    ->get();
                
                $videosPorChk = [];
                foreach ($videos as $video) {
                    $chkId = $video->IPI_descripcion;
                    
                    // Verificar si este chkId corresponde a un rubro de video
                    $esVideo = false;
                    $chkDescripcion = '';
                    foreach ($detallesVideo as $detalle) {
                        if ($detalle->IPD_chkId == $chkId) {
                            $esVideo = true;
                            $chkDescripcion = $detalle->CHK_descripcion;
                            break;
                        }
                    }
                    
                    if ($esVideo) {
                        if (!isset($videosPorChk[$chkId])) {
                            $videosPorChk[$chkId] = [];
                        }
                        
                        $videosPorChk[$chkId][] = [
                            'ruta' => $video->IPI_ruta,
                            'id' => $video->IPI_id,
                            'archivo' => basename($video->IPI_ruta),
                            'chk_descripcion' => $chkDescripcion
                        ];
                    }
                }
                
                if (!empty($videosPorChk)) {
                    $videosPorInspeccion[] = [
                        'inspeccion_id' => $inspeccion->IPR_id,
                        'estacion' => $inspeccion->IPR_nombreCentro,
                        'estacion_id' => $inspeccion->IPR_centroInspeccion,
                        'fecha' => $inspeccion->IPR_fechaInspeccion,
                        'videos' => $videosPorChk
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'videos' => $videosPorInspeccion
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al obtener los videos: ' . $e->getMessage()
            ], 500);
        }
    }
}


