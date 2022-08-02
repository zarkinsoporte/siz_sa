<?php
namespace App\Http\Controllers;

use DB;
use App;

use Auth;
use Session;
use Datatables;
use Carbon\Carbon;
use Dompdf\Dompdf;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Http\Request;
//excel
use App\Http\Controllers\Controller;
//DOMPDF
use App\Libraries\barcode_generator;
use Maatwebsite\Excel\Facades\Excel;
//use Pdf;
//Fin DOMPDF
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class Mod05_VentasController extends Controller
{
    public function index_codigo_barras()
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $sociedad = DB::table('OADM')->value('CompnyName');
        $data = array(
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'generator' => new barcode_generator(),
            'sociedad' => $sociedad
        );
        return view('Mod05_Ventas.index_codigo_barras', $data);
    }
    public function codigos_barra_xls(){
        $path = public_path() . '/assets/plantillas_excel/Mod_05/SIZ_codibarr.xlsx';
        $data = json_decode(Session::get('codigos_barra'));
        //dd($data);
       
        Excel::load($path, function ($excel) use ($data) {
            $excel->sheet('Detalles', function ($sheet) use ($data) {
                $generator = new barcode_generator();
                $proveedor = DB::table('OADM')
                ->select(DB::raw("CompnyName + ' ' + CompnyAddr AS CompnyAddr"))
                ->value('CompnyAddr');
                
                $index = 7;
                foreach ($data as $row) {
                    
                    $codigoean = self::generateEAN($row->codibarr);
                    //nombre
                    $path = public_path('codibarr/' . $codigoean . ".png");
                    //guardamos la imagen
                    $options = [];
                    $generator->output_image('png', 'ean-13-nopad', $codigoean, $options, $path);

                    $renglon = [
                        $row->ItemCode,
                        $row->ItemName,
                        $proveedor
                    ];
                    
                        $sheet->getRowDimension($index)->setRowHeight(75);
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath($path);                        
                        $objDrawing->setCoordinates('A' . $index);
                        $objDrawing->setWidthAndHeight(140, 125);
                        $objDrawing->setResizeProportional(true);
                        $objDrawing->setWorksheet($sheet);
                        //ponemos un vacio al inicio de la fila:
                        array_unshift($renglon, '');

                    $sheet->row($index, $renglon);
                    $index++;
                }
            });
        })
        ->setFilename('SIZ Codigos de Barra')
        ->export('xlsx');
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
        $proveedor = DB::table('OADM')
        ->select(DB::raw("CompnyName + ' ' + CompnyAddr AS CompnyAddr"))
        ->value('CompnyAddr');
        //return view('Mod05_Ventas.ReporteBarrasPDF', compact('a', 'generator'));
        $pdf = \SPDF::loadView('Mod05_Ventas.ReporteBarrasPDF', compact('a', 'generator', 'proveedor'));
       
        $pdf->setOption('header-html', $headerHtml);
        $pdf->setOption('footer-center', 'Pagina [page] de [toPage]');
        //$pdf->setOption('footer-left', 'SIZ');
        //$pdf->setOption('orientation', 'Landscape');
        $pdf->setOption('margin-top', '25mm');
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
        $codigoean = self::generateEAN($code);
        /* Output directly to standard output. */
        $path = public_path('codibarr/'. $codigoean.".png");
        return $generator->output_image('png', 'ean-13-nopad', $codigoean, $options, $path);
        
             
    }
    
    //la funcion principal de generateEAN esta en AppHelper
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