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
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
Route::get('/','HomeController@index');
Route::get('/home', 'HomeController@index');
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
    $orden = DB::table('@CP_LOGOF')->where('Code', $code)->first();
    return $orden->U_DocEntry;
});

route::get('setpassword', function (){
    try {
        $password = Hash::make('1234');
        DB::table('dbo.OHEM')
            ->where('U_EmpGiro', 1314 )
            ->update(['U_CP_Password' => $password]);
    } catch(\Exception $e) {
        echo  $e->getMessage();
    }

    echo 'hecho';
});

Route::post('cambio.password',   'Mod00_AdministradorController@cambiopassword');

Route::get('users', 'Mod00_AdministradorController@allUsers');
Route::get('users/edit/{empid}', 'Mod00_AdministradorController@editUser');

Route::get('controlPiso', 'Mod01_ProduccionController@estacionSiguiente');

Route::get('grupo/{id}', function ($id){
  Grupo::getInfo($id);
});

Route::get('admin/grupos/{id}', 'Mod00_AdministradorController@editgrupos');

Route::post('admin/createModulo/{id}', 'Mod00_AdministradorController@createModulo');
Route::post('admin/createMenu/{id}', 'Mod00_AdministradorController@createMenu');

Route::post('admin/createTarea/{id_grupo}', 'Mod00_AdministradorController@createTarea');//si se usa
Route::get('admin/grupos/delete_modulo/{id}', 'Mod00_AdministradorController@deleteModulo');
Route::get('admin/grupos/conf_modulo/{id}', 'Mod00_AdministradorController@confModulo');
Route::get('admin/grupos/conf_modulo/quitar/{id}', 'Mod00_AdministradorController@deleteTarea');

Route::get('dropdown', function(){

    return TAREA_MENU::where('id_menu_item',Input::get('option'))
           ->lists('name', 'id');
});


Route::get('datatable/{id}', 'Mod00_AdministradorController@confModulo');
Route::get('datatables.data', 'Mod00_AdministradorController@anyData')->name('datatables.data');

Route::get('updateprivilegio','Mod00_AdministradorController@updateprivilegio');

Route::get('switch', function (){
   $vava = MODULOS_GRUPO_SIZ::find(2);
   $vava->id_menu = null;
   $vava->save();
    var_dump(count(MODULOS_GRUPO_SIZ::find(1)));
});

Route::post('nuevatarea', 'Mod00_AdministradorController@nuevatarea');
Route::post('usuariotraslados', 'Mod01_ProduccionController@usuariotraslados');


/*
|--------------------------------------------------------------------------
| MOD01-PRODUCCION Routes
|--------------------------------------------------------------------------
*/
Route::get('home/TRASLADO ÷ AREAS', 'Mod01_ProduccionController@traslados');