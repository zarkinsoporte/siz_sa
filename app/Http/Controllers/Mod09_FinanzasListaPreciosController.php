<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use App\OP;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\SAP;
use Session;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Datatables;
use Validator;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);

class Mod09_FinanzasListaPreciosController extends Controller
{
    public function actualizarPrecios(Request $request){
        $articulos = $request->input('articulos');
        $precio_nuevo = $request->input('precio_nuevo');
        $precio_porcentaje = $request->input('precio_porcentaje');
        $option = $request->input('option');
        $articulos = explode(',', $articulos);
        $priceList =  $request->input('priceList') - 1;               
        $mensajeErr= '-';
        foreach ($articulos as $key => $articulo) {
            $pos = explode('&',$articulo);
            
            $codigo = $pos[0];
            $precio = $pos[1];
            if ($option == '1') { 
                $precio = $precio_nuevo;
            } else if ($option == '2') { 
                $precio += $precio * ( $precio_porcentaje / 100 );
            }
            $rs = SAP::updateItemPriceList($codigo, $priceList, $precio); 
            if($rs !== 'ok'){
                $mensajeErr = 'Error : articulo #'.$codigo.', SAP:'.$rs;
             }
        }
        return compact('mensajeErr');
    }
    public function registros_listaPrecios(Request $request){
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $depList = $request->input('deplist');
            $consulta = DB::select('SELECT OITM.ItemCode as codigo, 
            SUBSTRING( ItemName, 1, 50) AS descripcion, InvntryUom AS um, BuyUnitMsr AS umc, 
            NumInBuy AS factor_conversion, L01.Price AS precio, L01.Currency AS moneda 
            FROM OITM 
            INNER JOIN ITM1 L01 on OITM.ItemCode=L01.ItemCode 
            and L01.PriceList = ?
            WHERE ValidFor = \'Y\' and FrozenFor = \'N\' AND InvntItem = \'Y\' 
            AND U_TipoMat = \'MP\'', [$depList]);

            $arts = collect($consulta);
            return compact('arts');
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }
    public function listaPrecios()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $depList = 0;

            //segun el departamento del usuario (table:OUDP)
            //se asignara la lista de precios correspondiente (table:OPLN)
            switch ($user->dept) {
                case '9': //Diseño
                    $depList = 1;
                    break;
                case '16': //Ventas
                    $depList = 2;
                    break;
                case '4': //Compras
                    $depList = 9;
                    break;
                case '10': //Finanzas
                    $depList = 10;
                    break;
                case '15': //Sistemas
                    $depList = 7;
                    break;
            } 
            $depListName = DB::table('OPLN')->where('ListNum', $depList)
            ->value('ListName');
            $data = array(        
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'deplist' => $depList,
                'deplistname' => $depListName
            );
            return view('Mod04_Materiales.listaPrecios', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }

}