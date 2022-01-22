<?php
namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
use Carbon\Carbon;
//excel
use Maatwebsite\Excel\Facades\Excel;
//DOMPDF
use Dompdf\Dompdf;
use App;
//use Pdf;
//Fin DOMPDF
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Datatables;
use App\ACABADO;
class Mod08_DisenioController extends Controller
{
    public function __construct()
    {
        // check if session expired for ajax request
       // $this->middleware('ajax-session-expired');

        // check if user is autenticated for non-ajax request
        $this->middleware('auth');
    }

    public function mtto_acabados_index(){
        $user = Auth::user();
        $actividades = $user->getTareas();
        $acabados = ACABADO::select('CODIDATO', 'DESCDATO')
        ->where('ACA_Eliminado', 0)
        ->groupBy('CODIDATO', 'DESCDATO')
        ->orderBy('DESCDATO', 'desc')->get();
        $oitms = DB::select('SELECT ItemCode, ItemName FROM OITM WHERE PrchseItem = \'Y\' AND InvntItem = \'Y\' AND U_TipoMat <> \'PT\' AND U_TipoMat IS NOT NULL
        AND OITM.frozenFor = \'N\' ORDER BY ItemName asc');
        $data = array(
            'actividades' => $actividades,
            'acabados' => $acabados,
            'oitms' => $oitms,
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
}