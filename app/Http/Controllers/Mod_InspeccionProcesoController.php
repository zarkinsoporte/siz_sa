<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        
        // Obtener centros de inspección desde la tabla de rutas de calidad
        $centrosInspeccion = DB::table('@PL_RUTAS')
            ->select('Code as id', 'Name as nombre')
            ->where('U_Estatus', 'A')
            ->where('U_Calidad', 'S') // Solo rutas de calidad
            ->orderBy('Name')
            ->get();
        
        return view('Mod_InspeccionProcesoController.index_inspeccion_en_proceso', compact('actividades', 'ultimo', 'centrosInspeccion'));
    }
    
    /**
     * AJAX: Buscar materiales por OP y centro de inspección
     */
    public function buscarInspeccionesEnProceso(Request $request)
    {
        try {
            $op = $request->input('op');
            $centroInspeccion = $request->input('centro_inspeccion');
            
            if (!$op || !$centroInspeccion) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Debe proporcionar la OP y el Centro de Inspección'
                ], 400);
            }
            
            // TODO: Aquí irá la lógica para obtener los datos de la OP y materiales en proceso
            // Por ahora retornamos una estructura de ejemplo
            
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
                    'OWOR.U_Ruta as Ruta'
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
            
            // 2. Verificar que el centro de inspección esté en la ruta de la OP
            $rutaArray = explode(',', str_replace(' ', '', $ordenProduccion->Ruta));
            if (!in_array($centroInspeccion, $rutaArray)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'El Centro de Inspección seleccionado no está en la ruta de esta OP'
                ], 400);
            }
            
            // 3. Obtener el nombre del centro de inspección
            $nombreCentro = DB::table('@PL_RUTAS')
                ->where('Code', $centroInspeccion)
                ->value('Name');
            
            // 4. TODO: Aquí se obtendría el checklist específico para este centro de inspección
            // y las inspecciones previas si existen
            
            return response()->json([
                'success' => true,
                'op' => $ordenProduccion,
                'centro_inspeccion' => [
                    'id' => $centroInspeccion,
                    'nombre' => $nombreCentro
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error al buscar la OP: ' . $e->getMessage()
            ], 500);
        }
    }
}

