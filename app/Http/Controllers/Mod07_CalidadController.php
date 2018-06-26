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
    return view('Mod07_Calidad.rechazoNuevo',compact('mensaje'));
   }

public function RechazoIn(Request $request)   
    {
        DB::table('SIZ_Calidad_Rechazos')->insert(
            [
                'fechaRevision'      =>$request->input('Fech_Rev'),
                'fechaRecepcion'     =>$request->input('Fech_Recp'),
                'proveedorId'        =>$request->input('Id_prov'),
                'proveedorNombre'    =>$request->input('Proveedor'),
                'materialCodigo'     =>$request->input('Codigo'),
                'materialIUM'        =>$request->input('ium'),
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
            Session::flash('mensaje', '');
            return redirect('Mod07_Calidad/RechazosNuevo'); 
    }

   /* public function Mod_Rechazo($Id_Autor,$Mod_mensaje)
    {
        $Mod_Noti = DB::table('SIZ_Calidad_Rechazos')
            ->select('SIZ_Calidad_Rechazos.*')
            ->where('Id', '=',"folio")
            ->get();

        return view('Mod07_Calidad/RechazosNuevo', compact('Mod_Noti', 'Mod_mensaje')); 
    }
    public function Mod_RechazoUPDT(Request $request)
    {
           //dd($request->input('Id_Autor'));
           $folio=$request->input('folio');
           $M_noti = DB::table('SIZ_Calidad_Rechazos')
            ->where("Id", "=", "$folio")
            ->update(
                        [
                            'fechaRevision'      =>$request->input('Fech_Rev'),
                            'fechaRecepcion'     =>$request->input('Fech_Recp'),
                            'proveedorId'        =>$request->input('Id_prov'),
                            'proveedorNombre'    =>$request->input('Proveedor'),
                            'materialCodigo'     =>$request->input('Codigo'),
                            'materialIUM'        =>$request->input('ium'),
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
    Session::flash('info', '');
    return redirect('Mod07_Calidad/RechazosNuevo'); 
    }
*/
   
     public function Delete_Rechazo($folio){
      $eliminar = DB::table('SIZ_Calidad_Rechazos')->where('Id', '=', $folio)->delete();
    
        Session::flash('info', '');
        return redirect()->back();
    }
}