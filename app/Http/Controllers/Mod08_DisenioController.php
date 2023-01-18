<?php
namespace App\Http\Controllers;

use DB;
use App;
use Auth;
use App\User;
//use Session;
use Datatables;
use App\ACABADO;
//excel
//use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
//use Illuminate\Support\Facades\Validator;
use App\Jobs\LdmUpdate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Cache;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod08_DisenioController extends Controller
{
    public function __construct()
    {
        // check if session expired for ajax request
       // $this->middleware('ajax-session-expired');

        // check if user is autenticated for non-ajax request
        $this->middleware('auth');
    }
    public function mtto_acabados_PDF(){

        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $acabados = ACABADO::where('ACA_Eliminado', 0)
            ->whereNotNull('CODIDATO')           
            ->orderBy('DESCDATO', 'asc')
            ->orderBy('inval01_descripcion2', 'asc')
            ->get();
        //dd($acabados[0]);
        //$codigo_acabado = array_column($acabados, 'CODIDATO');
        //array_multisort($codigo_acabado, SORT_ASC, $acabados);
        $fechaActualizacion = ACABADO::where('ACA_Eliminado', 0)->max('FechaMov');
        $fechaImpresion = date("d-m-Y H:i:s"); 
        $headerHtml = view()->make('Mod08_Disenio.mtto_acabados_pdfheader', 
        [
            'titulo' => 'Relación de Complementos según Acabado.',
            'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
            'fechaActualizado' => 'Fecha Actualización: ' . $fechaActualizacion
        ])->render();
        
        $pdf = \SPDF::loadView('Mod08_Disenio.mtto_acabados_pdf', compact('acabados'));
        //$pdf->setOption('header-left', 'Fecha Actualización: '. $fechaActualizacion);
        //$pdf->setOption('header-right', 'Fecha de Impresión: '. $fechaImpresion);
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
        $pdf->setOption('footer-left', 'SIZ');
        $pdf->setOption('margin-top', '33mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('page-size', 'Letter');
        
        return $pdf->inline('SIZ_MantenimientoAcabados.pdf');
        
    }
    public function mtto_acabados_index(){
        $user = Auth::user();
        $actividades = $user->getTareas();
        $acabados = ACABADO::select('CODIDATO', 'DESCDATO')
        ->where('ACA_Eliminado', 0)
        ->groupBy('CODIDATO', 'DESCDATO')
        ->orderBy('DESCDATO', 'desc')->get();
        $recuperar_acabados = ACABADO::select('CODIDATO', 'DESCDATO')
        ->where('ACA_Eliminado', 1)
        ->groupBy('CODIDATO', 'DESCDATO')
        ->orderBy('DESCDATO', 'desc')->get();
        $oitms = DB::select("SELECT ItemCode, ItemName,
            CASE WHEN ItemName like '%NEGRO%' THEN 1 ELSE 0 END AS negro
            FROM OITM 
                WHERE PrchseItem = 'Y' AND InvntItem = 'Y' 
                AND U_TipoMat <> 'PT' AND U_TipoMat IS NOT NULL
                AND OITM.frozenFor = 'N'
                AND U_GrupoPlanea in (
                '18', --hilos
                '19', --cierres
                '9', --piel
                '10', --tela complemento
                '11' --tela y viniles
                )
                ORDER BY negro, ItemName asc
        ");
        $oitms_negro = array_where($oitms, function ($key, $value) {
            return $value->negro == 1;
        });
        $oitms_otros = array_where($oitms, function ($key, $value) {
            return $value->negro == 0;
        });
        $codigos_acabados = DB::select(
            "SELECT SUBSTRING(ItemCode , charindex('-', ItemCode, 6) + 1, LEN(ItemCode) - charindex('-', ItemCode, 6) + 1) as Acabado
            FROM OITM 
            LEFT JOIN (
                SELECT CODIDATO from SIZ_Acabados
                where ACA_Eliminado = 0
                group by CODIDATO
            ) AS ACABADOS on ACABADOS.CODIDATO = LTRIM(RTRIM(SUBSTRING(ItemCode , charindex('-', ItemCode, 6) + 1, LEN(ItemCode) - charindex('-', ItemCode, 6) + 1)))
            WHERE 
                CODIDATO IS NULL
                AND InvntItem = 'Y' 
                AND U_TipoMat = 'PT' AND U_TipoMat IS NOT NULL
                --AND OITM.frozenFor = 'N'
                AND ItemName not like '%NEGRO%'
                AND U_IsModel = 'N'
                AND ItemCode like '%-%'
                GROUP BY SUBSTRING(ItemCode , charindex('-', ItemCode, 6) + 1, LEN(ItemCode) - charindex('-', ItemCode, 6) + 1)
                ORDER BY SUBSTRING(ItemCode , charindex('-', ItemCode, 6) + 1, LEN(ItemCode) - charindex('-', ItemCode, 6) + 1) asc"
        );

        //dd($oitms_negro, $oitms_otros);
        $data = array(
            'actividades' => $actividades,
            'acabados' => $acabados,
            'recuperar_acabados' => $recuperar_acabados,
            'oitms_negro' => $oitms_negro,
            'oitms_otros' => $oitms_otros,
            'codigos_acabados' => $codigos_acabados,
            'ultimo' => count($actividades)
        );
        return view('Mod08_Disenio.mtto_acabados_index', $data);
    }
    public function datatables_acabados(Request $request)
    {
        //materiales del acabado
        $data = ACABADO::where('CODIDATO', $request->get('acabado_code'))
        ->where('ACA_Eliminado', 0)
        ->get();
        return Datatables::of($data)->make(true);
    }
    public function eliminar_acabado(Request $request)
    {
        ACABADO::where('CODIDATO', $request->get('acabado_code'))
        ->update(array('ACA_Eliminado' => 1, 'FechaMov'=> date('Ymd'), 'idUser' =>  Auth::user()->U_EmpGiro));        
    }
    public function dbrecuperar_acabado(Request $request)
    {
        ACABADO::where('CODIDATO', $request->get('acabado_code'))
        ->update(array('DESCDATO' => strtoupper( trim($request->get('acabado_descr')) ), 'ACA_Eliminado' => 0, 'FechaMov'=> date('Ymd'), 'idUser' =>  Auth::user()->U_EmpGiro));        
    }
    public function eliminar_material_acabado(Request $request)
    {
        $material = ACABADO::find($request->get('id_mat'));
        $material->ACA_Eliminado = 1;
        $material->FechaMov = date('Ymd');
        $material->idUser = Auth::user()->U_EmpGiro;
        $material->save();
    }
    public function guarda_material_acabado(Request $request)
    {
        $id_mat = $request->get('id_mat');
        $codigo_acabado = $request->get('codigo_acabado');
        $codigo_acabado = array_map('trim', explode('-', $codigo_acabado));        
        $codigo_a = $request->get('codigo_a');
        $codigo_a = array_map('trim', explode('-', $codigo_a));
        $codigo_b = $request->get('codigo_b');
        $codigo_b = array_map('trim', explode('-', $codigo_b));

        if ($id_mat == '') {
            # code...INSERT
            $material = new ACABADO;           
        } else {
            # code...UPDATE
            $material = ACABADO::find($id_mat);            
        }
        $material->Arti = trim($codigo_a[0]);
        $material->inval01_al0102 = trim($codigo_a[1]);
        $material->CODIDATO = trim($codigo_acabado[0]);
        $material->DESCDATO = trim($codigo_acabado[1]);
        $material->Surtir = trim($codigo_b[0]);
        $material->inval01_descripcion2 = trim($codigo_b[1]);
        $material->FechaMov = date('Ymd');
        $material->idUser = Auth::user()->U_EmpGiro;
        $material->save();
        return compact('material');
    }

    public function ldmUpdate(){
        $user = Auth::user()->U_EmpGiro;
        $var = $this->dispatch((new LdmUpdate('19732','383124-ESTRUCTURA', 8, false, $user))->onQueue('LdmUpdate'));
        
        return $var;
    }

    public function mtto_ldm()
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $data = array(
            'actividades' => $actividades,
            'tipomat' => ['CA', 'MP', 'SP', 'RF', 'HB'],
            'articulos' => [],
            'ultimo' => count($actividades),                
        );
        return view('Mod08_Disenio.mtto_ldm', $data);
       
    }

    public function datatables_mtto_ldm(Request $request)
    {

        $codigo = $request->get('codigo');
        
            $consulta = DB::select("SELECT T0.[Father] codigo_origen ,T2.[ItemName] as descripcion_origen, 
            T0.[Code] codigo, 
            T1.[ItemName] descripcion, T0.[Quantity] cantidad,T1.[invntryuom] as um,T0.[Price] precio, T0.[Quantity]*T0.[Price] as consumo 
            FROM ITT1 T0 
            INNER JOIN OITM T1 ON T0.Code = T1.ItemCode
            left join OITM T2 on  T0.father = T2.ItemCode 
            WHERE T0.[Code] = ?", [$codigo]);
        
        //dd(DB::getQueryLog());
        return response()->json(array('arts' => $consulta));
    }

    public function mtto_ldm_combobox_articulos(Request $request)
    {
        //DB::connection()->enableQueryLog();
        $q = "SELECT OITM.ItemCode, OITM.ItemCode +' - '+ OITM.ItemName AS descr
            from OITM  
            WHERE ValidFor = 'Y' and FrozenFor = 'N' and OITM.U_TipoMat = ?
            GROUP BY OITM.ItemCode, OITM.ItemName
            order by OITM.ItemName";
        $oitms = DB::select($q, [$request->get('tipomat')]);
        $oitms2 = [];
        if ($request->get('carga_combo_modal')) {
            $q = "SELECT OITM.ItemCode, OITM.ItemCode +' - '+ OITM.ItemName AS descr
                from OITM  
                WHERE ValidFor = 'Y' and FrozenFor = 'N'
                GROUP BY OITM.ItemCode, OITM.ItemName
                order by OITM.ItemName";
            $oitms2 = DB::select($q);
        }
        //dd(DB::getQueryLog());
        return compact('oitms', 'oitms2');
    }

    public function actualizarCantidad_mtto_ldm(Request $request){
        $articulos = $request->input('articulos');
        $input_update = $request->input('input_update');
        $input_modificacion = $request->input('input_modificacion');
        $input_factor = $request->input('input_factor');
        $option = $request->input('option');
        $codigo = $request->input('codigo');
        $codigo_cambio = $request->input('codigo_cambio');
        $delete_option = false;
        $cambio_option = false;
        $articulos = explode(',', $articulos);            
        $mensajeErr= '-';
          
        foreach ($articulos as $key => $articulo) {
            $pos = explode('&',$articulo);
            
            $codigo_origen = $pos[0];
            $cantidad = $pos[1];

            if ($option == '1') { 
                $cantidad = $input_update;
            } else if ($option == '2') { 
                $cantidad += $cantidad * ( $input_modificacion / 100 );
            } else if ($option == '3') {
                $delete_option = true;
            } else if ($option == '4') {
                $cantidad = $cantidad * ($input_factor);
                $cambio_option = true;                
            }
            $user = Auth::user()->U_EmpGiro;
            $this->dispatch(new LdmUpdate($codigo, $codigo_origen, $cantidad, $delete_option, $cambio_option, $codigo_cambio, $user));
            
        }
        return compact('mensajeErr');
    }
}