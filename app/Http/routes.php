<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Grupo;
use App\Modelos\MOD01\TAREA_MENU;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\OP;
use Illuminate\Support\Facades\DB;
Route::get('/','HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/pruebas', function(){
    return view('Mod00_Administrador.pruebas');
});
/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);
/*
|--------------------------------------------------------------------------
| MOD00-ADMINISTRADOR Routes
|--------------------------------------------------------------------------
*/
Route::get('MOD00-ADMINISTRADOR','Mod00_AdministradorController@index');
Route::get('orden/{code}', function ($code) {
//    $orden = DB::table('@CP_LOGOF')->where('Code', $code)->first();
//    return $orden->U_DocEntry;

    $Codes = OP::where('U_DocEntry', '60987' )->get();

//dd($Codes);
    $index = 0;
    foreach ($Codes as $code){
        $index = $index+1;
        $order =  DB::table('OWOR')
            ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
            ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry','=', 'OWOR.DocEntry')
            ->select(DB::raw( OP::getEstacionActual($code->Code).' AS U_CT_ACT'), DB::raw( OP::getEstacionSiguiente($code->Code).' AS U_CT_SIG'),
                'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden','OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
            ->where('@CP_OF.Code', $code->Code)->get();
        if ($index == 1){
            $one = DB::table('OWOR')
                ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry','=', 'OWOR.DocEntry')
                ->select(DB::raw( OP::getEstacionActual($code->Code).' AS U_CT_ACT'), DB::raw( OP::getEstacionSiguiente($code->Code).' AS U_CT_SIG'),
                    'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden','OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                    'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                ->where('@CP_OF.Code', $code->Code)->get();
        }else{

            $one = //array_merge($one, $order) ; //
            $one->merge($order);
            //dd($one);
        }
    }
  //  $order = OP::find('492418');
    return $one;
});
route::get('setpassword', function (){
    try {
        $password = Hash::make('1234');
        DB::table('dbo.OHEM')
            ->where('U_EmpGiro', 1349 )
            ->update(['U_CP_Password' => $password]);
    } catch(\Exception $e) {
        echo  $e->getMessage();
    }

    echo 'hecho';
});
Route::post('cambio.password',   'Mod00_AdministradorController@cambiopassword');

Route::get('admin/users', 'Mod00_AdministradorController@allUsers');
Route::get('users/edit/{empid}', 'Mod00_AdministradorController@editUser');
Route::get('admin/detalle-depto/{depto}','Mod00_AdministradorController@showUsers');
Route::get('datatables.showusers', 'Mod00_AdministradorController@DataShowUsers')->name('datatables.showusers');

//Rutas del Módulo de inventarios
Route::get('admin/altaInventario', 'Mod00_AdministradorController@altaInventario');
Route::post('admin/altaInventario', 'Mod00_AdministradorController@altaInventario2');
Route::post('admin/ModInventario', 'Mod00_AdministradorController@ModInventario');
Route::get('/admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor');
Route::post('admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor2');
Route::get('admin/inventario', 'Mod00_AdministradorController@inventario');
Route::get('admin/inventarioObsoleto', 'Mod00_AdministradorController@inventarioObsoleto');
Route::get('admin/monitores', 'Mod00_AdministradorController@monitores');
Route::get('admin/mark_obs/{id}', 'Mod00_AdministradorController@mark_obs');
Route::get('admin/mark_rest/{id}', 'Mod00_AdministradorController@mark_rest');
Route::get('admin/delete_inv/{id}', 'Mod00_AdministradorController@delete_inv');
Route::get('admin/mod_inv/{id}/{mensaje}', 'Mod00_AdministradorController@mod_inv');
Route::get('admin/mod_mon/{id}/{mensaje}', 'Mod00_AdministradorController@mod_mon');
Route::post('admin/mod_mon2', 'Mod00_AdministradorController@mod_mon2');
Route::post('admin/mod_inv2', 'Mod00_AdministradorController@mod_inv2');
Route::get('admin/generarPdf/{id}', 'Mod00_AdministradorController@generarPdf');

Route::get('controlPiso', 'Mod01_ProduccionController@estacionSiguiente');
Route::get('grupo/{id}', function ($id){
  Grupo::getInfo($id);
});
Route::get('admin/grupos/{id}', 'Mod00_AdministradorController@editgrupos');
Route::post('admin/createModulo/{id}', 'Mod00_AdministradorController@createModulo');
Route::post('admin/createMenu/{id}', 'Mod00_AdministradorController@createMenu');
Route::post('admin/createTarea/{id_grupo}', 'Mod00_AdministradorController@createTarea');//si se usa
Route::get('admin/grupos/delete_modulo/{id_grupo}/{id_modulo}', 'Mod00_AdministradorController@deleteModulo');
Route::get('admin/grupos/conf_modulo/{id_grupo}/{id_modulo}', 'Mod00_AdministradorController@confModulo');
Route::get('admin/grupos/conf_modulo/quitar/{id}', 'Mod00_AdministradorController@deleteTarea');
Route::get('help', function(){

    $produccion =  DB::select('SELECT "CP_ProdTerminada"."orden", "CP_ProdTerminada"."Pedido", "CP_ProdTerminada"."Codigo",
 "CP_ProdTerminada"."modelo", "CP_ProdTerminada"."VS", "CP_ProdTerminada"."fecha", 
 "CP_ProdTerminada"."Name", "CP_ProdTerminada"."CardName", "CP_ProdTerminada"."Semana", 
 "CP_ProdTerminada"."U_Tiempo", "CP_ProdTerminada"."Cantidad", "CP_ProdTerminada"."TVS", 
 "CP_ProdTerminada"."TTiempo"
 FROM   "FUSIONL"."dbo"."CP_ProdTerminada" "CP_ProdTerminada"
 WHERE  ("CP_ProdTerminada"."fecha">=\'12/12/2017\' AND 
 "CP_ProdTerminada"."fecha"<=\'12/12/2017\') AND 
 ("CP_ProdTerminada"."Name"= (\'175 Inspeccion Final\')  OR "CP_ProdTerminada"."Name"= (CASE
 WHEN  \'175 Inspeccion Final\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 ');

    print_r($produccion);

    dd(date('Y-m-d H:i:s'));
    $index = 1;
    $log = LOGOF::where('id', 1000)->first();
   // dd($log);
//    $newCode = new OP();
//    $newCode->Code =12121212;
//    $newCode->save();
//    $varOP = OP::find(12121212);
    $consecutivo =  DB::select('SELECT TOP 1 Code FROM  [FUSIONL2].[dbo].[@CP_LOGOT] ORDER BY  U_FechaHora DESC');
    //$consecutivo = ((int)$users->Code);
echo $consecutivo[0]->Code;
   // echo $log->U_CT;

});
Route::get('datatable/{idGrup}/{idMod}', 'Mod00_AdministradorController@confModulo');
Route::get('datatables.data', 'Mod00_AdministradorController@anyData')->name('datatables.data');
/*
|--------------------------------------------------------------------------
|NOTICIAS Y NOTIFICACIONES 'BRAYAN'
|--------------------------------------------------------------------------
*/
Route::get('admin/Nueva', 'Mod00_AdministradorController@Noticia');
Route::post('admin/Nueva', 'Mod00_AdministradorController@Noticia2');
Route::get('admin/Notificaciones', 'Mod00_AdministradorController@Notificacion');
Route::post('admin/Notificaciones', 'Mod00_AdministradorController@Notificacion2');
Route::get('admin/Mod_Noti/{id}/{mensaje}', 'Mod00_AdministradorController@Mod_Noti');
Route::post('admin/Mod_Noti2/', 'Mod00_AdministradorController@Mod_Noti2');
Route::get('admin/delete_Noti/{id}', 'Mod00_AdministradorController@delete_Noti');
Route::post('admin/delete_Noti/', 'Mod00_AdministradorController@delete_Noti');
//Route::get('admin/Nueva', 'Mod00_AdministradorController@Show');
/*
|--------------------------------------------------------------------------
| Finaliza Rutas Noticias y Notificaciones
|--------------------------------------------------------------------------
*/

Route::get('updateprivilegio','Mod00_AdministradorController@updateprivilegio');
Route::get('dropdown', function(){
         return TAREA_MENU::where('id_menu_item',Input::get('option'))
             ->lists('name', 'id');
  });

Route::get('switch', function (){
   $vava = MODULOS_GRUPO_SIZ::find(2);
   $vava->id_menu = null;
   $vava->save();
    var_dump(count(MODULOS_GRUPO_SIZ::find(1)));
});
Route::post('nuevatarea', 'Mod00_AdministradorController@nuevatarea');

/*
|--------------------------------------------------------------------------
| MOD01-PRODUCCION Routes
|--------------------------------------------------------------------------
*/
Route::get('home/R. PROD. GRAL.','Reportes_ProduccionController@produccion1');
Route::post('home/R. PROD. GRAL.','Reportes_ProduccionController@produccion1');
Route::get('home/TRASLADO ÷ AREAS', 'Mod01_ProduccionController@traslados');
Route::post('home/TRASLADO ÷ AREAS', 'Mod01_ProduccionController@traslados');
Route::get('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
Route::post('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
Route::post('home/traslados/avanzar', 'Mod01_ProduccionController@avanzarOP');
Route::post('home/traslados/Reprocesos', 'Mod01_ProduccionController@Retroceso');

// PDF de Historial por OP
Route::get('home/ReporteOpPDF/{op}', 'Mod01_ProduccionController@ReporteOpPDF');
Route::get('home/ReporteMaterialesPDF/{op}', 'Mod01_ProduccionController@ReporteMaterialesPDF');

Route::get('admin/aux', function(){
    $menus = MODULOS_GRUPO_SIZ::where('MODULOS_GRUPO_SIZ.id_modulo',2)
    ->where('MODULOS_GRUPO_SIZ.id_grupo', 2)
    ->whereNotNull('id_menu')
    ->whereNotNull('id_tarea')
    ->leftjoin('MENU_ITEM_SIZ', 'MODULOS_GRUPO_SIZ.id_menu', '=', 'MENU_ITEM_SIZ.id')
    ->leftjoin('TAREA_MENU_SIZ', 'MODULOS_GRUPO_SIZ.id_tarea', '=', 'TAREA_MENU_SIZ.id')
    ->select('MODULOS_GRUPO_SIZ.*', 'MENU_ITEM_SIZ.name as menu', 'TAREA_MENU_SIZ.name as tarea')
    ->get();
dd($menus);
});



