<?php

namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Dompdf\Dompdf;
//excel
use Illuminate\Http\Request;
//DOMPDF
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;

class Mod04_MaterialesController extends Controller
{
public function reporteEntradasAlmacen()
{
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();

     
        $consulta = DB::select(DB::raw("
        SELECT  PDN1.ItemCode, OPDN.DocNum, OPDN.DocDate, OPDN.CardCode, OPDN.CardName,
  PDN1.Price, PDN1.LineTotal, PDN1.VatSum, OPDN.DocCur, PDN1.Dscription,
   OPDN.DocRate, PDN1.WhsCode, PDN1.Quantity, PDN1.NumPerMsr, OPDN.NumAtCard
 FROM   SALOTTO.dbo.OPDN OPDN INNER JOIN SALOTTO.dbo.PDN1 PDN1 ON OPDN.DocEntry=PDN1.DocEntry
 WHERE 
  (OPDN.DocDate>={ts '2018-05-02 00:00:00.000'} AND OPDN.DocDate<={ts '2018-05-28 00:00:00.000'}) AND
  (PDN1.WhsCode=N'AMG-CC' OR PDN1.WhsCode=N'AMG-ST' 
 OR PDN1.WhsCode=N'AMG-FE' OR PDN1.WhsCode=N'AGG-RE'
  OR PDN1.WhsCode=N'AMG-KU' OR PDN1.WhsCode=N'AMP-BL' 
  OR PDN1.WhsCode=N'APG-ST' OR PDN1.WhsCode=N'APG-PA' 
  OR PDN1.WhsCode=N'ATG-ST' OR PDN1.WhsCode=N'ATG-FX' 
  OR PDN1.WhsCode=N'AMP-TR' OR PDN1.WhsCode=N'ARG-ST')
 ORDER BY OPDN.DocNum
        "));
        //dd($consulta);  if {OPDN.DocCur} = 'USD' or {OPDN.DocCur} = 'EUR'
                            //then {OPDN.DocRate}
        Session::put('repentradasalmacen', $consulta);      
        $data = array(
            'data' => $consulta,         
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'db' => DB::getDatabaseName(),          
            'fi' => Input::get('FechIn'),
            'ff' => Input::get('FechaFa')
        );
        return view('Mod04_Materiales.reporteEntradasAlmacen', $data);
    } else {
        return redirect()->route('auth/login');
    }
}
public function two()
{

}

public function PedidosCsvPDF()
{
    $pdf = \PDF::loadView('Mod03_Compras.PedidosPDF', Session::get('OrdenCompra'));
    $pdf->setOptions(['isPhpEnabled' => true]);
    return $pdf->stream('Siz_Orden_Compra' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
}
}