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
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\OP;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
Route::get('/', 'HomeController@index');
Route::get('/home',
    [
        'as' => 'home',
        'uses' => 'HomeController@index',
    ]);
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
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' => 'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);
Route::post('passwordUpdate', ['as' => 'passwordUpdate', 'uses' => 'Auth\FunctionsController@cambioPasswordUsers']);
Route::get('viewpassword', ['as' => 'viewpassword', 'uses' => 'Auth\FunctionsController@viewpassword']);
/*
|--------------------------------------------------------------------------
| MOD00-ADMINISTRADOR Routes
|--------------------------------------------------------------------------
 */
Route::get('MOD00-ADMINISTRADOR', 'Mod00_AdministradorController@index');
Route::get('admin/users', 'Mod00_AdministradorController@allUsers');
Route::get('admin/detalle-depto/{depto}', 'Mod00_AdministradorController@showUsers');
Route::get('admin/plantilla/{depto}', 'Mod00_AdministradorController@PlantillaExcel');
Route::get('admin/Plantilla_PDF/{depto}', 'Mod00_AdministradorController@Plantilla_PDF');
Route::get('datatables.showusers', 'Mod00_AdministradorController@DataShowUsers')->name('datatables.showusers');
Route::get('users/edit/{empid}', 'Mod00_AdministradorController@editUser');
Route::post('cambio.password', 'Mod00_AdministradorController@cambiopassword');
//Rutas del Módulo de inventarios
Route::get('admin/inventario', 'Mod00_AdministradorController@inventario');
Route::get('datatables.inventario', 'Mod00_AdministradorController@DataInventario')->name('datatables.inventario');
Route::post('admin/reporte/inventario', 'Mod00_AdministradorController@backOrderAjaxToSession');
Route::get('admin/reporte/inventarioComputoPDF', 'Mod00_AdministradorController@ReporteInventarioComputoPDF');
Route::get('admin/generarPdf/{id}', 'Mod00_AdministradorController@generarPdf');

Route::get('admin/inventarioObsoleto', 'Mod00_AdministradorController@inventarioObsoleto');

Route::get('admin/altaInventario', 'Mod00_AdministradorController@altaInventario');
Route::post('admin/altaInventario', 'Mod00_AdministradorController@saveInventario');
Route::post('admin/ModInventario', 'Mod00_AdministradorController@ModInventario');
Route::get('/admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor');
Route::post('admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor2');

Route::get('admin/monitores', 'Mod00_AdministradorController@monitores');
Route::get('admin/mark_obs/{id}', 'Mod00_AdministradorController@mark_obs');
Route::get('admin/mark_rest/{id}', 'Mod00_AdministradorController@mark_rest');
Route::get('admin/delete_inv/{id}', 'Mod00_AdministradorController@delete_inv');
Route::get('admin/mod_inv/{id}', 'Mod00_AdministradorController@mod_inv');
Route::get('admin/mod_mon/{id}/{mensaje}', 'Mod00_AdministradorController@mod_mon');
Route::post('admin/mod_mon2', 'Mod00_AdministradorController@mod_mon2');
Route::post('admin/mod_inv2', 'Mod00_AdministradorController@mod_inv2');
Route::get('admin/grupos/{id}', 'Mod00_AdministradorController@editgrupos');
Route::post('admin/createModulo/{id}', 'Mod00_AdministradorController@createModulo');
Route::post('admin/createMenu/{id}', 'Mod00_AdministradorController@createMenu');
Route::post('admin/createTarea/{id_grupo}', 'Mod00_AdministradorController@createTarea'); //si se usa
Route::get('admin/grupos/delete_modulo/{grupo}/{id}', 'Mod00_AdministradorController@deleteModulo');
Route::get('admin/grupos/conf_modulo/{grupo}/{id}', 'Mod00_AdministradorController@confModulo');
Route::get('admin/grupos/conf_modulo/{grupo}/quitar-tarea/{id}', 'Mod00_AdministradorController@deleteTarea');
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


Route::get('updateprivilegio', 'Mod00_AdministradorController@updateprivilegio');
Route::get('dropdown', function () {
    return TAREA_MENU::where('id_menu_item', Input::get('option'))
    ->lists('name', 'id');
});
Route::get('switch', function () {
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
Route::get('home/TRASLADO ÷ AREAS', [
    'as' => 'traslado', 'uses' =>'Mod01_ProduccionController@traslados']);
    Route::post('home/TRASLADO ÷ AREAS', 'Mod01_ProduccionController@traslados');
    Route::get('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
    Route::post('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
    //la siguiente ruta avanza la orden //
    Route::post('home/traslados/avanzar', 'Mod01_ProduccionController@avanzarOP');
    Route::post('home/traslados/Reprocesos', 'Mod01_ProduccionController@Retroceso');
    //Route::get('home/traslados/Reprocesos', 'Mod01_ProduccionController@getOP');
    Route::post('/', 'HomeController@index');
    Route::get('Mod01_Produccion/Noticias', 'HomeController@create');
    Route::get('leido/{id}', 'HomeController@UPT_Noticias');
    Route::post('/leido', 'HomeController@UPT_Noticias');
    //REPORTE DE PRODUCCION
    Route::get('home/REPORTE PRODUCCION', 'Reportes_ProduccionController@produccion1');
    Route::post('home/REPORTE PRODUCCION', 'Reportes_ProduccionController@produccion1');
    //REPORTE 112-CORTE PIEL///
    Route::get('home/112 CORTE DE PIEL','Mod01_ProduccionController@repCortePiel' );
    Route::post('home/112 CORTE DE PIEL','Mod01_ProduccionController@repCortePiel' );
    Route::post('home/reporte/DetinsPiel', 'Mod01_ProduccionController@repCortePiel');
    Route::get('home/repCortePielExl', 'Mod01_ProduccionController@repCortePielExl');
    //PDF de Historial por OP
    Route::get('home/ReporteOpPDF/{op}', 'Mod01_ProduccionController@ReporteOpPDF');
    Route::get('home/ReporteMaterialesPDF/{op}', 'Mod01_ProduccionController@ReporteMaterialesPDF');
    Route::get('home/ReporteProduccionPDF', 'Reportes_ProduccionController@ReporteProduccionPDF');
    Route::get('home/ReporteProduccionEXL', 'Reportes_ProduccionController@ReporteProduccionEXL');
    //REPORTE DE HISTORIAL X OP
    Route::get('home/HISTORIAL OP', 'Reportes_ProduccionController@showModal');
    Route::post('home/reporte/HISTORIAL OP', 'Reportes_ProduccionController@historialOP');
    Route::get('home/reporte/historialXLS', 'Reportes_ProduccionController@historialOPXLS');
    //REPORTE DE MATERIALES X OP
    Route::get('home/MATERIALES OP', 'Reportes_ProduccionController@showModal');
    Route::post('home/reporte/MATERIALES OP', 'Reportes_ProduccionController@materialesOP');
    Route::get('home/reporte/materialesXLS', 'Reportes_ProduccionController@materialesOPXLS');
    //REPORTE BACK ORDER
    Route::get('home/BACK ORDER', 'Reportes_ProduccionController@backorder');
    Route::get('datatables.showbackorder', 'Reportes_ProduccionController@DataShowbackorder')->name('datatables.showbackorder');
    Route::post('home/reporte/backorderPDF', 'Reportes_ProduccionController@backOrderAjaxToSession');
    Route::get('home/reporte/backorderVentasPDF', 'Reportes_ProduccionController@ReporteBackOrderVentasPDF');
    Route::get('home/reporte/backorderPlaneaPDF', 'Reportes_ProduccionController@ReporteBackOrderPlaneaPDF');
    Route::get('home/reporte/backorderXLS', 'Reportes_ProduccionController@ReporteBackOrderXLS');
    //RPORTE BACK ORDER CASCO
    Route::get('home/BACK ORDER CASCO', 'Reportes_ProduccionController@backorderCasco');
    Route::get('datatables.showbackordercasco', 'Reportes_ProduccionController@DataShowbackorderCasco')->name('datatables.showbackordercasco');
    
    Route::get('home/reporte/backorderCascoPDF', 'Reportes_ProduccionController@ReporteBackOrderCascoPDF');
    //Ruta generica para guardar ajaxtoSession
    Route::post('home/reporte/ajaxtosession/{id}', 'Reportes_ProduccionController@AjaxToSession');
    /*
    |--------------------------------------------------------------------------
    | MOD07-CALIDAD Routes
    |--------------------------------------------------------------------------
    */
    Route::get('home/NUEVO RECHAZO', 'Mod07_CalidadController@Rechazo');
    Route::post('RechazosNuevo', 'Mod07_CalidadController@RechazoIn');
    Route::get('Mod07_Calidad/Mod_Rechazo/{id}/{mensaje}', 'Mod07_CalidadController@Mod_Rechazo');
    Route::post('Mod07_Calidad/Mod_RechazoUPDT', 'Mod07_CalidadController@Mod_RechazoUPDT');
    Route::get('admin/Delete_Rechazo/{id}', 'Mod07_CalidadController@Delete_Rechazo');
    Route::post('admin/Delete_Rechazo/', 'Mod07_CalidadController@Delete_Rechazo');
    Route::get('search/autocomplete', 'Mod07_CalidadController@autocomplete');
    Route::post('/pdfRechazo', 'Mod07_CalidadController@Pdf_Rechazo');
    Route::get('home/REPORTE DE RECHAZOS', 'Mod07_CalidadController@Reporte');
    Route::get('home/CANCELACIONES', 'Mod07_CalidadController@Cancelado');
    Route::get('borrado/{id}', 'Mod07_CalidadController@UPT_Cancelado');
    Route::post('/borrado', 'Mod07_CalidadController@UPT_Cancelado');
Route::get('home/HISTORIAL', 'Mod07_CalidadController@Historial');
Route::post('/excel', 'Mod07_CalidadController@excel');
////reporte calidad
Route::get('home/CALIDAD POR DEPTO','Mod07_CalidadController@repCalidad' );
Route::post('home/CALIDAD POR DEPTO','Mod07_CalidadController@repCalidad2' );
Route::get('getAutocomplete', function () {
    return view('Mod07_Calidad.RechazoFrame');
})->name('getAutocomplete');
Route::get('search', array('as' => 'search', 'uses' => 'Mod07_CalidadController@search'));
Route::get('autocomplete', array('as' => 'autocomplete', 'uses' => 'Mod07_CalidadController@autocomplete'));
//
//-------------------------//
//RUTAS DE RECURSOS HUMANOS//---------------------------------------------------------
//-------------------------//
//
Route::get('home/CALCULO DE BONOS', 'Mod10_RhController@parametrosmodal');
//Route::get('home/rh/reportes/bonos','Mod10_RhController@calculoBonos');
Route::post('home/rh/reportes/bonos', 'Mod10_RhController@calculoBonos');
Route::get('home/PARAMETROS BONOS', 'Mod10_RhController@setParametrosBonos');
Route::post('home/PARAMETROS BONOS', 'Mod10_RhController@setParametrosBonos2');
Route::get('home/rh/reportes/bonosPdf', 'Mod10_RhController@bonosPdf');
Route::get('home/BONOS CORTE','Mod10_RhController@bonosCorte' );
Route::post('home/rh/reportes/bonosCorte', 'Mod10_RhController@calculoBonosCorte');
Route::get('home/rh/reportes/bonoscortePdf', 'Mod10_RhController@bonoscortePdf');
Route::get('home/rh/reportes/bonoscorteEXL', 'Mod10_RhController@bonoscorteEXL');
Route::get('home/mod_parametro/{id}', 'Mod10_RhController@mod_parametro');
Route::post('home/mod_parametro2/{id}', 'Mod10_RhController@mod_parametro2');
Route::get('home/delete_parametro/{id}', 'Mod10_RhController@delete_parametro');
//
//-------------------------//
//RUTAS DE COMPRAS//---------------------------------------------------------
//-------------------------//
//
Route::get('home/CONSULTA OC', 'Mod03_ComprasController@pedidosCsv');
Route::post('home/CONSULTA OC', 'Mod03_ComprasController@postPedidosCsv');
Route::get('home/desPedidosCsv', 'Mod03_ComprasController@desPedidosCsv');
Route::get('home/PedidosCsvPDF', 'Mod03_ComprasController@PedidosCsvPDF');
//
//-------------------------//
//RUTAS DE MATERIALES ALMACEN//---------------------------------------------------------
//-------------------------//
//
Route::get('home/ENTRADAS ALMACEN', 'Reportes_ProduccionController@showModal');
Route::post('home/reporte/ENTRADAS ALMACEN', 'Mod04_MaterialesController@reporteEntradasAlmacen');
Route::get('home/reporte/ENTRADAS ALMACEN', 'Mod04_MaterialesController@reporteEntradasAlmacenPDF');
Route::get('home/reporte/entradasXLS', 'Mod04_MaterialesController@entradasXLS');
Route::get('datatables.showentradasmp', 'Mod04_MaterialesController@DataShowEntradasMP')->name('datatables.showentradasmp');
Route::get('home/reporte/entradasPDF', 'Mod04_MaterialesController@entradasPDF');

//DESPLIEGUE DATOS MAESTROS ARTICULOS
Route::get('home/DATOS MAESTROS ARTICULOS', 'Reportes_ProduccionController@showModal');
Route::post('home/reporte/DATOS MAESTROS ARTICULOS/{redirek?}', 'Mod04_MaterialesController@DM_Articulos');
Route::post('articuloToSap', 'Mod04_MaterialesController@articuloToSap');
Route::get('OITM.show', 'HomeController@ShowArticulos')->name( 'OITM.show');
//
//ENTREGA DE MATERIALES
Route::get('home/SOLICITUD MATERIALES', 'Mod04_MaterialesController@solicitudMateriales');
Route::get('OITM.WH.show', 'Mod04_MaterialesController@ShowArticulosWH')->name( 'OITM.WH.show');
Route::post('home/saveArt', 'Mod04_MaterialesController@saveArt')->name('home/saveArt');
//
//-------------------------//
//RUTAS DE MRP//---------------------------------------------------------
//-------------------------//
//
Route::get('home/MRP', 'Reportes_ProduccionController@showModal');
Route::post('home/reporte/MRP', 'Mod02_PlaneacionController@reporteMRP');
Route::get('home/reporte/mrpXLS', 'Mod02_PlaneacionController@mrpXLS');
Route::get('datatables.showmrp', 'Mod02_PlaneacionController@DataShowMRP')->name('datatables.showmrp');
Route::get('home/reporte/mrpPDF', 'Mod02_PlaneacionController@mrpPDF');

Route::get('home/ACTUALIZAR MRP', 'Reportes_ProduccionController@showModal');
Route::post('home/reporte/ACTUALIZAR MRP', 'Mod02_PlaneacionController@actualizaMRP');
///Ruta Ayudas
Route::get('home/ayudas_pdf/{PdfName}', 'HomeController@showPdf');
Route::get('home/{r0}/ayudas_pdf/{PdfName}', 'HomeController@showPdf2');
//
//-------------------------//
//RUTAS DE PRODUCCION POR AREAS//---------------------------------------------------------
//-------------------------//
//
Route::get('home/PRODUCCION POR AREAS', 'Reportes_ProduccionController@showModal');
Route::post('home/reporte/PRODUCCION POR AREAS', 'Reportes_ProduccionController@reporteProdxAreas');
Route::get('home/reporte/PRODUCCION POR AREAS', 'Reportes_ProduccionController@reporteProdxAreasPDF');
Route::get('home/reporte/produccionxareasXLS', 'Reportes_ProduccionController@produccionxareasXLS');

 Route::get('/pruebas', function (Request $request) {
    
    //  $vCmp = new COM ('SAPbobsCOM.company') or die ("Sin conexión");
//  $vCmp->DbServerType="6"; 
//  $vCmp->server = "SERVER-SAPBO";
//  $vCmp->LicenseServer = "SERVER-SAPBO:30000";
//  $vCmp->CompanyDB = "Pruebas";
//  $vCmp->username = "manager";
//  $vCmp->password = "aqnlaaepp";
//  $vCmp->DbUserName = "sa";
//  $vCmp->DbPassword = "B1Admin";
//  $vCmp->UseTrusted = false;
//  $vCmp->language = "6";
//  $lRetCode = $vCmp->Connect;
//  //dd($lRetCode);
//  echo $vCmp->GetLastErrorDescription();
//  echo 'iniciada';
//  echo '<br>';
//  $vItem = $vCmp->GetBusinessObject("202");
//  $RetVal = $vItem->GetByKey("19848");
//  echo $vItem->ProductionOrderStatus;
//  echo '<br>';
//  $vItem->ProductionOrderStatus = 1;
//  $vItem->Update;
//  //if ($vCmp->InTransaction){
//      //$vCmp->EndTransaction();
//   //   dd('cerrada');
//  //}
//  echo $vCmp->GetLastErrorDescription();
//  echo $vItem->ProductionOrderStatus;
     //return view('Mod00_Administrador.pruebas');
 });
 Route::get('setpassword', function () {
    try {
        $password = Hash::make('1234');
        DB::table('dbo.OHEM')
            ->where('U_EmpGiro', 1349)
            ->update(['U_CP_Password' => $password]);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    echo 'hecho';
});
 