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
class Mod02_PlaneacionController extends Controller
{
public function reporteMRP()
{
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();
       
        $fechauser = Input::get('text_selUno');
        $tipo = Input::get('text_selDos');
       
        
        // if ($accion == 'Actualizar') {//se ejecuta la actualizacion de la tabla
        //     DB::update("exec SIZ_MRP");
        // }
        $f = DB::table('SIZ_T_MRP')->first();// obtener fecha de ultima actualizacion
        if(isset($f)){
             $Text = 'Actualizado el: ' . \AppHelper::instance()->getHumanDate_format($f->fechaDeEjecucion, 'h:i A');
        }else{
             $Text = 'Sin datos';
        }
        $data = array(        
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'text' => $Text,
            'fechauser' => $fechauser,
            'tipo' => $tipo
        );
        return view('Mod02_Planeacion.reporteMRP', $data);
    } else {
        return redirect()->route('auth/login');
    }
}

public function actualizaMRP(){
        DB::update("exec SIZ_MRP");
        
        Session::flash('mensaje', "MRP actualizado...");
        
        return redirect('home/MRP');
}
    public function DataShowMRP(Request $request)
    {
        if (Auth::check()) {
        $fecha = $request->get('fechauser');
        $tipo = $request->get('tipo');
        

         //   $fecha = Input::get('text_selUno');
         //   $tipo = Input::get('text_selDos');
         //   $accion = Input::get('text_selTres');  
         $consulta = '';
        switch ($tipo) {
            case 'Completo': 
               $tipo = '%';
                break;
            case 'Proyección':
                $tipo = 'P';
                break;
            case 'Con Orden':
                $tipo = 'C';
                break;
        }
        switch ($fecha) {
            case 'Producción':
                    $consulta = DB::table('SIZ_T_MRP')
                        ->select(DB::raw('fechaDeEjecucion, Descr, Itemcode, ItemName, UM, ExistGDL, ExistLERMA, WIP, sum(S0) S0, sum(S1)S1, sum(S2)S2, sum(S3)S3, sum(S4)S4, sum(S5)S5, sum(S6)S6, sum(S7)S7, sum(S8)S8, sum(S9)S9, sum(S10)S10, sum(S11)S11, sum(S2)S12, sum(S13)S13, sum(S14)S14, sum(S15)S15, sum(S16)S16, sum(S17)S17, sum(S18)S18, sum(S19)S19, sum(necesidadTotal)necesidadTotal, OC, Reorden, Minimo, Maximo, TE, Costo,Proveedor, Comprador'))
                        ->where('U_C_Orden', 'like', $tipo)
                        ->groupBy( "fechaDeEjecucion", 'Descr', 'Itemcode', 'ItemName', 'UM', 'ExistGDL', 'ExistLERMA', 'WIP', 'Costo', 'Proveedor', 'Comprador', 'Reorden', 'Maximo', 'Minimo', 'TE', 'OC');
      
                break;
            case 'Compras':
                    $consulta = DB::table('SIZ_T_MRP')
                        ->select(DB::raw( 'fechaDeEjecucion, Descr, Itemcode, ItemName, UM, ExistGDL, ExistLERMA, WIP, sum(Sc0) S0, sum(Sc1)S1, sum(Sc2)S2, sum(Sc3)S3, sum(Sc4)S4, sum(Sc5)S5, sum(Sc6)S6, sum(Sc7)S7, sum(Sc8)S8, sum(Sc9)S9, sum(Sc10)S10, sum(Sc11)S11, sum(Sc2)S12, sum(Sc13)S13, sum(Sc14)S14, sum(Sc15)S15, sum(Sc16)S16, sum(Sc17)S17, sum(Sc18)S18, sum(Sc19)S19, sum(necesidadTotal)necesidadTotal, OC, Reorden, Minimo, Maximo, TE, Costo,Proveedor, Comprador'))
                        ->where('U_C_Orden', 'like', $tipo)
                        ->groupBy( "fechaDeEjecucion", 'Descr', 'Itemcode', 'ItemName', 'UM', 'ExistGDL', 'ExistLERMA', 'WIP', 'Costo', 'Proveedor', 'Comprador', 'Reorden', 'Maximo', 'Minimo', 'TE', 'OC');
          
                break;
        }
            return Datatables::of($consulta)
                ->addColumn('Resto', function ($consulta) {
                    return ($consulta->S13 + $consulta->S14 + $consulta->S15 + $consulta->S16 + $consulta->S17 + $consulta->S18 + $consulta->S19);
                })
                ->addColumn('Necesidad', function ($consulta) {
                    return ($consulta->ExistGDL + $consulta->ExistLERMA) - $consulta->necesidadTotal;
                })
                ->make(true);
        } else {
            return redirect()->route('auth/login');
        }
    }
   
    public function mrpPDF() 
    {
        $data = json_decode(Session::get('mrp'));
      //  dd($data);
        $pdf = \PDF::loadView('Mod02_Planeacion.ReporteMrpPDF', compact('data'));
        $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);  
        return $pdf->stream('Siz_MRP' . ' - ' . date("d/m/Y") . '.Pdf');
    }

    public function mrpXLS()
    {
        $path = public_path() . '/assets/plantillas_excel/Mod_02/SIZ_mrps.xlsx';
        $data = json_decode(Session::get('mrp'));
        
       // dd( Session::get('mrp'));
                
        Excel::load($path, function ($excel) use ($data) {
            $excel->sheet('General', function ($sheet) use ($data ) {

                $sheet->cell('A4', function ($cell) {
                    $cell->setValue("Fecha de Impresión: ".\AppHelper::instance()->getHumanDate(date("Y-m-d H:i:s")).' '. date("H:i:s"));
                });
                
                $fecha = \Carbon\Carbon::now();
                $sheet->row(6, [
                    "Grupo",
                                "Código",
                                "Descripción",
                                "UM",
                                "Exist. Gdl",

                                "Exist. Lerma",
                                "WIP",
                                "Anterior",
                                "Sem-".$fecha->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                "Sem-".$fecha->addWeek(1)->weekOfYear,
                                
                                "Resto",
                                "Necesidad",
                                "Disp. S/WIP",
                                "OC",
                                "P. Reorden",
                                "S. Minimo",
                                "S. Maximo",
                                "T.E.",
                                "Costo C",
                                "Proveedor",
                                "Comprador"
                ]);

                $index = 7;
                foreach ($data as $row) {
                    if ($index == 7) {
                        $sheet->cell('A5', function ($cell) use ($row){
                            $cell->setValue('Fecha de Actualización: ' . \AppHelper::instance()->getHumanDate( $row->fechaDeEjecucion));
                     });
                    }
                    $sheet->row($index, [
                     $row->Descr, 
                     $row->Itemcode,
                     $row->ItemName,
                     $row->UM,
                     number_format($row->ExistGDL, '2', '.', ','),
                     number_format($row->ExistLERMA, '2', '.', ','),
                     number_format($row->WIP, '2', '.', ','),
                     number_format($row->S0, '2', '.', ','),
                     number_format($row->S1, '2', '.', ','),
                     number_format($row->S2, '2', '.', ','),
                     number_format($row->S3, '2', '.', ','),
                     number_format($row->S4, '2', '.', ','),
                     number_format($row->S5, '2', '.', ','),
                     number_format($row->S6, '2', '.', ','),
                     number_format($row->S7, '2', '.', ','),
                     number_format($row->S8, '2', '.', ','),
                     number_format($row->S9, '2', '.', ','),
                     number_format($row->S10, '2', '.', ','),
                     number_format($row->S11, '2', '.', ','),
                     number_format($row->S12, '2', '.', ','),
                     number_format($row->Resto, '2', '.', ','),
                     number_format($row->necesidadTotal, '2', '.', ','),
                     number_format($row->Necesidad, '2', '.', ','),
                     number_format($row->OC, '2', '.', ','),
                     number_format($row->Reorden, '2', '.', ','),
                     number_format($row->Minimo, '2', '.', ','),
                     number_format($row->Maximo, '2', '.', ','),
                     $row->TE,
                     $row->Costo,
                     $row->Proveedor,
                     $row->Comprador,
                    
                    ]);
                    $index++;
                }
            });
        })
            ->setFilename('SIZ Resumen de MRP')
            ->export('xlsx');
    }
}