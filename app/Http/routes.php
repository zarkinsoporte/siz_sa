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

Route::get('/','HomeController@index');


Route::get('orden/{code}', function ($code) {
    $orden = DB::table('@CP_LOGOF')->where('Code', $code)->first();
    return $orden->U_DocEntry;
});

route::get('setpassword', function (){
    try {
        $password = Hash::make('1234');
        DB::table('dbo.OHEM')
            ->where('U_EmpGiro', 246 )
            ->update(['U_CP_Password' => $password]);
    } catch(\Exception $e) {
        echo  $e->getMessage();
    }

    echo 'hecho';
});

Route::get('/home', 'HomeController@index');

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::post('cambio.password',   'AdminController@cambiopassword');

Route::get('MOD00-ADMINISTRADOR',['as' => 'MOD00-ADMINISTRADOR', 'uses' => 'AdminController@index']);
Route::get('USUARIOS',['as' => 'USUARIOS', 'uses' => 'AdminController@allUsers']);