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

Route::get('/home', 'HomeController@index');

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::post('cambio.password',   'AdminController@cambiopassword');

Route::get('Admin',['as' => 'Admin', 'uses' => 'AdminController@index']);
Route::get('users',['as' => 'users', 'uses' => 'AdminController@allUsers']);