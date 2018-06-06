<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\LOGOT;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use App\OP;
use App\User;
use Mail;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $actividades = $user->getTareas();
       // dd($id_not);
        $id_user=Auth::user()->U_EmpGiro;
        $noticias=DB::select(DB::raw("SELECT * FROM Noticias WHERE Destinatario='$id_user'and Leido='N'"));
        $user = Auth::user();
        $actividades = $user->getTareas();
        return view('homeIndex',   ['actividades' => $actividades,'noticias' => $noticias,'id_user' => $id_user, 'ultimo' => count($actividades)]);
   
    }

    public function UPT_Noticias($id){
     
       DB::table('noticias')
        ->where('Id', $id)
        ->update(['Leido' => 'Si']);
        $user = Auth::user();
        //dd($user->U_EmpGiro);
        //--------------------correo-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
        $DCorreo=DB::select(DB::raw("SELECT * from Noticias Where Id=$id"));
        $Demail=$DCorreo[0];
        $autor=$Demail->Autor;
        $Nomina=$Demail->N_Empleado;
        $orden=$Demail->No_Orden;
        $cantidad=$Demail->Cant_Enviada;
        $est_Act=$Demail->Estacion_Act;
        $est_Ant=$Demail->Estacion_Destino;
        $Descripcion=$Demail->Descripcion;
        $Nota=$Demail->Nota;
        $Leido=$Demail->Leido;
        $dt = date('d-m-Y H:i:s');
        
        //--------------------correo-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

        $Num_Nominas=DB::select(DB::raw("SELECT No_Nomina from Email_SIZ where Reprocesos='1'"));
        foreach ($Num_Nominas as $Num_Nomina) {
        $user= User::find($Num_Nomina->No_Nomina);
        $correo  = utf8_encode ('"'.$user['email'].'"'.'@zarkin.com');
       Mail::send('Emails.Reprocesos',['dt'=>$dt,'Nomina'=>$Nomina,'autor'=>$autor,'orden'=>$orden,'cantidad'=>$cantidad,'est_Act'=>$est_Act,'est_Ant'=>$est_Ant,'Descripcion'=>$Descripcion,'Nota'=>$Nota,'Leido'=>$Leido],function($msj) use($correo){
        $msj-> subject  ('Bienvenido a las notificaciones Zarkin');//ASUNTO DEL CORREO
         $msj-> to($correo);//Correo del destinatario  
        });
        }
    //---------Estacion Destino-----------------------------------------------------------------------------------------------------------------------------------------------------//
     $DestinoCp = OP::where('U_DocEntry', $orden)->where('U_CT', $est_Ant)->first();
     $boolvar = $DestinoCp!=NULL;
    
      if($boolvar){
      //  dd('update '.$DestinoCp);
        DB::table('@CP_OF')
        ->where('Code', $DestinoCp->Code)
        ->update([
        //  'U_Recibido'=> $DestinoCp->U_Recibido + $cantidad,
            //'U_Reproceso'=>'S',
            'U_Defectuoso'=>$cantidad + $DestinoCp->U_Defectuoso,
            'U_Comentarios'=>$Nota,
          //  'U_Procesado'=>$DestinoCp->U_Procesado - $cantidad;
            ]);
      }
      else{
      //  dd('insert '.$DestinoCp);
        $N_Code =  DB::select('select max (CONVERT(INT,Code)) as Code from [@CP_OF]');

            $Nuevo_reproceso = new OP();
            $Nuevo_reproceso->Code=((int)$N_Code[0]->Code)+1;
            $Nuevo_reproceso->Name=((int)$N_Code[0]->Code)+1;
            $Nuevo_reproceso->U_DocEntry=$orden;
            $Nuevo_reproceso->U_CT=$est_Ant;
            $Nuevo_reproceso->U_Entregado=0;
            $Nuevo_reproceso->U_Orden=$est_Ant;
            $Nuevo_reproceso->U_Procesado=0;
            $Nuevo_reproceso->U_Recibido= $cantidad;
            $Nuevo_reproceso->U_Reproceso="S";
            $Nuevo_reproceso->U_Defectuoso=$cantidad;
            $Nuevo_reproceso->U_Comentarios=$Nota;
            $Nuevo_reproceso->U_CTCalidad=0;
            $Nuevo_reproceso->save();
    //-------- Tabla Logot----//

    $Con_Loguot =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                    $cot = new LOGOT();
                    $cot->Code = ((int)$Con_Loguot[0]->Code)+1;
                    $cot->Name = ((int)$Con_Loguot[0]->Code)+1;
                    $cot->U_idEmpleado=$Nomina;
                    $cot->U_CT = $est_Ant;
                    $cot->U_Status = "O";
                    $cot->U_FechaHora = $dt;
                    $cot->U_OP =$orden;
                   $cot->save();
   }  
    //---------Estacion Actual-----------------------------------------------------------------------------------------------------------------------------------------------------//

$Actual_Cp = OP::where('U_DocEntry', $orden)->where('U_CT', $est_Act)->first();
$Actual=$Actual_Cp->U_Recibido;
//dd($Actual_Cp);

if($Actual==$cantidad){
   $Actual_Cp->delete();
}
if($Actual_Cp->PlannedQty > $cantidad){
    DB::table('@CP_OF')
    ->where('Code', $Actual_Cp->Code)
    ->update([
   'U_Recibido'=> $Actual_Cp->U_Recibido - $cantidad,
        ]);
        $OrdenDest = OP::find($DestinoCp->Code);
        if($boolval && $OrdenDest->U_Reproceso == 'N'){         
          $OrdenDest->U_Procesado = $OrdenDest->U_Procesado - $cantidad;
          $OrdenDest->U_Reproceso ='S';
          $OrdenDest->save();
        }
        if($boolval && $OrdenDest->U_Reproceso == 'S'){         
            $OrdenDest->U_Recibido = $OrdenDest->U_Recibido + $cantidad;
            $OrdenDest->save();
          }
       

}

   //-------------Tabla LOGOF-----------------------------//
   //$Code_actual = OP::find(Input::get('code'));

   
//dd(Input::get('code'));
                                    
           
   
            //---------Count Cantidades negativas  /_(○_○)-/-----------------------------------------------------------------------------------------------------------------------------------------------------//
        $estaciones = OP::getRuta($orden);
        foreach($estaciones as $estacion){
            if($estacion >= $est_Ant && $estacion < $est_Act ){
                $Con_Logof =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                $log = new LOGOF();
                $log->Code = ((int)$Con_Logof[0]->Code)+1;
                $log->Name = ((int)$Con_Logof[0]->Code)+1;
                $log->U_idEmpleado = $Nomina;
                $log->U_CT =$estacion;
                $log->U_Status = "T";
                $log->U_FechaHora = $dt;
                $log->U_DocEntry = $orden;
                $log->U_Cantidad = $cantidad*-1;
                $log->U_Reproceso = 'S';    
                //$Code_actual->save();
                $log->save();
            }
        }
    
            
       return redirect('/');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
*/
    public function create(Request $request)
    { 
        $user = Auth::user();
        $actividades = $user->getTareas();
        $id_noticia=$request->input("id");
      //dd($id_noticia);

        $id_user=Auth::user()->U_EmpGiro;
    
        $noticias=DB::select(DB::raw("SELECT * FROM Noticias WHERE Destinatario='$id_user'and Leido='N'"));
        //dd
      return view('Mod01_Produccion/Noticias', ['actividades' => $actividades,'noticias' => $noticias,'id_user' => $id_user, 'ultimo' => count($actividades)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
