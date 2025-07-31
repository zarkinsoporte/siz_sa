<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Modelos\Siz_Incoming;
use App\Modelos\Siz_Checklist;
use App\Modelos\Siz_IncomDetalle;
use App\Modelos\Siz_PielClases;
use App\Modelos\Siz_IncomImagen;

class Mod_IncomingController extends Controller
{
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
        $materiales = DB::select('EXEC SIZ_Calidad_EntradaMaterial @NumeroEntrada = ?', [$numeroEntrada]);
        return response()->json($materiales);
    }

    // AJAX: Obtener checklist y respuestas previas para un material
    public function getChecklist(Request $request)
    {
        $inc_id = $request->input('inc_id');
        $checklist = \App\Modelos\Siz_Checklist::on('siz')->where('CHK_activo', 'S')->orderBy('CHK_orden')->get();
        $respuestas = \App\Modelos\Siz_IncomDetalle::on('siz')->where('IND_incId', $inc_id)->get();
        return response()->json(['checklist' => $checklist, 'respuestas' => $respuestas]);
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

            // Buscar o crear registro principal de inspección
            $incoming = \App\Modelos\Siz_Incoming::on('siz')->firstOrNew([
                'INC_docNum' => $material['NOTA_ENTRADA'],
                'INC_codMaterial' => $material['COD_ARTICULO']
            ]);
            $incoming->INC_fechaRecepcion = $material['FECHA_RECEPCION'];
            $incoming->INC_codProveedor = $material['CODIGO_PROVEEDOR'];
            $incoming->INC_nomProveedor = $material['NOMBRE_PROVEEDOR'];
            $incoming->INC_numFactura = $material['NUM_FACTURA'];
            $incoming->INC_nomMaterial = $material['MATERIAL'];
            $incoming->INC_unidadMedida = $material['UDM'];
            $incoming->INC_cantRecibida = $material['CANTIDAD'];
            $incoming->INC_cantAceptada = $material['CAN_INSPECCIONADA'] ?? 0;
            $incoming->INC_cantRechazada = $material['CAN_RECHAZADA'] ?? 0;
            $incoming->INC_fechaInspeccion = now();
            $incoming->INC_esPiel = ($material['GRUPO_MATERIAL'] == 113) ? 'S' : 'N';
            $incoming->save();

            // Guardar checklist
            if ($checklist) {
                foreach ($checklist as $chk_id => $item) {
                    $detalle = \App\Modelos\Siz_IncomDetalle::on('siz')->firstOrNew([
                        'IND_incId' => $incoming->INC_id,
                        'IND_chkId' => $chk_id
                    ]);
                    $detalle->IND_estado = $item['estado'] ?? 'A';
                    $detalle->IND_observacion = $item['obs'] ?? null;
                    $detalle->save();
                }
            }

            // Guardar clases de piel si aplica
            if ($incoming->INC_esPiel == 'S' && $piel) {
                $pielClases = \App\Modelos\Siz_PielClases::on('siz')->firstOrNew([
                    'PLC_incId' => $incoming->INC_id
                ]);
                $pielClases->PLC_claseA = $piel['claseA'] ?? 0;
                $pielClases->PLC_claseB = $piel['claseB'] ?? 0;
                $pielClases->PLC_claseC = $piel['claseC'] ?? 0;
                $pielClases->PLC_claseD = $piel['claseD'] ?? 0;
                $pielClases->save();
            }

            // Guardar imágenes de evidencia
            if ($imagenes) {
                foreach ($imagenes as $chk_id => $img) {
                    if ($img) {
                        $nombre = date('Ymd_His')."_{$incoming->INC_docNum}_{$incoming->INC_codMaterial}_{$chk_id}.".$img->getClientOriginalExtension();
                        $ruta = $img->storeAs('public/incoming', $nombre);
                        \App\Modelos\Siz_IncomImagen::on('siz')->create([
                            'IMG_incId' => $incoming->INC_id,
                            'IMG_ruta' => $ruta,
                            'IMG_descripcion' => $chk_id,
                            'IMG_cargadoPor' => auth()->user() ? auth()->user()->name : 'sistema',
                            'IMG_cargadoEn' => now(),
                            'IMG_borrado' => 'N'
                        ]);
                    }
                }
            }

            DB::connection('siz')->commit();
            return response()->json(['success' => true, 'msg' => 'Inspección guardada correctamente']);
        } catch (\Exception $e) {
            DB::connection('siz')->rollBack();
            return response()->json(['success' => false, 'msg' => 'Error al guardar: '.$e->getMessage()]);
        }
    }
} 