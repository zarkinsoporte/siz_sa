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
        $data = array(
            'actividades' => $actividades,
            'ultimo' => count($actividades)
        );
        return view('Mod08_Disenio.mtto_acabados_index', $data);
    }
}