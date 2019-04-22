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
use Datatables;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod04_MaterialesController extends Controller
{
public function reporteEntradasAlmacen()
{
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();  
        $data = array(        
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
    public function DataShowEntradasMP(Request $request)
    {
        if (Auth::check()) {
            $consulta = DB::select(DB::raw( "
          SELECT * FROM(
            SELECT 'ENTRADA G' as TIPO, PDN1.ItemCode, OPDN.DocNum, OPDN.DocDate, OPDN.CardCode, OPDN.CardName, PDN1.Price, PDN1.LineTotal, PDN1.VatSum, OPDN.DocCur, PDN1.Dscription, OPDN.DocRate, PDN1.WhsCode, PDN1.Quantity, PDN1.NumPerMsr, OPDN.NumAtCard
            FROM   SALOTTO.dbo.OPDN OPDN INNER JOIN dbo.PDN1 PDN1 ON OPDN.DocEntry=PDN1.DocEntry
            WHERE  (CAST( OPDN.DocDate as DATE) 
                        BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            )AND (PDN1.WhsCode=N'AMG-CC' OR PDN1.WhsCode=N'AMG-ST' OR PDN1.WhsCode=N'AMG-FE' OR PDN1.WhsCode=N'AGG-RE' OR PDN1.WhsCode=N'AMG-KU' OR PDN1.WhsCode=N'AMP-BL' OR PDN1.WhsCode=N'APG-ST' OR PDN1.WhsCode=N'APG-PA' OR PDN1.WhsCode=N'ATG-ST' OR PDN1.WhsCode=N'ATG-FX' OR PDN1.WhsCode=N'AMP-TR' OR PDN1.WhsCode=N'ARG-ST')
UNION ALL
            SELECT 'ENTRADA L' as TIPO, PDN1.ItemCode, OPDN.DocNum, OPDN.DocDate, OPDN.CardCode, OPDN.CardName, PDN1.Price, PDN1.LineTotal, PDN1.VatSum, OPDN.DocCur, PDN1.Dscription, OPDN.DocRate, PDN1.WhsCode, PDN1.Quantity, PDN1.NumPerMsr, OPDN.NumAtCard
            FROM   OPDN OPDN INNER JOIN PDN1 PDN1 ON OPDN.DocEntry=PDN1.DocEntry
            WHERE  (CAST( OPDN.DocDate as DATE) 
            BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            ) 
            AND (PDN1.WhsCode IS  NULL  OR  NOT (PDN1.WhsCode=N'AGG-RE' OR PDN1.WhsCode=N'AMG-CC' OR PDN1.WhsCode=N'AMG-FE' OR PDN1.WhsCode=N'AMG-KU' OR PDN1.WhsCode=N'AMG-ST' OR PDN1.WhsCode=N'AMP-BL' OR PDN1.WhsCode=N'AMP-TR' OR PDN1.WhsCode=N'APG-PA' OR PDN1.WhsCode=N'APG-ST' OR PDN1.WhsCode=N'ARG-ST' OR PDN1.WhsCode=N'ATG-FX' OR PDN1.WhsCode=N'ATG-ST'))
UNION ALL
            SELECT 'NOTA CREDITO' as TIPO, RPC1.ItemCode, ORPC.DocNum, ORPC.DocDate, ORPC.CardCode, ORPC.CardName, RPC1.Price, RPC1.LineTotal, RPC1.VatSum, ORPC.DocCur, RPC1.Dscription, ORPC.DocRate, RPC1.WhsCode, RPC1.Quantity, RPC1.NumPerMsr, ORPC.NumAtCard
            FROM   ORPC ORPC INNER JOIN RPC1 RPC1 ON ORPC.DocEntry=RPC1.DocEntry
            WHERE  (CAST( ORPC.DocDate as DATE) 
            BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            ) 
            AND (RPC1.WhsCode IS  NULL  OR  NOT (RPC1.WhsCode=N'AGG-RE' OR RPC1.WhsCode=N'AMG-CC' OR RPC1.WhsCode=N'AMG-FE' OR RPC1.WhsCode=N'AMG-KU' OR RPC1.WhsCode=N'AMG-ST' OR RPC1.WhsCode=N'AMP-BL' OR RPC1.WhsCode=N'AMP-TR' OR RPC1.WhsCode=N'APG-PA' OR RPC1.WhsCode=N'APG-ST' OR RPC1.WhsCode=N'ARG-ST' OR RPC1.WhsCode=N'ATG-FX' OR RPC1.WhsCode=N'ATG-ST'))
UNION ALL
            SELECT 'DEVOLUCION' AS TIPO, RPD1.ItemCode, ORPD.DocNum, ORPD.DocDate, ORPD.CardCode, ORPD.CardName, RPD1.Price, RPD1.LineTotal, RPD1.VatSum, ORPD.DocCur, RPD1.Dscription, ORPD.DocRate, RPD1.WhsCode, RPD1.Quantity, RPD1.NumPerMsr, ORPD.NumAtCard
            FROM   ORPD ORPD INNER JOIN RPD1 RPD1 ON ORPD.DocEntry=RPD1.DocEntry
            WHERE  (CAST( ORPD.DocDate as DATE) 
            BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            ) 
            AND (RPD1.WhsCode IS  NULL  OR  NOT (RPD1.WhsCode=N'AGG-RE' OR RPD1.WhsCode=N'AMG-CC' OR RPD1.WhsCode=N'AMG-FE' OR RPD1.WhsCode=N'AMG-KU' OR RPD1.WhsCode=N'AMG-ST' OR RPD1.WhsCode=N'AMP-BL' OR RPD1.WhsCode=N'AMP-TR' OR RPD1.WhsCode=N'APG-PA' OR RPD1.WhsCode=N'APG-ST' OR RPD1.WhsCode=N'ARG-ST' OR RPD1.WhsCode=N'ATG-FX' OR RPD1.WhsCode=N'ATG-ST'))
            
            ) T
            ORDER BY T.TIPO, T.DocNum, T.DocDate
        "));
        
        $request->session()->put( 'fechas_entradas', array(
                'fi' => $request->get('fi'),
                'ff' => $request->get('ff')
            ));
        
        $consulta = collect($consulta);
            return Datatables::of($consulta)
                ->addColumn('Cant', function ($consulta) {
                    return ($consulta->Quantity * $consulta->NumPerMsr);
                })
                ->addColumn('TotalConIva', function ($consulta) {
                    return ($consulta->LineTotal + $consulta->VatSum);
                })
                
                ->make(true);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function DataShowEntradasMP_GDL(Request $request)
    {
        if (Auth::check()) {
            $consulta = DB::select(DB::raw( "
           SELECT PDN1.ItemCode, OPDN.DocNum, OPDN.DocDate, OPDN.CardCode, OPDN.CardName, PDN1.Price, PDN1.LineTotal, PDN1.VatSum, OPDN.DocCur, PDN1.Dscription, OPDN.DocRate, PDN1.WhsCode, PDN1.Quantity, PDN1.NumPerMsr, OPDN.NumAtCard
            FROM   SALOTTO.dbo.OPDN OPDN INNER JOIN dbo.PDN1 PDN1 ON OPDN.DocEntry=PDN1.DocEntry
            WHERE  (CAST( OPDN.DocDate as DATE) 
                        BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 23:59:59' . "'
            AND (PDN1.WhsCode=N'AMG-CC' OR PDN1.WhsCode=N'AMG-ST' OR PDN1.WhsCode=N'AMG-FE' OR PDN1.WhsCode=N'AGG-RE' OR PDN1.WhsCode=N'AMG-KU' OR PDN1.WhsCode=N'AMP-BL' OR PDN1.WhsCode=N'APG-ST' OR PDN1.WhsCode=N'APG-PA' OR PDN1.WhsCode=N'ATG-ST' OR PDN1.WhsCode=N'ATG-FX' OR PDN1.WhsCode=N'AMP-TR' OR PDN1.WhsCode=N'ARG-ST')
            ORDER BY OPDN.DocNum
        "));
            $consulta = collect($consulta);
            return Datatables::of($consulta)
                ->addColumn('Cant', function ($consulta) {
                    return ($consulta->Quantity * $consulta->NumPerMsr);
                })
                ->addColumn('TotalConIva', function ($consulta) {
                    return ($consulta->LineTotal + $consulta->VatSum);
                })

                ->make(true);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function DataShowDevoluciones(Request $request)
    {
        if (Auth::check()) {
            $consulta = DB::select(DB::raw( "
            SELECT RPD1.ItemCode, ORPD.DocNum, ORPD.DocDate, ORPD.CardCode, ORPD.CardName, RPD1.Price, RPD1.LineTotal, RPD1.VatSum, ORPD.DocCur, RPD1.Dscription, ORPD.DocRate, RPD1.WhsCode, RPD1.Quantity, RPD1.NumPerMsr, ORPD.NumAtCard
            FROM   ORPD ORPD INNER JOIN RPD1 RPD1 ON ORPD.DocEntry=RPD1.DocEntry
            WHERE  (CAST( ORPD.DocDate as DATE) 
            BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 23:59:59' . "'
            ) 
            AND (RPD1.WhsCode IS  NULL  OR  NOT (RPD1.WhsCode=N'AGG-RE' OR RPD1.WhsCode=N'AMG-CC' OR RPD1.WhsCode=N'AMG-FE' OR RPD1.WhsCode=N'AMG-KU' OR RPD1.WhsCode=N'AMG-ST' OR RPD1.WhsCode=N'AMP-BL' OR RPD1.WhsCode=N'AMP-TR' OR RPD1.WhsCode=N'APG-PA' OR RPD1.WhsCode=N'APG-ST' OR RPD1.WhsCode=N'ARG-ST' OR RPD1.WhsCode=N'ATG-FX' OR RPD1.WhsCode=N'ATG-ST'))
            ORDER BY ORPD.DocNum
        "));
            $consulta = collect($consulta);
            return Datatables::of($consulta)
                ->addColumn('Cant', function ($consulta) {
                    return ($consulta->Quantity * $consulta->NumPerMsr);
                })
                ->addColumn('TotalConIva', function ($consulta) {
                    return ($consulta->LineTotal + $consulta->VatSum);
                })

                ->make(true);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function DataShowNotasCredito(Request $request)
    {
        if (Auth::check()) {
            $consulta = DB::select(DB::raw( "
            SELECT RPC1.ItemCode, ORPC.DocNum, ORPC.DocDate, ORPC.CardCode, ORPC.CardName, RPC1.Price, RPC1.LineTotal, RPC1.VatSum, ORPC.DocCur, RPC1.Dscription, ORPC.DocRate, RPC1.WhsCode, RPC1.Quantity, RPC1.NumPerMsr, ORPC.NumAtCard
            FROM   ORPC ORPC INNER JOIN RPC1 RPC1 ON ORPC.DocEntry=RPC1.DocEntry
            WHERE  (CAST( ORPC.DocDate as DATE) 
            BETWEEN '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('d-m-Y', strtotime($request->get('fi'))) . ' 23:59:59' . "'
            ) 
            AND (RPC1.WhsCode IS  NULL  OR  NOT (RPC1.WhsCode=N'AGG-RE' OR RPC1.WhsCode=N'AMG-CC' OR RPC1.WhsCode=N'AMG-FE' OR RPC1.WhsCode=N'AMG-KU' OR RPC1.WhsCode=N'AMG-ST' OR RPC1.WhsCode=N'AMP-BL' OR RPC1.WhsCode=N'AMP-TR' OR RPC1.WhsCode=N'APG-PA' OR RPC1.WhsCode=N'APG-ST' OR RPC1.WhsCode=N'ARG-ST' OR RPC1.WhsCode=N'ATG-FX' OR RPC1.WhsCode=N'ATG-ST'))
            ORDER BY ORPC.DocNum
        "));
            $consulta = collect($consulta);
            return Datatables::of($consulta)
                ->addColumn('Cant', function ($consulta) {
                    return ($consulta->Quantity * $consulta->NumPerMsr);
                })
                ->addColumn('TotalConIva', function ($consulta) {
                    return ($consulta->LineTotal + $consulta->VatSum);
                })

                ->make(true);
        } else {
            return redirect()->route('auth/login');
        }
    }
public function entradasPDF()
{
    $a = json_decode(Session::get('entradas'));
      
        $entradasL = array_filter($a, function ($value) {
            return $value->TIPO == 'ENTRADA L';
        });
        $entradasG = array_filter($a, function ($value) {
            return $value->TIPO == 'ENTRADA G';
        });
        $devoluciones = array_filter($a, function ($value) {
            return $value->TIPO == 'DEVOLUCION';
        });
        $notascredito = array_filter($a, function ($value) {
            return $value->TIPO == 'NOTA CREDITO';
        });
        
    // dd(\AppHelper::instance()->getHumanDate(array_get( Session::get('fechas_entradas'), 'ff')));
    $data = array('notascredito' => $notascredito, 'entradasL' => $entradasL, 'entradasG' => $entradasG, 'devoluciones' => $devoluciones, 'fechas_entradas' => Session::get('fechas_entradas'));
    $pdf = \PDF::loadView('Mod04_Materiales.ReporteEntradasPDF', $data);
        $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);  
    return $pdf->stream('Siz_MP_Almacén' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
}

    public function entradasXLS()
    {
        $path = public_path() . '/assets/plantillas_excel/Mod_01/SIZ_entradas.xlsx';
        $data = json_decode(Session::get('entradas'));
        
       
        
        Excel::load($path, function ($excel) use ($data) {
            $excel->sheet('MP', function ($sheet) use ($data) {

                $sheet->cell('C4', function ($cell) {
                    $cell->setValue(\AppHelper::instance()->getHumanDate(date("Y-m-d H:i:s")));
                });
                $sheet->cell('C5', function ($cell) {
                    $cell->setValue(date("H:i:s"));
                });
                $index = 7;
                foreach ($data as $row) {
                    $sheet->row($index, [
                     $row->DocNum, 
                     $row->TIPO,
                     $row->DocDate,
                     $row->CardCode,
                     $row->CardName,
                     $row->NumAtCard,
                     $row->ItemCode,
                     $row->Dscription,
                     $row->Cant,
                     $row->Price,
                     $row->LineTotal,
                     $row->VatSum,
                     $row->TotalConIva,
                     $row->DocCur
                     
                    ]);
                    $index++;
                }
            });
        })
            ->setFilename('SIZ Reporte de Materia Prima')
            ->export('xlsx');
    }
}