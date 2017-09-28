<?php

namespace App\Http\Controllers;

use App\User;
use App\OP;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
class Mod01_ProduccionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('Mod00_Administrador.admin');
    }

    public function estacionSiguiente( Request $request)
    {
       // echo OP::getEstacionSiguiente("81143");
      // dd(OP::getStatus("81143"));
      // dd(OP::getRuta("81143"));
    }

    public function allUsers(Request $request){
            $users = DB::select('select * from view_Plantilla_SIZ');
        $users = $this->arrayPaginator($users, $request);
        $stocksTable = Lava::DataTable();
        $stocksTable->addDateColumn('Day of Month')
            ->addNumberColumn('Projected')
            ->addNumberColumn('Official');

        // Random Data For Example
        for ($a = 1; $a < 30; $a++) {
            $stocksTable->addRow([
                '2015-10-' . $a, rand(800,1000), rand(800,1000)
            ]);
        }

        $beto = Lava::AreaChart('beto', $stocksTable, [
            'title' => 'Population Growth',
            'legend' => [
                'position' => 'in'
            ]
        ]);

        return view('Mod00_Administrador.usuarios', compact('users', 'beto'));
    }

    public function arrayPaginator($array, $request)
    {
        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }

    public function usuariosActivos( Request $request)
    {
        $users = User::name($request->get('name'))->where('jobTitle', '<>' , 'Z BAJA')->where('status', '1')
            ->orderBy('Ohem.dept, OHEM.jobTitle, ohem.firstname', 'asc')
           ;
        dd($users);
        return view('Mod00_Administrador.usuarios', compact('users'));
    }

    public function cambiopassword(){
        try {
            $password = Hash::make(Input::get('password'));
            DB::table('dbo.OHEM')
                ->where('U_EmpGiro',Input::get('userId') )
                ->update(['U_CP_Password' => $password]);
        } catch(\Exception $e) {
            return redirect()->back()->withErrors(array('msg' => $e->getMessage()));
        }
        $user = User::find(Input::get('userId'));
        Session::flash('mensaje', 'La contraseña de '.$user->firstName.' '.$user->lastName.' ha cambiado.');
        return redirect()->back();
    }

    public function traslados(Request $request)
    {
        $enviado = $request->input('send');
//dd($request->input('miusuario'));

        if (Auth::check()){
            $user = Auth::user();
            $actividades = $user->getTareas();
        if  ($enviado == 'send')
        {
            if (strlen($request->input('miusuario')) == null){
                $t_user =  Auth::user();
            }else{
                $t_user = User::find($request->input('miusuario'));
                if ($t_user == null){
                    return redirect()->back()->withErrors(array('message' => 'Error, el usuario no existe.'));
                }
            }


            if (Hash::check($request->input('pass'), $t_user->U_CP_Password)) {
                Session::flash('usertraslados', 1);
                return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades), 't_user' => $t_user]);
            }else{
                return redirect()->back()->withErrors(array('message' => 'Error de autenticación.'));
            }

        }else{

            Session::flash('usertraslados', false);
        }


            return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
        }else{
            return redirect()->route('auth/login');
        }

    }
    public function getOP($id)
    {
        $t_user = User::find($id);
        if ($t_user == null) {
            return redirect()->back()->withErrors(array('message' => 'Error, el usuario no existe.'));
        }

        $user = Auth::user();
        $actividades = $user->getTareas();
        Session::flash('usertraslados', 2);  //evita que salga el modal


        $Codes = OP::where('U_DocEntry', Input::get('op'))->get();
        if (count($Codes) > 0){
            $index = 0;
        foreach ($Codes as $code) {
            $index = $index + 1;
            $order = DB::table('OWOR')
                ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                ->select(DB::raw(OP::getEstacionActual($code->Code) . ' AS U_CT_ACT'), DB::raw(OP::getEstacionSiguiente($code->Code) . ' AS U_CT_SIG'), DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                    'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                    'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                ->where('@CP_OF.Code', $code->Code)->get();
            if ($index == 1) {
                $one = DB::table('OWOR')
                    ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                    ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                    ->select(DB::raw(OP::getEstacionActual($code->Code) . ' AS U_CT_ACT'), DB::raw(OP::getEstacionSiguiente($code->Code) . ' AS U_CT_SIG'), DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                        'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                        'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                    ->where('@CP_OF.Code', $code->Code)->get();
                foreach ($one as $o) {
                    $pedido = $o->OriginNum;
                }
            } else {

                $one = array_merge($one, $order); //$one->merge($order);
                //dd($one);
            }
        }
        //  $order = OP::find('492418');
        //return $one;


        // $actual = OP::getEstacionActual(Input::get('op'));
        // $siguiente = OP::getEstacionSiguiente(Input::get('op'));

        return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades), 't_user' => $t_user, 'ofs' => $one, 'op' => Input::get('op'), 'pedido' => $pedido]);

    }
        return redirect()->back()->withErrors(array('message' => 'La OP '.Input::get('op').' no existe.'));


    }

    public function avanzarOP(){
       return 'avanzar';
    }

}
