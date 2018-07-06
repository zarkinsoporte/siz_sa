<?php
namespace App\Http\Controllers;
ini_set('max_execution_time', 180);

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
//DOMPDF
use Dompdf\Dompdf;
use App;
//use Pdf;
//Fin DOMPDF
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($user = Auth::user()->U_EmpGiro == 246){
            return view('Mod00_Administrador.admin');
        }else{

        }

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

    public function showUsers($depto)
    {

        return view('Mod00_Administrador.usuariosDepto', compact('depto'));
    }
    public function DataShowUsers(Request $request)
    {
        $users = DB::table('Siz_View_Plantilla_Personal')
            ->where('Depto', 'like', '%'.$request->get('depto').'%');

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                return  '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#mymodal" data-whatever="'.$user->U_EmpGiro.'">
                                        <i class="fa fa-unlock" aria-hidden="true"></i>
                                    </button>';
            }
            )
            ->make(true);


    }

    public function allUsers(Request $request){
        $users = DB::select('SELECT depto, COUNT(*) as c  FROM Siz_View_Plantilla_Personal GROUP BY Depto');


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


        $finalarray = [];
        foreach ($users as $user)
        {

            $miarray = DB::select('SELECT jobTitle, COUNT(*) as c FROM Siz_View_Plantilla_Personal where Depto like \'%'.$user->depto.'%\' GROUP BY jobTitle');
            $finalarray[$user->depto] = $miarray;
        }

        return view('Mod00_Administrador.usuarios', compact('finalarray', 'beto'));
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
                ->where('U_EmpGiro',Input::get('userId') )
                ->update(['U_CP_Password' => $password]);
        } catch(\Exception $e) {
            return redirect()->back()->withErrors(array('msg' => $e->getMessage()));
        }
        $user = User::where('U_EmpGiro',Input::get('userId'))->first();
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
         ->leftJoin('Siz_Modulo', 'Siz_Modulos_Grupo.id_modulo', '=', 'Siz_Modulo.id')
         ->select('Siz_Modulos_Grupo.id_modulo', 'Siz_Modulos_Grupo.id', 'Siz_Modulo.descripcion', 'Siz_Modulo.name')
         ->groupBy('Siz_Modulos_Grupo.id', 'id_modulo', 'descripcion', 'name')
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
                $nueva_tarea = MODULOS_GRUPO_SIZ::where('id_grupo',$id_grupo)
                    ->where('id_modulo', $id_modulo)
                    ->where('id_menu', $id_menu)
                    ->where('id_tarea', $id_tarea)
                    ->first();
                  //dd(count($tarea));
                if  (count($nueva_tarea) == 1){
                    return redirect()->back()->withErrors(array('message' => 'La tarea ya existe.'));
                }else{
                    $modulo = new MODULOS_GRUPO_SIZ();
                    $modulo->id_grupo = $id_grupo;
                    $modulo->id_modulo = $id_modulo;
                    $modulo->id_menu = $id_menu;
                    $modulo->id_tarea = $id_tarea;
                    $modulo->save();
                }
                }


        }elseif (count($tarea) > 1){
            $nueva_tarea = MODULOS_GRUPO_SIZ::where('id_grupo',$id_grupo)
                ->where('id_modulo', $id_modulo)
                ->where('id_menu', $id_menu)
                ->where('id_tarea', $id_tarea)
                ->first();
           // dd(count($tarea));
            if  (count($nueva_tarea) == 1){
                return redirect()->back()->withErrors(array('message' => 'La tarea ya existe.'));
            }else{
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

    public function confModulo($id){

        $grupos = DB::table('OHTY')
                ->where('typeID', '>', 0)->get();
        $primero = MODULOS_GRUPO_SIZ::where('id', $id)->first();
        if ($primero != null){
             $id_grupo = $primero->id_grupo;

             /*$menus = MODULOS_GRUPO_SIZ::where('Siz_Modulos_Grupo.id_modulo',$id_modulo)
                 ->where('id_grupo', $id_grupo)
                 ->whereNotNull('id_menu')
                 ->whereNotNull('id_tarea')
                 ->leftjoin('Siz_Menu_Item', 'Siz_Modulos_Grupo.id_menu', '=', 'Siz_Menu_Item.id')
                 ->leftjoin('Siz_Tarea_menu', 'Siz_Modulos_Grupo.id_tarea', '=', 'Siz_Tarea_menu.id')
                 ->select('Siz_Modulos_Grupo.*', 'Siz_Menu_Item.name as menu', 'Siz_Tarea_menu.name as tarea')
                 ->get();*/

             $grupo = Grupo::find($id_grupo);
             $id_modulo=$primero->id_modulo;
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
        
        $menus = MODULOS_GRUPO_SIZ::where('Siz_Modulos_Grupo.id_modulo',$request->get('id_modulo'))
            ->where('Siz_Modulos_Grupo.id_grupo', $request->get('id_grupo'))
            ->whereNotNull('id_menu')
            ->whereNotNull('id_tarea')
            ->leftjoin('Siz_Menu_Item', 'Siz_Modulos_Grupo.id_menu', '=', 'Siz_Menu_Item.id')
            ->leftjoin('Siz_Tarea_menu', 'Siz_Modulos_Grupo.id_tarea', '=', 'Siz_Tarea_menu.id')
            ->select('Siz_Modulos_Grupo.*', 'Siz_Menu_Item.name as menu', 'Siz_Tarea_menu.name as tarea')
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

    public function deleteModulo($id){
        $busqueda = MODULOS_GRUPO_SIZ::
        where('id', $id)
        ->first();
//dd($busqueda);
    if (count($busqueda)==1){
        $busqueda->delete();
        Session::flash('mensaje', 'Módulo Eliminado!!');
        return redirect()->back();
    }else{
        return redirect()->back()->withErrors(array('message' => 'El Modulo no se encuentra.'));        
    }

    }

    public function inventario()
    {
        //Realizamos la consulta nuevamente
        $inventario = DB::table('Siz_Inventario')
            ->join('Siz_Monitores', 'Siz_Inventario.monitor', '=', 'Siz_Monitores.id')
            ->select('Siz_Inventario.id as id_inv', 'Siz_Inventario.*', 'Siz_Monitores.id as id_mon', 'Siz_Monitores.*')
            ->where('Siz_Inventario.activo', '=',1)
            ->orderBy('numero_equipo')
            ->get();
        $monitores  = DB::table('Siz_Monitores')->get();

        foreach ($inventario as $inv_campo) {
                    $nombre_usuario = explode(".", $inv_campo->correo);
                    $nombre   = strtoupper($nombre_usuario[0]);
                    $apellido = explode("@", $apellido = $nombre_usuario[1]);
                    $apellido = strtoupper($apellido[0]);
                   
                    if($nombre_usuario[1]!="com" && $inv_campo->nombre_usuario==NULL){    
                        $act_usr = DB::table('Siz_Inventario')
                            ->where('id', $inv_campo->id_inv)
                            ->update(['nombre_usuario' => $nombre." ".$apellido ]);
                        //dd($nombre_usuario[0]);
                    }

        }

        return view('Mod00_Administrador.inventario', compact('inventario', 'monitores'));    
    }

    public function mark_obs($id)
    {
        //Actualizamos el valor en la DB
        $act_inv = DB::table('Siz_Inventario')
            ->where("id", "=", "$id")
            ->update([
                'activo' => '0'
            ]);
        //dd($act_inv);     
        //Realizamos la consulta nuevamente
        //$this->inventario();
        return redirect('admin/inventario');
    }

    public function mark_rest($id)
    {
        //Actualizamos el valor en la DB
        $act_inv = DB::table('Siz_Inventario')
            ->where("id", "=", "$id")
            ->update([
                'activo' => '1'
            ]);
        //dd($act_inv);     
        //Realizamos la consulta nuevamente
        //$this->inventario();
        return redirect('admin/inventarioObsoleto');
    }

    public function inventarioObsoleto( Request $request)
    {
        //Realizamos la consulta buscando donde activo sea igual a 0
        $inventario = DB::table('Siz_Inventario')
            ->join('Siz_Monitores', 'Siz_Inventario.monitor', '=', 'Siz_Monitores.id')
            ->select('Siz_Inventario.id as id_inv', 'Siz_Inventario.*', 'Siz_Monitores.id as id_mon', 'Siz_Monitores.*')
            ->where('Siz_Inventario.activo', '=',0)
            ->get();
        $monitores  = DB::table('Siz_Monitores')->get();
        //dd($inventario);
        return view('Mod00_Administrador.inventarioObsoleto', compact('inventario', 'monitores'));    
    }   

    public function monitores( Request $request)
    {
        $monitores = DB::table('Siz_Monitores')->orderBy('id', 'ASC')->get();
        return view('Mod00_Administrador.monitores')->with('monitores', $monitores);
    }

    public function altaInventario( Request $request)
    {
        //$monitores = DB::table('Siz_Monitores')->get();
        $monitores = DB::select( DB::raw("SELECT Siz_Monitores.id AS id_mon, nombre_monitor FROM Siz_Monitores LEFT JOIN Siz_Inventario ON Siz_Monitores.id = Siz_Inventario.monitor WHERE Siz_Inventario.monitor IS NULL AND Siz_Monitores.id !='1'") );
        return view('Mod00_Administrador.altaInventario', compact('monitores'));   
    }

    public function altaMonitor( Request $request)
    {
        //$users = User::plantilla();
        return view('Mod00_Administrador.altaMonitor');
    }

    public function mod_mon($id, $mensaje)
    {
        //$users = User::plantilla();

        $monitor = DB::table('Siz_Monitores')
        ->select('Siz_Monitores.*')
        ->where('id', '=',$id)
        ->first();
        return view('Mod00_Administrador.modMonitor', compact('monitor', 'mensaje'));   
    }

    public function mod_mon2( Request $request)
    {
        //$users = User::plantilla();
        $id_monitor = $request->input('id_monitor');
        $act_mon = DB::table('Siz_Monitores')
        ->where("id", "=", "$id_monitor")
        ->update([
            'nombre_monitor' => $request->input('nombre_monitor')
        ]);
        $mensaje="Monitor Actualizado";
        return $this->mod_mon($id_monitor, $mensaje);   
    }

    public function altaMonitor2(Request $request)
    {
        //Insertamos el monitor en la DB
        DB::table('Siz_Monitores')->insert(
            [
             'nombre_monitor' => $request->input('nombre_monitor')
            ]
        );
        //Realizamos la consulta nuevamente
        $monitores = DB::table('Siz_Monitores')->orderBy('id', 'ASC')->get();
        //Llamamos a la vista para mostrar su contendio
        return view('Mod00_Administrador.monitores')->with('monitores', $monitores);
    }

    public function altaInventario2(Request $request)
    {
        $tiempo_vida = $request->input('tiempo_vida');    
        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( "+$tiempo_vida year" , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        //Insertamos el monitor en la DB
        DB::table('Siz_Inventario')->insert(
            [
             'nombre_equipo' => $request->input('nombre_equipo'),
             'nombre_usuario' => $request->input('nombre_usuario'),
             'correo' => $request->input('correo'), 
             'numero_equipo' => $request->input('numero_equipo'),
             'tipo_equipo' => $request->input('tipo_equipo'),
             'fecha_alta' => date("Y-m-d"),
             'fecha_baja' => $nuevafecha,
             'monitor' => $request->input('monitor'),
             'Fecha_mantenimiento' => $request->input('mantenimiento')
            ]
        );
        //Realizamos la consulta nuevamente
        return redirect('admin/inventario');
    }

    public function generarPdf($id)
    {
        //$pdf = App::make('dompdf.wrapper');
        $inventario = DB::table('Siz_Inventario')
        ->join('Siz_Monitores', 'Siz_Inventario.monitor', '=', 'Siz_Monitores.id')
        ->select('Siz_Inventario.id as id_inv', 'Siz_Inventario.*', 'Siz_Monitores.id as id_mon', 'Siz_Monitores.*')
        ->where('Siz_Inventario.id', '=',$id)
        ->get();
    
        $data=array('data' => $inventario);
        //$data = $inventario;
        
        $pdf = \PDF::loadView('Mod00_Administrador.pdfInventario', $data);
        //dd($pdf);
        //return $pdf->stream();
        return $pdf->stream('Responsiva.pdf');
    }

    public function delete_inv($id)
    {

        $eliminar = DB::table('Siz_Inventario')->where('id', '=', $id)->delete();
        return redirect('admin/inventario');
    }

    public function mod_inv($id, $mensaje)
    {
        $inventario = DB::table('Siz_Inventario')
            ->join('Siz_Monitores', 'Siz_Inventario.monitor', '=', 'Siz_Monitores.id')
            ->select('Siz_Inventario.id as id_inv', 'Siz_Inventario.*', 'Siz_Monitores.id as id_mon', 'Siz_Monitores.*')
            ->where('Siz_Inventario.id', '=',$id)
            ->orderBy('id_inv')
            ->get();
        //dd($inventario);    
        $monitores = DB::select( DB::raw("SELECT Siz_Monitores.id AS id_mon, nombre_monitor FROM Siz_Monitores LEFT JOIN Siz_Inventario ON Siz_Monitores.id = Siz_Inventario.monitor WHERE Siz_Inventario.monitor IS NULL AND Siz_Monitores.id !='1'") );
        //dd($inventario[0]->nombre_equipo);
        return view('Mod00_Administrador.modInventario', compact('monitores', 'inventario', 'mensaje')); 
        {
            return redirect('admin/inventario');
        }
    }

    public function mod_inv2(Request $request)
    {
        //dd($request->input('monitor'));
        $act_inv = DB::table('Siz_Inventario')
        ->where("id", "=", "$request->id_inv")
        ->update(
            [
                'nombre_equipo' => $request->input('nombre_equipo'),
                'nombre_usuario' => $request->input('nombre_usuario'),
                'correo' => $request->input('correo'), 
                'numero_equipo' => $request->input('numero_equipo'),
                'tipo_equipo' => $request->input('tipo_equipo'),
                'fecha_alta' => $request->input('fecha_alta'),
                'fecha_baja' => $request->input('fecha_baja'),
                'monitor' => $request->input('monitor'),
                'Fecha_mantenimiento' => $request->input('Mtto')
               ]
        );
        $id_inv = $request->id_inv;
        $mensaje="Registro Actualizado Correctamente";
        return $this->mod_inv($id_inv, $mensaje);
    }

//brayan
//Muestra Vista

public function Noticia()
    {
     return view('Mod00_Administrador.Nueva',compact('mensaje'));
    }
    ///inserta datos del formulario Noticias
    public function Noticia2(Request $request)   
    {
        DB::table('Siz_Noticias')->insert(
            [
             'Autor'=>$Nom_User,
             'Destinatario' =>$N_Emp->U_EmpGiro, 
             'Descripcion' => $reason,
             'Estacion_Act' => $Est_act,
             'Estacion_Destino' => $Est_ant,
             'Cant_Enviada'=>$cant_r,
             'Nota' => $nota,
             //'Leido' => 'si' ,
        
            ]
        );
            Session::flash('mensaje', 'Has creado una noticia');
            return redirect('admin/Notificaciones'); 
    }

/////////////Vista Notificacion
   public function Notificacion()
    {

        $noti = DB::table('Siz_Noticias')
                  ->select('Siz_Noticias.*')
                  ->get();
                  //    dd($noti);

                  //$data=array('data' => $noti);
                  
         return view('Mod00_Administrador.Notificaciones', compact('noti')); 
         
    }


    //Aqui empieza modicificación
    public function Mod_Noti($Id_Autor,$Mod_mensaje)
    {
        $Mod_Noti = DB::table('Siz_Noticias')
            ->select('Siz_Noticias.*')
            ->where('Id', '=',$Id_Autor)
            ->get();
        //dd($inventario);  
      
        //dd($inventario[0]->nombre_equipo);
        return view('Mod00_Administrador.ModNotificacion', compact('Mod_Noti', 'Mod_mensaje')); 
    }
    public function Mod_Noti2(Request $request)
    {
           //dd($request->input('Id_Autor'));
           $id_Autor=$request->input('Id');
           $M_noti = DB::table('Siz_Noticias')
            ->where("Id", "=", "$id_Autor")
            ->update(
                        [
                        'Autor' => $request->input('Autor'), 
                        'Destinatario' => $request->input('Destinatario'),
                        'Descripcion' => $request->input('Nota'),
                        ]
    );
    Session::flash('info', 'Tu noticia se ha actualizado');
    return redirect('admin/Notificaciones'); 
    }

   
     public function delete_noti($id_Autor){
      $eliminar = DB::table('Siz_Noticias')->where('Id', '=', $id_Autor)->delete();
    
        Session::flash('info', 'Eliminaste una noticia');
        return redirect()->back();
    }



    
//envia los datos//
}
