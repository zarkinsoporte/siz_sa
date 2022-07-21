<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\barcode_generator;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;

use Carbon\Carbon;
//excel
use Maatwebsite\Excel\Facades\Excel;
//DOMPDF
use Dompdf\Dompdf;
use App;
//use Pdf;
//Fin DOMPDF
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Datatables;


class Mod05_VentasController extends Controller
{
    public function index_codigo_barras()
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $data = array(
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'generator' => new barcode_generator()
        );
        return view('Mod05_Ventas.index_codigo_barras', $data);
    }
    public function datatables_oitm_index_codigo_barras(Request $request)
    {
        $consulta = DB::select("SELECT ItemCode, ItemName, ValidComm codibarr
        FROM OITM WHERE OITM.ValidComm is not null AND
        isnumeric(OITM.ValidComm) = 1 AND
        OITM.frozenFor = 'N' AND InvntItem = 'Y'");        
        return Datatables::of(collect($consulta))
        ->make(true);
    }
    public function codigo_barras_PDF()
    {
        $a = collect(json_decode(Input::get('data_t')));
       
        $fechaImpresion = date("d-m-Y H:i:s");
        $headerHtml = view()->make(
            'Mod05_Ventas.ReporteBarras_pdfheader',
            [
                'titulo' => 'Codigo de Barras',
                'fechaImpresion' => 'Fecha de Impresión: ' . $fechaImpresion,
            ]
        )->render();
       
        $generator = new barcode_generator();
        //test a vista:
        //return view('Mod05_Ventas.ReporteBarrasPDF', compact('a', 'generator'));
        $pdf = \SPDF::loadView('Mod05_Ventas.ReporteBarrasPDF', compact('a', 'generator'));
       
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
        $pdf->setOption('footer-left', 'SIZ');
        $pdf->setOption('orientation', 'Landscape');
        $pdf->setOption('margin-top', '40mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('page-size', 'Letter');

        $path = public_path('pdf/');
        $fileName =  Auth::user()->U_EmpGiro . '.' . 'pdf';
        $pdf->save($path . '/' . $fileName, true);
        
        return compact('fileName');

    }
    public function test_codibarr($code)   
    {
        $generator = new barcode_generator();
        $options = array();
        $codigo = self::generateEAN($code);
        /* Output directly to standard output. */
        return $generator->output_image('svg', 'ean-13-nopad', self::generateEAN($code), $options);     
    }
    
    public function generateEAN($number)
    {
        $code = str_pad($number, 12, '0');
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit. 
        // loop backwards to make the loop length-agnostic. The same basic functionality 
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--) {
            $sum += (int)$code[$i] * ($weightflag ? 3 : 1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }
    
}