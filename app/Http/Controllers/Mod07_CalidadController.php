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
use App\OP;
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
        $hoy = strtotime("now");
        
        if (strtotime(Input::get('Fech_Rev')) > $hoy) {
            Session::flash('error', 'Registro No guardado, La fecha de revisión no puede ser mayor a hoy');
            return response()->redirectTo('home/NUEVO RECHAZO');
        }

    if($request->input('Fech_Recp')<=($request->input('Fech_Rev')))
    {
        $DocItems = DB::table('Siz_Calidad_Rechazos')
        ->where('DocumentoNumero', $request->input('N_Doc'))
        ->where('Borrado', 'N')
        ->get();
        $item = $request->input('Codigo');
        $busqueda = array_where($DocItems, function ($key, $value) use($item) {
            return $value->materialCodigo = $item;
        });
        if (count($busqueda) == 1) {
            
            Session::flash('error', 'Registro No guardado, Ya existe un registro con el mismo "Numero de Fac." y "Código de Material"');
            return response()->redirectTo('home/NUEVO RECHAZO');
        }
        DB::table('Siz_Calidad_Rechazos')->insert(
            [
                //la siguiente linea es para el boton
                //DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                'fechaRecepcion'     =>$request->input('Fech_Recp'),
                'fechaRevision'      =>$request->input('Fech_Rev'),
                'proveedorId'        =>$request->input('Id_prov'),
                'proveedorNombre'    =>$request->input('Proveedor'),
                'materialCodigo'     =>$request->input('Codigo'),
                'materialUM'         =>$request->input('Um'),
                'materialDescripcion'=>$request->input('Material'),
                'cantidadRecibida'   =>$request->input('C_Recibida'),
                'cantidadRevisada'   =>$request->input('C_Revisada'),
                'cantidadAceptada'   =>$request->input('C_Aceptada'),
                'cantidadRechazada'  =>$request->input('C_Rechazada'),
                'DescripcionRechazo' =>$request->input('D_Rechazo'),
                'DocumentoNumero'    =>$request->input('N_Doc'),
                'InspectorNombre'    =>$request->input('Inspector'),
                'Observaciones'      =>$request->input('Observaciones'),
                'Borrado'            =>'N'
            ]
        );
            Session::flash('mensaje', 'Registro Guardado');
          return response()->redirectTo('home/NUEVO RECHAZO');
    } 
else{
    Session::flash('error', 'Registro No guardado, La fecha Revisión es menor a la fecha de Recepción');
    return response()->redirectTo('home/NUEVO RECHAZO');
    
}
}
    public function autocomplete(Request $request)
    {
      
       // dd($data);
       return response()->json(DB::table('OCRD')->where('CardType', 'S')->value('CardName'));
    }

   public function Reporte(){
    if(Auth::check()){
        $user = Auth::user();
        $actividades = $user->getTareas();
        //dd($actividades );
        //aqui va tu qwery
        $Proveedores=  DB::select('SELECT proveedorId, proveedorNombre FROM Siz_Calidad_Rechazos group by proveedorId, proveedorNombre');
    
        $Articulos=  DB::select('SELECT materialCodigo, materialDescripcion FROM Siz_Calidad_Rechazos group by materialCodigo, materialDescripcion');

        return view('Mod07_Calidad.Reporte_Rechazos',['Articulos' => $Articulos,'Proveedores' => $Proveedores,'actividades' => $actividades, 'ultimo' => count($actividades)]);
    }else {
        return  redirect()->route('auth/login');
    }
    
   }
   
    public function Pdf_Rechazo(Request $request)
    {
      // $rechazo=DB::select('SELECT* FROM Siz_Calidad_Rechazos');
       //$pdf = App::make('dompdf');
       $fechaIni = $request->input('FechIn');
       $fechaFin = $request->input('FechaFa');
       $sociedad=DB::table('OADM')->value('CompnyName');
       
    
         $prov1= $request->input('prov');
       if($prov1==null){
        $prov1='';
    }
       $btnradio=$request->input('registro');
       if($btnradio==null){
        $btnradio='0';
    }
       $artic1=$request->input('arti');
       if($artic1==null){
        $artic1='';
    }
    $rechazo=null;
    switch ($btnradio) {
        case 0:
        $rechazo=DB::table('Siz_Calidad_Rechazos')
        ->whereBetween('fechaRevision',[$fechaIni. ' 00:00:00' ,$fechaFin. ' 23:59:59'])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->where('Borrado','N')
        ->get();
            break;
        case 1:
        $rechazo=DB::table('Siz_Calidad_Rechazos')->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->where('cantidadRechazada',">",0)
        ->where('Borrado','N')
        ->get();

            break;
        case 2:
        $rechazo=DB::table('Siz_Calidad_Rechazos')
        ->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->where('cantidadRechazada',0)
        ->where('Borrado','N')
        ->get();

            break;
    }
    $Opc_Document = $request->input('expor');

    if($Opc_Document==1){
            $pdf = \PDF::loadView('Mod07_Calidad.RechazoPDF',compact('sociedad','rechazo','fechaIni','fechaFin'));
            $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true]);
            return $pdf->stream('Siz_Calidad_Reporte_Rechazo.Pdf');

    //dd($rechazo);
            $pdf = \PDF::loadView('Mod07_Calidad.RechazoPDF',['sociedad'=>$sociedad,'rechazo'=>$rechazo,'fechaIni'=>$fechaIni,'fechaFin'=>$fechaFin]);
            return $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true])->stream('Siz_Calidad_Reporte_Rechazo.Pdf');

           // return $pdf->download('ReporteOP.pdf');
        }
        else
        {
            Excel::load('Siz_Calidad_Reporte_Rechazos.xlsx' ,function($excel)use($rechazo) {
               //Header
                $excel->sheet('Hoja1', function($sheet) use($rechazo){

        foreach($rechazo as $R => $Rec) {
            $sheet->row($R+11, [
                "        ",
            date('d/m/Y',strtotime($Rec->fechaRevision)),
             $Rec->proveedorNombre, 
             $Rec->materialCodigo, 
             $Rec->materialDescripcion, 
             $Rec->cantidadRecibida,
             $Rec->cantidadAceptada,
             $Rec->cantidadRechazada,
             $Rec->cantidadRevisada,
             $Rec->cantidadAceptada /$Rec->cantidadRecibida * 100,
             $Rec->InspectorNombre,
             $Rec->DocumentoNumero 
             
    ]);	
                }         
            });
             
            })->export('xlsx');
            
        }
    }



    
    public function Cancelado()
    {
        if(Auth::check()){
            $user = Auth::user();
            $actividades = $user->getTareas();
           return view('Mod07_Calidad.Cancelaciones',['actividades' => $actividades, 'ultimo' => count($actividades)]);
        }else {
            return  redirect()->route('/auth/login');
        }
    }
    public function DataShowCancelaciones(){
         $consulta = DB::select("SELECT * FROM Siz_Calidad_Rechazos where Borrado='N'");
     $consulta = collect($consulta);
            return Datatables::of($consulta)             
                 ->addColumn('action', function ($item) {                     
                      return  '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirma" data-whatever="'.$item->id.'">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>';
                    }
                    )
                ->make(true);
    }
    public function UPT_Cancelado(){    
       // dd(Input::get('code')); 
        DB::table('Siz_Calidad_Rechazos')
         ->where('id', Input::get('code'))
         ->update(['Borrado' => 'S']);
                         
         return redirect()->back();
     }
     public function Historial()
     {
         $user = Auth::user();
         $actividades = $user->getTareas();
         $VerHistorial= DB::select("SELECT * FROM Siz_Calidad_Rechazos where Borrado='S'");
         //$Delfechas=DB::select('SELECT fechaRevision FROM Siz_Calidad_Rechazos');
      //dd($DelRechazo);
      return view('Mod07_Calidad.Historial',['VerHistorial'=>$VerHistorial,'actividades' => $actividades, 'ultimo' => count($actividades)]);
     }
 public function repCalidad2(){
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();      
        //anio_in
       // dd(Input::all());
        $valid = Validator::make(Input::all(), [
            'anio_in' => 'required' ,
            'semana_in' => 'required|unique:Siz_Calidad_Depto,Semana',
            'cor_in'=> 'required',
            'cos_in'=> 'required',
            'coj_in'=> 'required',
            'tap_in'=> 'required',
            'car_in'=> 'required',
        ]);

        if ($valid->fails()) {
            return redirect()->back()
                        ->withErrors($valid)
                        ->withInput();
        }
        DB::table('Siz_Calidad_Depto')
        ->insert(
            [
                'anio'   => Input::get('anio_in'),
                'Semana' => Input::get('semana_in'),
                'CorteIn'=> Input::get('cor_in'),
                'CostIn' => Input::get('cos_in'),
                'CojiIn' => Input::get('coj_in'),
                'TapIn'  => Input::get('tap_in'),
                'CarpIn' => Input::get('car_in'),           
            ]
            );
     
            return redirect()->back();
    } else {
        return redirect()->route('auth/login');
    }
}
public function repCalidad(){
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();      
$Indatos = DB::table('Siz_Calidad_Depto')->orderBy('Semana','asc')->get();

         return view('Mod07_Calidad.CalidadDepto',
             ['actividades' => $actividades,
                 'ultimo' => count($actividades),
                 'enviado' => false,
                 'semana_in' => '',
                 'Indatos' => $Indatos
                ]
             );
    } else {
        return redirect()->route('auth/login');
    }
}

public function showCapturaDefectivos(Request $request){

   //dd($request->get('input-op'));
    if (Auth::check()) {
        $rules = [
             'fieldOtroNumber' => 'required|exists:@CP_OF,U_DocEntry',
           // 'FechIn' => 'required|date|before:tomorrow',
           // 'FechaFa' => 'required|date',              
        ];
        $customMessages = [
            'fieldOtroNumber.required' => 'Ingresa OP',               
            'fieldOtroNumber.exists' => 'OP no encontrada',               
            //'fieldText.exists' => 'El Código no existe.'
        ];

        if ($request->get('input-op') != null && is_numeric($request->get('input-op'))) {
            $op = $request->get('input-op');
        } else {         
            $valid = Validator::make( $request->all(), $rules, $customMessages);
            
            if ($valid->fails()) {
                return redirect()->back()
                    ->withErrors($valid)
                    ->withInput($request->input());
            }
            $op = $request->input('fieldOtroNumber');    
        }
        $user = Auth::user();
        $actividades = $user->getTareas();  
        $data = array(        
            'actividades' => $actividades,
            'ultimo' => count($actividades),
            'op' => $op,
            'descripcion' => OP::getDescripcion($op)         
        );
        return view('Mod07_Calidad.DefectivosCaptura', $data);


    } else {
        return redirect()->route('auth/login');
    }
}

public function showDefectivosTabla(){
     if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();  
        $data = array(        
            'actividades' => $actividades,
            'ultimo' => count($actividades),         
        );
        return view('Mod07_Calidad.DefectivosTabla', $data);
    } else {
        return redirect()->route('auth/login');
    }
}
public function DataShowDefectivosTabla(){
    $consulta = DB::table('SIZ_Calidad_Defectivos')
    ->leftjoin('OUDP', 'OUDP.Code', '=', 'cda_depto')
    ->select('SIZ_Calidad_Defectivos.*',  'OUDP.Name as depto');           
        return Datatables::of($consulta)
            ->addColumn(
                'acciones',
                function ($item) {

                 return    '<button type="button" class="btn btn-primary" id="boton-editar" data-toggle="tooltip"
    data-placement="right" title="Editar"><span class="glyphicon glyphicon-pencil"></span></button>' .
'<button type="button" class="btn btn-danger" id="boton-eliminar" data-toggle="tooltip" data-placement="right"
    title="Eliminar"><span class="glyphicon glyphicon-trash"></span></button>';

                    // '<a href="TRASLADO RECEPCION/solicitud/' . $item->cda_id . '"><i class="fa fa-hand-o-right"></i> </a>';
                }
            )
           ->addColumn(
                'activo',
                function ($item) {
                    return  ($item->cda_activo == 1) ? 'ACTIVO' : 'NO ACTIVO' ;
                }
            )

            ->make(true);
}
public function DataShowDefectivosCaptura(Request $request){
    $consulta = DB::table('SIZ_Calidad_Defectivos_Estadistico')
    ->leftjoin('OUDP', 'OUDP.Code', '=', 'cde_depto')
    ->leftjoin('OHEM as o1', 'o1.U_EmpGiro', '=', 'SIZ_Calidad_Defectivos_Estadistico.cde_operario')  
    ->leftjoin('OHEM as o2', 'o2.U_EmpGiro', '=', 'SIZ_Calidad_Defectivos_Estadistico.cde_inspector')  
    ->select('SIZ_Calidad_Defectivos_Estadistico.*',  'OUDP.Name as depto', DB::raw("(o1.firstName +' '+ o1.lastName) as operario"), DB::raw("(o2.firstName +' '+ o2.lastName) as inspector"))
    ->where('SIZ_Calidad_Defectivos_Estadistico.cde_op', $request->get('op')); 
 //dd($request->get('op'));
        return Datatables::of($consulta)
            ->addColumn(
                'acciones',
                function ($item) {

                 return    '<button type="button" class="btn btn-primary" id="boton-editar" data-toggle="tooltip"
    data-placement="right" title="Editar"><span class="glyphicon glyphicon-pencil"></span></button>' .
'<button type="button" class="btn btn-danger" id="boton-eliminar" data-toggle="tooltip" data-placement="right"
    title="Eliminar"><span class="glyphicon glyphicon-trash"></span></button>';

                    // '<a href="TRASLADO RECEPCION/solicitud/' . $item->cda_id . '"><i class="fa fa-hand-o-right"></i> </a>';
                }
            )         
            ->make(true);
}

   public function combobox(){
       //para Tabla_de_Defectivos
        $deptos = DB::select("select Code as [llave], Name as [valor] from OUDP where Code > 0 order by Name asc");
        return compact('deptos');
    }
   public function comboboxCapturaDefectivos(){        
        $deptos = DB::select("select Code as [llave], Name as [valor] from OUDP where Code > 0 order by Name asc");                                            
        return compact('deptos');
    }
   public function comboboxCapturaDefectivosOperarios(Request $request){        
        $defectos = DB::select("select cda_descripcion as llave, cda_descripcion as valor from SIZ_Calidad_Defectivos where cda_depto = ? and cda_activo = 1 order by cda_descripcion", [$request->get('departamento')]);
        $operarios = DB::select("select U_EmpGiro as [llave] ,firstName + ' ' + lastName as [valor] from OHEM where dept = ? and status = 1 order by firstName", [$request->get('departamento')]);              
        return compact('operarios', 'defectos');
    }

    public function defectivos_addorupdate(){        
        if (array_key_exists ('cda_activo', Input::all())) {
            $act = 1;
        } else {
            $act = 0;
        }
        
        if (Input::get('input-accion') == 'nuevo') {
            DB::table('SIZ_Calidad_Defectivos')
            ->insert([
                'cda_depto' => Input::get('depto'), 
                'cda_descripcion' => Input::get('cda_descripcion'),            
                'cda_pond' => Input::get('cda_pond'),            
                'cda_activo' => $act,            
            ]); 
           
        } else {
            DB::table('SIZ_Calidad_Defectivos')
            ->where('cda_id', Input::get('input-id'))
            ->update([
                'cda_depto' => Input::get('depto'), 
                'cda_descripcion' => Input::get('cda_descripcion'),            
                'cda_pond' => Input::get('cda_pond'),            
                'cda_activo' => $act,            
            ]);
          
        }
        
        Session::flash('mensaje', 'Guardado correctamente');
        return redirect()->back();
    }

    public function capturadefectivos_addorupdate(Request $request){               
       // dd(Input::all());
        if (Input::get('input-accion') == 'nuevo') {
            DB::table('SIZ_Calidad_Defectivos_Estadistico')
            ->insert([
                'cde_cda' => Input::get('descripcion'), 
                'cde_depto' => Input::get('depto'), 
                'cde_op' => Input::get('input-op'),            
                'cde_operario' => Input::get('operario'),            
                'cde_cantidad' => Input::get('cde_cantidad'),            
                'cde_fecha' => Input::get('cde_fecha'),            
                'cde_inspector' => Input::get('cde_inspector'),                                      
            ]); 
           
        }else if (Input::get('input-accion') == 'editar'){
            DB::table('SIZ_Calidad_Defectivos_Estadistico')
            ->where('cde_id', Input::get('input-id'))
            ->update([
                'cde_cda' => Input::get('descripcion'), 
                'cde_depto' => Input::get('depto'),            
                'cde_operario' => Input::get('operario'),            
                'cde_cantidad' => Input::get('cde_cantidad'),            
                'cde_fecha' => Input::get('cde_fecha'),            
                'cde_inspector' => Input::get('cde_inspector'),                                       
            ]);
          
        }
        
        Session::flash('mensaje', 'Guardado correctamente');
        return redirect()->route('defectoscaptura', [$request]);
        
    }

      public function CDA_quitar(){    
        DB::table('SIZ_Calidad_Defectivos')
        ->where('cda_id', Input::get('code'))
        ->delete();
        Session::flash('mensaje', 'Registro eliminado');                 
        return redirect()->back();
     }
      public function CDE_quitar(Request $request){    
        DB::table('SIZ_Calidad_Defectivos_Estadistico')
        ->where('cde_id', Input::get('code'))
        ->delete();
        Session::flash('mensaje', 'Registro eliminado');                 
        return redirect()->route('defectoscaptura', [$request]);
     }
}