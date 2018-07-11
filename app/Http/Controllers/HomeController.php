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
        $noticias=DB::select(DB::raw("SELECT * FROM Siz_Noticias WHERE Destinatario='$id_user'and Leido='N'"));
        $user = Auth::user();
        $actividades = $user->getTareas();
        return view('homeIndex',   ['actividades' => $actividades,'noticias' => $noticias,'id_user' => $id_user, 'ultimo' => count($actividades)]);
   
    }

    public function UPT_Noticias($id){
     
       DB::table('Siz_Noticias')
        ->where('Id', $id)
        ->update(['Leido' => 'Si']);
        $user = Auth::user();
                
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
    
        $noticias=DB::select(DB::raw("SELECT * FROM Siz_Noticias WHERE Destinatario='$id_user'and Leido='N'"));
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
