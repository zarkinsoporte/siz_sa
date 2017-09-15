<?php

namespace App\Http\Controllers;

use App\Grupo;
use App\Modelos\MOD01\MENU_ITEM;
use App\Modelos\MOD01\MODULOS_SIZ;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\User;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
use Illuminate\Support\Facades\Route;

use Datatables;
class Mod00_AdministradorController extends Controller
{
    /**
     * Create a new controller instance.
     *  https://datatables.yajrabox.com/starter
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
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

    public function plantilla( Request $request)
    {
        $users = User::plantilla();


        $stocksTable = Lava::DataTable();  // Lava::DataTable() if using Laravel

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

    public function allUsers(Request $request){
        $users = DB::select('select * from view_Plantilla_SIZ');
        $users = collect($users)->sortByDesc('dept');

        //$users = $this->arrayPaginator($users, $request);
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

        return view('Mod00_Administrador.usuarios', compact('users'));
    }

    public function cambiopassword(){

       // dd(Input::get('userId')." - ".Input::get('password'));
        try {
            $password = Hash::make(Input::get('password'));
            DB::table('dbo.OHEM')
                ->where('empID',Input::get('userId') )
                ->update(['U_CP_Password' => $password]);
        } catch(\Exception $e) {
            return redirect()->back()->withErrors(array('msg' => $e->getMessage()));
        }
        $user = User::where('empID',Input::get('userId'))->first();
        //dd($user);
        Session::flash('mensaje', 'La contraseña de '.$user->firstName.' '.$user->lastName.' ha cambiado.');
        return redirect()->back();
    }

    public function editUser($empid){


        $user = User::where('empID',$empid)->first();
dd($user);
        return view('Mod00_Administrador.editUser', compact('user'));
    }


    public function editgrupos($id_grupo){

 if ($id_grupo > 0){
     $grupos = DB::table('OHTY')
         ->where('typeID', '>', 0)->get();

     $nombre_grupo = $grupos[$id_grupo-1]->name;
     $modulos_grupo = MODULOS_GRUPO_SIZ::where('id_grupo', $id_grupo)
         ->leftJoin('MODULOS_SIZ', 'MODULOS_GRUPO_SIZ.id_modulo', '=', 'MODULOS_SIZ.id')
         ->select('MODULOS_GRUPO_SIZ.id_modulo', 'MODULOS_SIZ.descripcion', 'MODULOS_SIZ.name')
         ->groupBy('id_modulo', 'descripcion', 'name')
         ->get();
     $modulos = MODULOS_SIZ::all();
     return view('Mod00_Administrador.grupos', compact('grupos', 'modulos','modulos_grupo', 'id_grupo', 'nombre_grupo'));
 }else{
        return redirect()->back();
    }
    }

    public function createMenu($id_modulo){

        $modulo = new TAREA_MENU();
        $modulo->name = strtoupper (Input::get('name'));
        $modulo->id_menu_item  = $id_modulo;
        $modulo->save();
        return redirect()->back();
    }

    public function createModulo($id_grupo){

        $busqueda = MODULOS_GRUPO_SIZ::where('id_grupo', $id_grupo)
            ->where('id_modulo', Input::get('sel1'))
            ->first();

        if (count($busqueda)>0){
            return redirect()->back()->withErrors(array('message' => 'El Grupo ya tiene ese módulo.'));
        }else{
            $modulo = new MODULOS_GRUPO_SIZ();
            $modulo->id_grupo = $id_grupo;
            $modulo->id_modulo = Input::get('sel1');
            $modulo->save();
        }

        return redirect()->back();
    }

    public function createTarea($id_grupo){

        $id_menu = Input::get('sel1');
        $id_tarea = Input::get('sel2');

        $id_modulo = MENU_ITEM::find(Input::get('sel1'))->id_modulo;

      //  dd($id_grupo, $id_modulo);
        $tarea = MODULOS_GRUPO_SIZ::where('id_grupo',$id_grupo)
                                    ->where('id_modulo', $id_modulo)->first();

        if (count($tarea) == 1){

            if ($tarea->id_menu == null){
                $tarea->id_menu = $id_menu;
                $tarea->id_tarea = $id_tarea;
                $tarea->save();
            }else{
                $modulo = new MODULOS_GRUPO_SIZ();
                $modulo->id_grupo = $id_grupo;
                $modulo->id_modulo = $id_modulo;
                $modulo->id_menu = $id_menu;
                $modulo->id_tarea = $id_tarea;
                $modulo->save();
            }

        }elseif (count($tarea) > 1){
            $nueva_tarea = MODULOS_GRUPO_SIZ::where('id_grupo',$id_grupo)
                ->where('id_modulo', $id_modulo)
                ->where('id_menu', $id_modulo)
                ->where('id_tarea', $id_modulo)
                ->first();
            if ($nueva_tarea == null or count($nueva_tarea) == 0){
                $modulo = new MODULOS_GRUPO_SIZ();
                $modulo->id_grupo = $id_grupo;
                $modulo->id_modulo = $id_modulo;
                $modulo->id_menu = $id_menu;
                $modulo->id_tarea = $id_tarea;
                $modulo->save();
            }
        }

        return redirect()->back();
    }

    public function deleteTarea($id_modulog){
       $modulo =  MODULOS_GRUPO_SIZ::find($id_modulog);
       if ($modulo != null && count($modulo) > 0){
           if (count($modulo) == 1){
               $modulo->privilegio_tarea = "checked";
               $modulo->id_tarea = null;
               $modulo->id_menu = null;
               $modulo->save();
           }else{
               $modulo->delete();
           }
           return redirect()->back();
       }else{
           return view('Mod00_Administrador.admin')->withErrors(array('message' => 'No exite el modulo.'));
       }

    }

    public function confModulo($id_modulo){

        $grupos = DB::table('OHTY')
                ->where('typeID', '>', 0)->get();
        $primero = MODULOS_GRUPO_SIZ::where('id_modulo', $id_modulo)->first();
        if ($primero != null){
             $id_grupo = $primero->id_grupo;

             /*$menus = MODULOS_GRUPO_SIZ::where('MODULOS_GRUPO_SIZ.id_modulo',$id_modulo)
                 ->where('id_grupo', $id_grupo)
                 ->whereNotNull('id_menu')
                 ->whereNotNull('id_tarea')
                 ->leftjoin('MENU_ITEM_SIZ', 'MODULOS_GRUPO_SIZ.id_menu', '=', 'MENU_ITEM_SIZ.id')
                 ->leftjoin('TAREA_MENU_SIZ', 'MODULOS_GRUPO_SIZ.id_tarea', '=', 'TAREA_MENU_SIZ.id')
                 ->select('MODULOS_GRUPO_SIZ.*', 'MENU_ITEM_SIZ.name as menu', 'TAREA_MENU_SIZ.name as tarea')
                 ->get();*/

             $grupo = Grupo::find($id_grupo);
             $modulo = MODULOS_SIZ::find($id_modulo);

             $menus_existentes = MENU_ITEM::where('id_modulo', $id_modulo)
                 ->get();

             return view('Mod00_Administrador.createMenu', compact('id_grupo','id_modulo','grupos','menus_existentes','grupo', 'modulo'));
        }else{
            return view('Mod00_Administrador.admin')->withErrors(array('message' => 'El modulo no existe.'));
        }

    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function anyData(Request $request)
    {
        $primero = MODULOS_GRUPO_SIZ::where('id_modulo', $request->get('id'))->first();
        $id_grupo = $primero->id_grupo;
        $menus = MODULOS_GRUPO_SIZ::where('MODULOS_GRUPO_SIZ.id_modulo',$request->get('id'))
            ->where('id_grupo', $id_grupo)
            ->whereNotNull('id_menu')
            ->whereNotNull('id_tarea')
            ->leftjoin('MENU_ITEM_SIZ', 'MODULOS_GRUPO_SIZ.id_menu', '=', 'MENU_ITEM_SIZ.id')
            ->leftjoin('TAREA_MENU_SIZ', 'MODULOS_GRUPO_SIZ.id_tarea', '=', 'TAREA_MENU_SIZ.id')
            ->select('MODULOS_GRUPO_SIZ.*', 'MENU_ITEM_SIZ.name as menu', 'TAREA_MENU_SIZ.name as tarea')
            ->get();

       return Datatables::of($menus)
           ->addColumn('action', function ($menu) {
               return  '<a href="quitar/'.$menu->id.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Quitar</a>';
           }

           )
           ->addColumn('priv', function ($menu) {

               return  '<input  class="toggle" type="checkbox" value="'.$menu->id.'" '.$menu->privilegio_tarea.' />';
           }

           )
           ->make(true);
    }

    public function updateprivilegio()
    {
        $mg = MODULOS_GRUPO_SIZ::find(Input::get('option'));
        $mg->privilegio_tarea = Input::get('check');
        $mg->save();
    }

    public function nuevatarea(){

       if (Input::get('radio1')=='1'){
         $mimenu = Input::get('sel3');

       }else{
         $mimenu = strtoupper(Input::get('menu2'));

         if (empty($mimenu)){
             return redirect()->back()->withErrors(array('message' => 'El nombre del modulo no es válido.'));
         }else{
            $menuexiste = MENU_ITEM::where('name',$mimenu)
            ->where('id_modulo', Input::get('modulo'))->first();

            if (count($menuexiste)== 1 && $menuexiste!= null){
                $mimenu = $menuexiste->id;
            }else{
                $modulo = new MENU_ITEM();
                $id = $modulo->id;
                $modulo->name = $mimenu;
                $modulo->id_modulo = Input::get('modulo');
                $modulo->save();

                $mimenu = $id;
            }

         }
       }
        $nombretarea = strtoupper(Input::get('name'));
        $tareaexiste = TAREA_MENU::where('name', $nombretarea)
            ->where('id_menu_item', $mimenu)->get();

        if (count($tareaexiste)== 1 && $tareaexiste!= null){
            return redirect()->back()->withErrors(array('message' => 'La tarea '.$nombretarea.' ya existe.'));
        }else{
            $modulo = new TAREA_MENU();
            $modulo->name = $nombretarea;
            $modulo->id_menu_item = $mimenu;
            $modulo->save();
            Session::flash('mensaje', 'La tarea '.$nombretarea.' se creo, pero no ha sido agregada a este grupo.');
            return redirect()->back();
        }

    }
}
