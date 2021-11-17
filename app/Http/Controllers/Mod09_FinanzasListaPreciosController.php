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
    public function simulador_actualizarPrecios(Request $request){
        $articulos = $request->input('articulos');
        $precio_nuevo = $request->input('precio_nuevo');
        $precio_porcentaje = $request->input('precio_porcentaje');
        $option = $request->input('option');
        $moneda = $request->input('moneda');
        $tc_usd = $request->input('tc_usd');
        $tc_can = $request->input('tc_can');
        $tc_eur = $request->input('tc_eur');
        $code_composicion = $request->input('code_composicion');
        $articulos = explode(',', $articulos);
       
        foreach ($articulos as $key => $articulo) {
            $pos = explode('&',$articulo);
            
            $codigo = $pos[0];
            $precio = $pos[1];
            
            if ($option == '1') { 
                $precio = $precio_nuevo;
            } else if ($option == '2') { 
                $precio += $precio * ( $precio_porcentaje / 100 );
            }
            switch ($moneda) {
                case 'USD':
                    $precioMXP = $precio * $tc_usd;
                    break;
                case 'CAN':
                    $precioMXP = $precio * $tc_can;
                    break;
                case 'EUR':
                    $precioMXP = $precio * $tc_eur;
                    break;                
                default:
                    $precioMXP = $precio;
                    break;
            }
            DB::update('update Siz_simulador_temp set precio = ?, precioMXP = ?
            where composicionCodigo = ?
            and codigo = ?', 
            [$precio, $precioMXP, $code_composicion, $codigo]);
        }
        return 'ok';
    }
    public function datatables_simulador_precios(Request $request)
    {
        $cat = $request->get('categoria');   
        switch ($cat) {
            case 'precios_piel':
                $cat = '9';
                break;
            case 'precios_tela':
                $cat = '11';
                break;
            
            default:
                # code...
                break;
        }
        $id = $request->get('id');        
      //Siz_simulador_temp
            
        $material = DB::select('select *, cantidad*precioMXP as precio_pesos from Siz_simulador_temp
        where composicionCodigo = ?
        and grupoPlaneacion = ? and subModelo <> ? 
        ', [$id, $cat,'C']);
               
        return response()->json(array('material' => $material));
        
        $consulta = DB::select('exec SIZ_SIMULADOR_COSTO_LDM  ?', [$tc_usd]);
        //Definimos las columnas 
        $consulta= collect($consulta);
        return Datatables::of($consulta)
            
            ->addColumn(
                'pieles',
                function ($item) {
                    return  number_format($item->g_piel_cant, 2, '.', ',') . ' / ' . number_format($item->g_tela_cant, 2, '.', ',');
                }
            )
            ->addColumn(
                'pieles_precio',
                function ($item) {
                    //return   . ;
                    return '<a href="#" id="precios_piel">
                            <i class="fa fa-hand-o-right"></i> '.number_format($item->g_piel, 2, '.', ',').'</a> / 
                            <a href="#" id="precios_tela">
                            <i class="fa fa-hand-o-right"></i> '.number_format($item->g_tela, 2, '.', ',').'</a>';
                    
                }
            )
            ->make(true);
      
    }
    public function Simulador($modelo, $modelo_descr)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $tc = DB::select('SELECT TOP 1 TC_can can, TC_usd usd, TC_eur eur
                        FROM SIZ_TipoCambio 
                        WHERE YEAR(TC_date) = YEAR(GETDATE())');
            
            $data = array(
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'modelo' => $modelo,
                'modelo_descr' => $modelo_descr,
                'tc' => $tc
            );
            return view('Mod09_Finanzas.simulador', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function datatables_simulador(Request $request)
    {
        $modelo = $request->get('modelo');        
        $tc_usd = $request->get('tc_usd');        
        $tc_can = $request->get('tc_can');        
        $tc_eur = $request->get('tc_eur');        
        $insert = $request->get('insert');

        if ($insert == 1 ) {
            
        $composiciones = DB::select('Select OITM.ItemCode,
        OITM.FrgnName composicion
        From OITM
        inner join ITT1 on OITM.ItemCode = ITT1.Father  
        inner join OITM A1 on ITT1.Code = A1.ItemCode and A1.ItemCode =\'12826\'
        Where OITM.ItemCode like \''.$modelo.'%\' and oitm.ItemCode like \'%0301\' 
        ');
        //dd($composiciones);              
        DB::table('Siz_simulador_temp')->truncate();
        
        foreach ($composiciones as $value) {
            $xCodeSub[0] = $value->ItemCode;
            //dd($xCodeSub);
            foreach ($xCodeSub as $codeSub) {

                $subs = DB::select("Select OITM.ItemCode AS CODIGO 
                    from ITT1 
                    inner join OITM on OITM.ItemCode = ITT1.Code 
                    inner join [@PL_RUTAS] RUTE on RUTE.Code = OITM.U_estacion 
                    inner join ITM1 on ITM1.ItemCode = OITM.ItemCode 
                    and ITM1.PriceList=? 
                    where  ITT1.Father = ? and 
                    (OITM.QryGroup29 = 'Y' or OITM.QryGroup30 = 'Y' or OITM.QryGroup31 = 'Y' or OITM.QryGroup32 = 'Y') 
                    --Order by MATERIAL",
                    [1, $codeSub]);
                 $subs = array_pluck($subs,'CODIGO');

                $xCodeSub = array_merge($xCodeSub, $subs);
                
            }
            $sub_cadena = "";
            for ($x = 0; $x < count($xCodeSub); $x++) {
                if ($x == count($xCodeSub) - 1) {
                    $sub_cadena = $sub_cadena . $xCodeSub[$x];
                } else {
                    $sub_cadena = $sub_cadena . $xCodeSub[$x] . ",";
                }
            }
            $xCodeSub = [];
            //dd([$sub_cadena, $value->ItemCode, $value->composicion, $tc_usd, $tc_can, $tc_eur]);
            clock([$sub_cadena, $value->ItemCode]);
            DB::select('exec SIZ_SIMULADOR_COSTO_LDM_INSERT  ?, ?, ?, ?', 
            [$sub_cadena, $value->ItemCode, $value->composicion, $tc_usd]);
        }

        } //end insert
        
        $consulta = DB::select('exec SIZ_SIMULADOR_COSTO_LDM  ?', [$tc_usd]);
        //Definimos las columnas 
        $consulta= collect($consulta);
        return Datatables::of($consulta)
            
            ->addColumn(
                'pieles',
                function ($item) {
                    return  number_format($item->g_piel_cant, 2, '.', ',') . ' / ' . number_format($item->g_tela_cant, 2, '.', ',');
                }
            )
            ->addColumn(
                'pieles_precio',
                function ($item) {
                    //return   . ;
                    return '<a href="#" id="precios_piel">
                            <i class="fa fa-hand-o-right"></i> '.number_format($item->g_piel, 2, '.', ',').'</a> / 
                            <a href="#" id="precios_tela">
                            <i class="fa fa-hand-o-right"></i> '.number_format($item->g_tela, 2, '.', ',').'</a>';
                    
                }
            )
            ->make(true);
        $columns = array(
            ["data" => "composicionCodigoCorto", "name" => "Código"],
            ["data" => "composicion", "name" => "Composición"],
            ["data" => "pielcant", "name" => "DCM/MT"],
            ["data" => "gpiel", "name" => "1 Piel/Tela T"],
            ["data" => "pg_piel_tela", "name" => "% Piel"],
            ["data" => "g_huleUSD", "name" => "Dólares Hule"],
            ["data" => "g_hule", "name" => "2 Hule"],
            ["data" => "pg_hule", "name" => "% Hule"],
            ["data" => "g_cojineria", "name" => "3 Pluma/Acojin"],
            ["data" => "pg_cojineria", "name" => "% Cojín"],
            ["data" => "g_casco", "name" => "4 Casco"],
            ["data" => "pg_casco", "name" => "% Casco"],
            ["data" => "g_herrajesUSD", "name" => "Dólares Herrajes"],
            ["data" => "g_herrajes", "name" => "5 Herrajes y Mecanismos"],
            ["data" => "pg_herrajes", "name" => "% Herrajes"],
            ["data" => "g_metalesUSD", "name" => "Dólares Patas"],
            ["data" => "g_metales", "name" => "6 Patas"],
            ["data" => "pg_metales", "name" => "% Patas"],
            ["data" => "g_empaques", "name" => "7 Empaque"],
            ["data" => "pg_empaques", "name" => "% Empaque"],
            ["data" => "g_otros", "name" => "8 Otros"],
            ["data" => "pg_metales", "name" => "% Otros"],
            ["data" => "g_cuotas", "name" => "9 Cuotas"],
            ["data" => "pg_cuotas", "name" => "% Cuotas"],
            ["data" => "total", "name" => "Total"]

        );

       // return response()->json(array('data' => $consulta, 'columns' => $columns));
    }
    public function DetalleModelos($modelo, $modelo_descr)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();

            $data = array(
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'modelo' => $modelo,
                'modelo_descr' => $modelo_descr

            );
            return view('Mod09_Finanzas.detalleModelos', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function datatables_simulador_detalle_modelos(Request $request)
    {
        $consulta = DB::select('
            Declare @TiCa_CAN decimal(10,4)
            Declare @TiCa_USD decimal(10,4)
            Declare @TiCa_EUR decimal(10,4)

                        SELECT TOP 1 @TiCa_CAN = TC_can, @TiCa_USD = TC_usd, @TiCa_EUR = TC_eur
                        FROM SIZ_TipoCambio 
                        WHERE YEAR(TC_date) = YEAR(GETDATE())

                        SELECT  ITT1.Father AS CODIGO
                        , A3.ItemName AS DESCRIPCION		 
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P03\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC03
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P04\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC04
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P05\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC05
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P06\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC06
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P07\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC07
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P08\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC08
                        , Case When Left(Right(ITT1.Father, 5),2) <> \'P0\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS OTROS
                        , \'MXP\' AS MONEDA
                FROM ITT1 
                INNER JOIN OITM A3 on ITT1.Father = A3.ItemCode
                INNER JOIN ITM1 L1 on ITT1.Code= L1.ItemCode and L1.PriceList=1 
                WHERE A3.InvntItem = \'Y\' and A3.frozenFor=\'N\' and A3.U_TipoMat = \'PT\' 
                and A3.U_IsModel = \'N\'
                and left(ITT1.Father,4) = ?
                GROUP BY ITT1.Father, A3.ItemName, ITT1.Currency
                ORDER BY Left(A3.ItemName, 14), Left(Right(ITT1.Father, 5),3) 
            ', [$request->get('modelo')]);

        //Definimos las columnas 
        $columns = array(
            ["data" => "CODIGO", "name" => "Código"],
            ["data" => "DESCRIPCION", "name" => "Descripción"],
            ["data" => "PC03", "name" => "PC03"],
            ["data" => "PC04", "name" => "PC04"],
            ["data" => "PC05", "name" => "PC05"],
            ["data" => "PC06", "name" => "PC06"],
            ["data" => "PC07", "name" => "PC07"],
            ["data" => "PC08", "name" => "PC08"],
            ["data" => "OTROS", "name" => "OTROS"],
            ["data" => "MONEDA", "name" => "Moneda"]

        );

        return response()->json(array('data' => $consulta, 'columns' => $columns));
    }
    public function DetalleAgrupado(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $modelo = Input::get('pKey');
            $rules = [
                // 'fieldText' => 'required|exists:OITM,ItemCode',
                'pKey' => 'required',
             

            ];
            $customMessages = [
                'pKey.required' => 'Ningun modelo seleccionado',
                
                //'fieldText.exists' => 'El Código no existe.'
            ];
            $valid = Validator::make( $request->all(), $rules, $customMessages);
            
            if ($valid->fails()) {
                return redirect()->back()
                    ->withErrors($valid)
                    ->withInput();
            }
            $model_descr = DB::table('OITM')
                        ->select(DB::raw('left(OITM.FrgnName , charindex(\',\', OITM.FrgnName ) - 1) descr'))
                        ->where('OITM.ItemCode', 'like', $modelo.'-%')
                        ->first();
                      //  dd($model_descr);
            $data = array(
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'modelo' => $modelo,
                'modelo_descr' => $model_descr->descr
            );
            return view('Mod09_Finanzas.detalleAgrupado', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function datatables_simulador_modelos()
    {

        $consulta = DB::select('SELECT OITM.ItemCode 
                , OITM.ItemName AS MODELO
                FROM OITM 
                WHERE OITM.U_IsModel = \'S\' AND OITM.frozenFor= \'N\' AND U_Linea = \'01\'
                ORDER BY OITM.ItemName asc	
            ');

        //Definimos las columnas
        $columns = array(
            ["data" => "ItemCode", "name" => "Código"],
            ["data" => "MODELO", "name" => "Modelo"],
        );

        return response()->json(array('data' => $consulta, 'columns' => $columns, 'pkey' => 'ItemCode'));
    }
    public function datatables_simulador_detalle_agrupado(Request $request)
    {

        $consulta = DB::select('
            Declare @TiCa_CAN decimal(10,4)
            Declare @TiCa_USD decimal(10,4)
            Declare @TiCa_EUR decimal(10,4)

            SELECT TOP 1  @TiCa_CAN = TC_can, @TiCa_USD = TC_usd, @TiCa_EUR = TC_eur
            FROM SIZ_TipoCambio 
            WHERE YEAR(TC_date) = YEAR(GETDATE())

                SELECT SC.COMPONENTE AS CODIGO, SC.DESCRIPCION, MAX(SC.PC03) AS PC03, MAX(SC.PC04) AS PC04
                , MAX(SC.PC05) AS PC05, MAX(SC.PC06) AS PC06, MAX(SC.PC07) AS PC07, MAX(SC.PC08) AS PC08, MAX(SC.OTROS) AS OTRO, SC.MONEDA, 
                COUNT(SC.CATEGORIA) AS AGRUPADOS
                FROM (
                SELECT  Left(ITT1.Father, 7) AS COMPONENTE
                        , Case When Left(Right(ITT1.Father, 5),2) = \'P0\' then Left(Right(ITT1.Father, 5),2) else \'B0\' END AS CATEGORIA
                        , ITT1.Father AS CODIGO
                        , A3.FrgnName AS DESCRIPCION
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P03\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC03
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P04\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC04
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P05\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC05
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P06\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC06
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P07\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC07
                        , Case When Left(Right(ITT1.Father, 5),3) = \'P08\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS PC08
                        , Case When Left(Right(ITT1.Father, 5),2) <> \'P0\' then SUM(ITT1.Quantity * L1.Price * 
                            (Case When ITT1.Currency = \'USD\' then @TiCa_USD When ITT1.Currency = \'CAN\' then @TiCa_CAN When ITT1.Currency = \'EUR\' 
                            then @TiCa_EUR When ITT1.Currency = \'MXP\' then  1 end)) ELSE 0 END AS OTROS
                        , \'MXP\' AS MONEDA	
                FROM ITT1 
                INNER JOIN OITM A3 on ITT1.Father = A3.ItemCode
                INNER JOIN ITM1 L1 on ITT1.Code= L1.ItemCode and L1.PriceList=1 
                WHERE A3.InvntItem = \'Y\' and A3.frozenFor=\'N\' and A3.U_TipoMat = \'PT\' 
                and A3.U_IsModel = \'N\'
                and left(ITT1.Father,4) = ?
                GROUP BY ITT1.Father, A3.FrgnName, ITT1.Currency
                ) SC
                GROUP BY SC.COMPONENTE, SC.DESCRIPCION, SC.MONEDA
                ORDER BY SC.DESCRIPCION		
            ', [$request->get('modelo')]);

        //Definimos las columnas 
        $columns = array(
            ["data" => "CODIGO", "name" => "Código"],
            ["data" => "DESCRIPCION", "name" => "Descripción"],
            ["data" => "PC03", "name" => "PC03"],
            ["data" => "PC04", "name" => "PC04"],
            ["data" => "PC05", "name" => "PC05"],
            ["data" => "PC06", "name" => "PC06"],
            ["data" => "PC07", "name" => "PC07"],
            ["data" => "PC08", "name" => "PC08"],
            ["data" => "MONEDA", "name" => "Moneda"]

        );

        return response()->json(array('data' => $consulta, 'columns' => $columns));
    }
    public function actualizarPrecios(Request $request){
        $articulos = $request->input('articulos');
        $precio_nuevo = $request->input('precio_nuevo');
        $moneda_nueva = ($request->input('moneda_nueva') == '' || $request->input('priceList') == 10) ? 'NOMONEDA' : $request->input('moneda_nueva');
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
            clock('$codigo, $priceList, $precio, $moneda_nueva', $codigo, $priceList, $precio, $moneda_nueva);
            $rs = SAP::updateItemPriceList($codigo, $priceList, $precio, $moneda_nueva); 
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
            $hide_rollout = false;
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
                    $hide_rollout = true;
                    break;
                case '15': //Sistemas
                    $depList = 7;
                    break;
                default:
                    return redirect()->route('home')->withErrors(array('message' => 'El departamento al que pertenece no esta considerado para actualizar precios.'));
                    break;
            } 
            $depListName = DB::table('OPLN')->where('ListNum', $depList)
            ->value('ListName');
            $data = array(        
                'actividades' => $actividades,
                'ultimo' => count($actividades),
                'deplist' => $depList,
                'deplistname' => $depListName,
                'hide_rollout' => $hide_rollout
            );
            return view('Mod04_Materiales.listaPrecios', $data);
        } else {
            return redirect()->route('auth/login');
        }
    }

}