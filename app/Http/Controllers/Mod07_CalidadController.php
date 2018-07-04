<?php
namespace App\Http\Controllers;
use App\Grupo;
use App\Modelos\MOD01\MENU_ITEM;
use App\Modelos\MOD01\MODULOS_SIZ;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\User;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
//DOMPDF
use Dompdf\Dompdf;
use App;
//use Pdf;
//Fin DOMPDF
use Illuminate\Support\Facades\Route;

use Datatables;
class Mod07_CalidadController extends Controller
{
    public function Rechazo()
   {
    //$var =  DB::table('OCRD')->where('CardType', 'S')->whereNotNull('CardName')->lists('CardName','CardCode');
    //list($CodeP, $NameP) = array_divide($var);
    $resultProveedores =  DB::select('SELECT CardName, CardCode FROM OCRD WHERE CardType = \'S\' AND CardName IS NOT NULL');
    $CardNames = array_pluck($resultProveedores, 'CardName');
    $CardCodes = array_pluck($resultProveedores, 'CardCode');
    //dd($var);
    //DB::table('OITM')->lists('ItemName','ItemCode');
    //list($CodeMat,$NameM)=array_divide($Material);
    // $CodeMat2=array_map('strval', $CodeMat);
    $resultMateriales = DB::select('SELECT ItemCode, ItemName, InvntryUom AS UM FROM OITM WHERE PrchseItem = \'Y\' AND InvntItem = \'Y\'');
    $ItemNames = array_pluck($resultMateriales, 'ItemName');
    $ItemCodes = array_pluck($resultMateriales, 'ItemCode');
    
    $user = Auth::user();
    $actividades = $user->getTareas();
    //dd($actividades );
    return view('Mod07_Calidad.rechazosNuevo',['Material'=>$resultMateriales,'CodeMat'=>$ItemCodes,'NameM'=>$ItemNames,'CodeP'=>$CardCodes,'NameP'=>$CardNames,'var'=>$resultProveedores,'actividades' => $actividades, 'ultimo' => count($actividades)]);
   }

public function RechazoIn(Request $request)   
    {
      //  dd($request->input('Inspector'));
        DB::table('SIZ_Calidad_Rechazos')->insert(
            [
                'fechaRevision'      =>$request->input('Fech_Rev'),
                'fechaRecepcion'     =>$request->input('Fech_Recp'),
                'proveedorId'        =>$request->input('Id_prov'),
                'proveedorNombre'    =>$request->input('Proveedor'),
                'materialCodigo'     =>$request->input('Codigo'),
                'materialUM'         =>$request->input('Um'),
                'materialDescripcion'=>$request->input('Material'),
                'cantidadRevisada'   =>$request->input('C_Revisada'),
                'cantidadAceptada'   =>$request->input('C_Aceptada'),
                'cantidadRechazada'  =>$request->input('C_Rechazada'),
                'DescripcionRechazo' =>$request->input('D_Rechazo'),
                'DocumentoNumero'    =>$request->input('N_Doc'),
                'InspectorNombre'    =>$request->input('Inspector'),
                'Observaciones'      =>$request->input('Observaciones'),
        
            ]
        );
            Session::flash('mensaje', 'Registro Guardado');
          return response()->redirectTo('home/NUEVO RECHAZO');
    }
    public function autocomplete(Request $request)
    {
      
       // dd($data);
       return response()->json(DB::table('OCRD')->where('CardType', 'S')->value('CardName'));
    }

   public function Reporte(){
   
    $user = Auth::user();
    $actividades = $user->getTareas();
    //dd($actividades );
    return view('Mod07_Calidad.Reporte_Rechazos',['actividades' => $actividades, 'ultimo' => count($actividades)]);
   
   }
   
    public function Pdf_Rechazo(Request $request)
    {
      // $rechazo=DB::select('SELECT* FROM SIZ_Calidad_Rechazos');
       //$pdf = App::make('dompdf');
       $fechaIni = $request->input('FechIn');
       $fechaFin = $request->input('FechaFa');
       $rechazo=DB::table('SIZ_Calidad_Rechazos')->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])->get();

       //dd($rechazo);
            $pdf = \PDF::loadView('Mod07_Calidad.RechazoPDF',['rechazo'=>$rechazo,'fechaIni'=>$fechaIni,'fechaFin'=>$fechaFin]);
            return $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true])->stream();
           // return $pdf->download('ReporteOP.pdf');
       
    }

}