<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;


ini_set('max_execution_time', 90);
class Reportes_ProduccionController extends Controller
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

    public function Produccion1(Request $request)
    {

        $enviado = $request->input('send');
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            if ($enviado == 'send') {
                $departamento = $request->input('dep');
                //  $fecha = explode(" - ",$request->input('date_range'));
                //$dt = date('d-m-Y H:i:s');
                $fechaI = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechIn'))));
                $fechaF = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechaFa'))));

                $clientes = DB::select('SELECT CardName from  "CP_ProdTerminada" WHERE  (fecha>=\'' . $fechaI . '\' AND
  fecha<=\'' . $fechaF . '\') AND
 (Name= (\'' . $departamento . '\')  OR Name= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 GROUP BY CardName, fecha, Name');

                $produccion = DB::select('SELECT "CP_ProdTerminada"."orden", "CP_ProdTerminada"."Pedido", "CP_ProdTerminada"."Codigo",
 "CP_ProdTerminada"."modelo", "CP_ProdTerminada"."VS", "CP_ProdTerminada"."fecha",
 "CP_ProdTerminada"."CardName", 
 "CP_ProdTerminada"."Cantidad", "CP_ProdTerminada"."TVS"
 FROM   "CP_ProdTerminada" "CP_ProdTerminada"
 WHERE  ("CP_ProdTerminada"."fecha">=\'' . $fechaI . '\' AND
 "CP_ProdTerminada"."fecha"<=\'' . $fechaF . '\') AND
 ("CP_ProdTerminada"."Name"= (\'' . $departamento . '\')  OR "CP_ProdTerminada"."Name"= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 ORDER BY "CP_ProdTerminada"."CardName", "CP_ProdTerminada"."orden"');

                $result = json_decode(json_encode($produccion), true);
                $finalarray = [];
                foreach ($clientes as $client) {
                    $miarray = array_filter($result, function ($item) use ($client) {
                        return $item['CardName'] == $client->CardName;
                    });
                    $finalarray[$client->CardName] = $miarray;

                }
                //dd(($finalarray['CASTRO HERRERA ALEJANDRO ISAAC'][0]['orden']));
                $values = ['actividades' => $actividades, 'ultimo' => count($actividades), 'ofs' => $finalarray, 'departamento' => $departamento, 'fechaI' => $fechaI, 'fechaF' => $fechaF, 'tvs' => 0, 'cant' => 0];
                Session::flash('Ocultamodal', 1);
                dd($produccion);             
                Session::put('values', $produccion);
                return view('Mod01_Produccion.produccionGeneral', $values);
                $compiled = view('Mod01_Produccion.produccionGeneral', $values)->render();
              
            } else {
                Session::flash('Ocultamodal', false);
                return view('Mod01_Produccion.produccionGeneral', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
            }

        } else {
            return redirect()->route('auth/login');
        }

    }
    public function ReporteProduccionPDF()
    {
        $array = Session::get('values');

        $pdf = \PDF::loadView('Mod01_Produccion.produccionGeneralPDF', compact("array"));
        //$pdf = new FPDF('L', 'mm', 'A4');
        $pdf->setOptions(['isPhpEnabled' => true]);
//        Session::forget('values');
        return $pdf->stream('Siz_Reporte_Produccion ' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }
}
