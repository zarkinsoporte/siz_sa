<?php

namespace App\Http\Controllers;
use DateTime;
use App\Modelos\MOD01\LOGOT;
use App\User;
use App\OP;
use App\Modelos\MOD01\LOGOF;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
use Dompdf\Dompdf;
use App;
use Mail;
class Mod01_ProduccionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('Mod00_Administrador.admin');
    }

    public function estacionSiguiente( Request $request)
    {
       // echo OP::getEstacionSiguiente("81143");
      // dd(OP::getStatus("81143"));
      // dd(OP::getRuta("81143"));
    }

    public function ReporteOpPDF($op)
    {
        //$pdf = App::make('dompdf.wrapper');
        $GraficaOrden = DB::select( DB::raw("SELECT [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOT].U_FechaHora)) AS FechaI,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)) AS FechaF ,OHEM.firstName + ' ' + OHEM.lastName AS Empleado, [@CP_LOGOF].U_DocEntry  ,OWOR.ItemCode , OITM.ItemName ,
        SUM([@CP_LOGOF].U_Cantidad) AS U_CANTIDAD,
        (oitm.U_VS ) AS VS,(SELECT CompnyName FROM OADM) AS CompanyName     
       
        FROM [@CP_LOGOF] inner join [@PL_RUTAS] ON [@CP_LOGOF].U_CT = [@PL_RUTAS].Code
        left join OHEM ON [@CP_LOGOF].U_idEmpleado = OHEM.empID
        inner join [@CP_LOGOT] ON [@CP_LOGOF].U_DocEntry = [@CP_LOGOT].U_OP and [@CP_LOGOf].U_CT = [@CP_LOGOT].U_CT 
        inner join OWOR ON [@CP_LOGOF].U_DocEntry = OWOR.DocNum
        inner join OITM ON OWOR.ItemCode = OITM.ItemCode
        WHERE U_DocEntry = $op 
        GROUP BY [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOT].U_FechaHora)) ,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)) ,
        OHEM.firstName + ' ' + OHEM.lastName , [@CP_LOGOF].U_DocEntry  ,OWOR.ItemCode , OITM.ItemName ,
        oitm.U_VS
        ORDER BY [@CP_LOGOF].U_CT") );
        //dd($GraficaOrden);
    
        $data=array(
                    'data' => $GraficaOrden,
                    'op'   => $op
                   );
        //$data = $GraficaOrden;
        
        $pdf = \PDF::loadView('Mod01_Produccion.ReporteOpPDF', $data);
        //dd($pdf);
        return $pdf->stream();
       // return $pdf->download('ReporteOP.pdf');
    }

    public function ReporteMaterialesPDF($op)
    {
        //$pdf = App::make('dompdf.wrapper');
        $Materiales = DB::select( DB::raw("SELECT b.DocNum AS DocNumOf, 
        '*' + CAST(b.DocNum as varchar (50)) + '*' as CodBarras,
        b.ItemCode, 
        c.ItemName, 
        c.U_VS AS VS, 
        d.CardCode, 
        d.CardName, 
        d.DocNum AS DocNumP, 
        b.DueDate AS FechaEntrega, 
        b.plannedqty, 
        d.Comments as Comentario, 
        b.Comments, 
        c.UserText, 
        f.InvntryUom,
        --f.U_estacion as CodEstacion,
         '*' + cast(f.u_estacion as varchar (3)) + '*' as BarrEstacion, 
        ISNULL((SELECT Name FROM [@PL_RUTAS] WHERE Code=f.U_Estacion),'Sin Estacion') AS Estacion,
        a.ItemCode AS Codigo, 
        f.ItemName as Descripcion, 
        a.PlannedQty AS Cantidad, 
        0 AS [Cant. Entregada], 
        0 AS [Cant. Devolución],
        --g.Father,
        b.U_NoSerie,
        f.U_Metodo,
        b.U_OF as origen,
        (SELECT TOP 1 ItemName FROM OITM INNER JOIN OWOR ON OITM.ITEMCODE = OWOR.ItemCode  WHERE OWOR.DocNum = b.U_OF ) as Funda            
    FROM (WOR1 a
         INNER JOIN OWOR b ON a.DocEntry=b.DocEntry
         INNER JOIN OITM c ON b.ItemCode=c.ItemCode
         INNER JOIN ORDR d ON b.OriginAbs=d.DocEntry
         INNER JOIN OITM f ON a.ItemCode=f.ItemCode)
         --inner join ITT1 g on a.ItemCode  = g.Code and b.ItemCode = g.Father  
    WHERE a.DocEntry=CONVERT(Int,$op) 
       AND NOT (f.InvntItem='N' AND f.SellItem='N' AND f.PrchseItem='N' AND f.AssetItem='N')
       AND f.ItemName  not like  '%Gast%'
    ORDER BY CONVERT(INT, f.U_Estacion)") );
        //dd($Materiales);
    
        $data=array(
                    'data' => $Materiales,
                    'op'   => $op
                   );
        //$data = $GraficaOrden;
        
        $pdf = \PDF::loadView('Mod01_Produccion.ReporteMaterialesPDF', $data);
        //dd($pdf);
        //return $pdf->stream();
        return $pdf->stream('ReporteMateriales.pdf');
    }

    public function allUsers(Request $request){
        $users = DB::select('select * from view_Plantilla_SIZ');
        $users = $this->arrayPaginator($users, $request);
        

        return view('Mod00_Administrador.usuarios', compact('users'));
    }

    public function arrayPaginator($array, $request)
    {
        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
    public function usuariosActivos( Request $request)
    {
        $users = User::name($request->get('name'))->where('jobTitle', '<>' , 'Z BAJA')->where('status', '1')
            ->orderBy('Ohem.dept, OHEM.jobTitle, ohem.firstname', 'asc')
           ;
        dd($users);
        return view('Mod00_Administrador.usuarios', compact('users'));
    }

    public function cambiopassword(){
        try {
            $password = Hash::make(Input::get('password'));
            DB::table('dbo.OHEM')
                ->where('U_EmpGiro',Input::get('userId') )
                ->update(['U_CP_Password' => $password]);
        } catch(\Exception $e) {
            return redirect()->back()->withErrors(array('msg' => $e->getMessage()));
        }
        $user = User::find(Input::get('userId'));
        Session::flash('mensaje', 'La contraseña de '.$user->firstName.' '.$user->lastName.' ha cambiado.');
        return redirect()->back();
    }

    public function traslados(Request $request)
    {
        $enviado = $request->input('send');
//dd($request->input('miusuario'));

        if (Auth::check()){
            $user = Auth::user();
            $actividades = $user->getTareas();
        if  ($enviado == 'send')
        {
            if (strlen($request->input('miusuario')) == null){
                $t_user =  Auth::user();
            }else{
                $t_user = User::find($request->input('miusuario'));
                if ($t_user == null){
                    return redirect()->back()->withErrors(array('message' => 'Error, el usuario no existe.'));
                }
            }

            if (Hash::check($request->input('pass'), $t_user->U_CP_Password)) {
                Session::flash('usertraslados', 1);
                return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades), 't_user' => $t_user]);
            }else{
                return redirect()->back()->withErrors(array('message' => 'Error de autenticación.'));
            }

        }else{

            Session::flash('usertraslados', false);
        }

          return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
            return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
        }else{
            return redirect()->route('auth/login');
        }

    }
    public function getOP($id)
    {
        if (Session::has('op')) {
            $op = Session::get('op');
            Session::forget('op');
        }else if (Input::has('op'))
        {
            $op = Input::get('op');
        }else{
            return redirect()->route('home');
        }

        $t_user = User::find($id);
        if ($t_user == null) {
            return redirect()->back()->withErrors(array('message' => 'Error, el usuario no existe.'));
        }

        $user = Auth::user();
        $actividades = $user->getTareas();
        Session::flash('usertraslados', 2);  //evita que salga el modal


        $Codes = OP::where('U_DocEntry',$op)->get();

        if (count($Codes) > 0){
            $index = 0;
        foreach ($Codes as $code) {

if ($code->U_Recibido > $code->U_Procesado){



           // dd($code->U_Recibido);
            if ($code->U_Recibido == '0' && $code->U_Procesado == '0' && $code->U_Entregado == '0'){
           $cantlogof = DB::table('@CP_LOGOF')
               ->where('U_DocEntry', $code->U_DocEntry)
               ->get();

           //dd($cantlogof);

                if (count($cantlogof) == 0){
                    $CantOrden = DB::table('OWOR')
                        ->where('DocEntry', $code->U_DocEntry)
                        ->first();

                   // dd($CantOrden->PlannedQty);
                    $code->U_Recibido = (int) $CantOrden->PlannedQty;
                    $code->save();
                }

            }
 //dd($code);
            $index = $index + 1;
            $EstacionA = OP::getEstacionActual($code->Code);
            $EstacionS = OP::getEstacionSiguiente($code->Code, 1);
           // dd($EstacionA);
            $pedido ='';
           if ($EstacionA != null && $EstacionS != null){
            $order = DB::table('OWOR')
                ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                ->select(DB::raw($EstacionA.' AS U_CT_ACT'), DB::raw($EstacionS . ' AS U_CT_SIG'), DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                    'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                    'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                ->where('@CP_OF.Code', $code->Code)->get();
            if ($index == 1) {
                $one = DB::table('OWOR')
                    ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                    ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                    ->select(DB::raw($EstacionA. ' AS U_CT_ACT'), DB::raw($EstacionS . ' AS U_CT_SIG'),
                        DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                        'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                        'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                    ->where('@CP_OF.Code', $code->Code)->get();
                foreach ($one as $o) {
                    $pedido = $o->OriginNum;
                }
            } else {

                $one = array_merge($one, $order); //$one->merge($order);
                //dd($one);
            }

        }else{
            return redirect()->back()->withErrors(array('message' => 'La orden no tiene ruta en SAP.'));   
        }

        }
        }
        //  $order = OP::find('492418');
        //return $one;


        // $actual = OP::getEstacionActual(Input::get('op'));
        // $siguiente = OP::getEstacionSiguiente(Input::get('op'));


        //Comienza el código para graficar 
        $GraficaOrden = DB::select( DB::raw("SELECT [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOT].U_FechaHora)) AS FechaI,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)) AS FechaF ,OHEM.firstName + ' ' + OHEM.lastName AS Empleado, [@CP_LOGOF].U_DocEntry  ,OWOR.ItemCode , OITM.ItemName ,
        SUM([@CP_LOGOF].U_Cantidad) AS U_CANTIDAD,
        (oitm.U_VS ) AS VS,      
        (SELECT CompnyName FROM OADM ) AS CompanyName
        FROM [@CP_LOGOF] inner join [@PL_RUTAS] ON [@CP_LOGOF].U_CT = [@PL_RUTAS].Code
        left join OHEM ON [@CP_LOGOF].U_idEmpleado = OHEM.empID
        left join Sof_Tiempos  ON [@CP_LOGOF].U_DocEntry = Sof_Tiempos.DocNum and [@CP_LOGOF].U_CT = Sof_Tiempos.U_idRuta    
        inner join [@CP_LOGOT] ON [@CP_LOGOF].U_DocEntry = [@CP_LOGOT].U_OP and [@CP_LOGOf].U_CT = [@CP_LOGOT].U_CT 
        inner join OWOR ON [@CP_LOGOF].U_DocEntry = OWOR.DocNum
        inner join OITM ON OWOR.ItemCode = OITM.ItemCode
        WHERE U_DocEntry = $op 
        GROUP BY [@CP_LOGOF].U_idEmpleado, [@CP_LOGOF].U_CT ,[@PL_RUTAS].NAME,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOT].U_FechaHora)) ,
        DATEADD(dd, 0, DATEDIFF(dd, 0, [@CP_LOGOF].U_FechaHora)) ,
        OHEM.firstName + ' ' + OHEM.lastName , [@CP_LOGOF].U_DocEntry  ,OWOR.ItemCode , OITM.ItemName ,
        oitm.U_VS
        ORDER BY FechaI,[@CP_LOGOF].U_CT") );
        //dd($GraficaOrden);
        $cont = count($GraficaOrden);
        $stocksTable = Lava::DataTable();
        $stocksTable->addDateColumn('Day of Month')
            //->addNumberColumn('Projected')
            ->addNumberColumn('Estación')
            ->addRoleColumn('string', 'tooltip',[
                'html' => true
            ]);

            foreach($GraficaOrden as $campo){
                $date = date_create($campo->FechaI);

                $nom_emp = $campo->Empleado;
                if($nom_emp==NULL){
                    $users = DB::table('OHEM')->where('U_EmpGiro', '=', $campo->U_idEmpleado)
                    ->select('OHEM.lastName', 'OHEM.firstName')
                    ->first();
                    $nom_emp=$users->firstName.' '.$users->lastName;
                }
                
                $stocksTable->addRow([
                   $campo->FechaI, $campo->U_CT, '<p style=margin:10px><b>'.
                   ucwords(strtolower($nom_emp)).
                   '</b><br>Estación:<b>'.
                   $campo->NAME.
                   '</b><br>C. Recibida:<b>'.
                   $campo->U_CANTIDAD.
                   '</b><br>Fecha:<b>'.
                   date_format($date,'d/m/Y').
                   '</b></p>'
                 ]);
             }
            
             
            //  foreach($GraficaOrden as $campo){
            //     $campo->U_CT;       
            //  }

        $HisOrden = Lava::AreaChart('HisOrden', $stocksTable, [
            'title' => 'Historial por OP',
            'interpolateNulls'   => true,
            'pointsVisible' => true,
            'legend' => [
                'position' => 'top'
            ], 
            'tooltip'=> [
                'isHtml' => true
            ], 
        ]);

        ////RUTA RETROCESO
        $Ruta = OP::getRutaNombres($op);

        return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades),'Ruta'=>$Ruta,'t_user' => $t_user, 'ofs' => $one, 'op' => $op, 'pedido' => $pedido, 'HisOrden' => $HisOrden]);

    }
        return redirect()->back()->withErrors(array('message' => 'La OP '.Input::get('op').' no existe.'));


    }

    public function avanzarOP(){


        try{

            $id = Input::get('userId');

            $t_user = User::find($id);
            if ($t_user == null) {
                return redirect()->back()->withInput()->withErrors(array('message' => 'Error, el usuario no existe.'));

            }
            $Cant_procesar = Input::get('cant');
            $Code_actual = OP::find(Input::get('code'));
            Session::put('op', $Code_actual->U_DocEntry);
//dd($Code_actual);
            $U_CT_siguiente = OP::getEstacionSiguiente($Code_actual->Code, 2);

            if ($U_CT_siguiente == $Code_actual->U_CT){
                Session::flash('info', 'La estacion '.OP::getEstacionSiguiente($Code_actual->Code, 1).' es la ultima');
                return redirect()->back();
            }

            //  $cant_pendiente = $Code_actual->U_Recibido - $Code_actual->U_Procesado;
// ->where(DB::raw('(U_Recibido - U_Procesado)', '>', '0'))
            $Code_siguiente =  OP::where('U_CT', $U_CT_siguiente)
                ->where('U_DocEntry', $Code_actual->U_DocEntry)
                ->where('U_Reproceso', 'N')
                ->get();

            $dt = date('Y-m-d H:i:s');
            $CantOrden = DB::table('OWOR')
                ->where('DocEntry', $Code_actual->U_DocEntry)
                ->first();
            $cantO = (int)$CantOrden->PlannedQty;
            //dd($Code_siguiente);
            if (count($Code_siguiente) == 1){
               
                $Code_siguiente =  OP::where('U_CT', $U_CT_siguiente)
                    ->where('U_DocEntry', $Code_actual->U_DocEntry)
                    ->where('U_Reproceso', 'N')
                    ->first();
               // dd( ($Cant_procesar + $Code_siguiente->U_Recibido) <= (Input::get('numcant')+$Code_actual->U_Procesado));


                if ( ($Cant_procesar + $Code_siguiente->U_Recibido) <= $cantO){

                    $Code_siguiente->U_Recibido = $Code_siguiente->U_Recibido + $Cant_procesar;
                    $Code_siguiente->save();

                }else{
                    return redirect()->back()->withInput()->withErrors(array('message' => 'La cantidad total recibida no debe ser mayor a la cantidad de la Orden.'));
                }
            }else if(count($Code_siguiente) == 0){
                try{
                    //esta linea obtiene el consecutivo del numero 
                    $consecutivo =  DB::select('select max (CONVERT(INT,Code)) as Code from [@CP_Of]');
//aqui acaba num consecutivo
                    $newCode = new OP();
                    $newCode->Code = ((int)$consecutivo[0]->Code)+1;
                    $newCode->Name = ((int)$consecutivo[0]->Code)+1;
                    $newCode->U_DocEntry = $Code_actual->U_DocEntry;
                    $newCode->U_CT = $U_CT_siguiente;
                    $newCode->U_Entregado = 0;
                    $newCode->U_Orden = $U_CT_siguiente;
                    $newCode->U_Procesado = 0;
                    $newCode->U_Recibido = $Cant_procesar;
                    $newCode->U_Reproceso = "N";
                    $newCode->U_Defectuoso = 0;
                    $newCode->U_Comentarios = "";
                    $newCode->U_CTCalidad = 0;
                    $newCode->save();
                   //save=insert select max (CONVERT(INT,Code)) as Code
                    $consecutivologot =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                    $lot = new LOGOT();
                    $lot->Code = ((int)$consecutivologot[0]->Code)+1;
                    $lot->Name = ((int)$consecutivologot[0]->Code)+1;
                    $lot->U_idEmpleado = $id;
                    $lot->U_CT = $Code_actual->U_CT;
                    $lot->U_Status = "O";
                    $lot->U_FechaHora = $dt;
                    $lot->U_OP = $Code_actual->U_DocEntry;
                    $lot->save();

                }catch (Exception $e)
                {
                    return redirect()->back()->withInput()->withErrors(array('message' => 'Error al guardar nuevo registro en CP_OF.'));
                }
            }
            else{
                return redirect()->back()->withInput()->withErrors(array('message' => 'Existen registros duplicados en la siguiente estación.'));
            }
            // dd(count($Code_siguiente));
            $Code_actual->U_Procesado = $Code_actual->U_Procesado + $Cant_procesar;
            $Code_actual->U_Entregado = $Code_actual->U_Entregado + $Cant_procesar;


            $consecutivologof =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
            
            $log = new LOGOF();
            $log->Code = ((int)$consecutivologof[0]->Code)+1;
            $log->Name = ((int)$consecutivologof[0]->Code)+1;
            $log->U_idEmpleado = $id;
            $log->U_CT = $Code_actual->U_CT;
            $log->U_Status = "T";
            $log->U_FechaHora = $dt;
            $log->U_DocEntry = $Code_actual->U_DocEntry;
            $log->U_Cantidad = $Cant_procesar;
            $log->U_Reproceso = 'N';

            $Code_actual->save();
            $log->save();
       // dd($cantO);

            Session::flash('mensaje', 'El usuario '.$id.' avanzo '.$Cant_procesar.' pza(s) a la estación '.OP::getEstacionSiguiente($Code_actual->Code, 1));

            if ($Code_actual->U_Recibido > 0 && $cantO == $Code_actual->U_Procesado){
                $lineaActual = OP::find($Code_actual->Code);   //si esta linea ya termino de procesar_todo entonces se borra
                $lineaActual->delete();
            }



            return redirect()->back()->withInput();
        }catch (Exception $e){
            return redirect()->back()->withInput()->withErrors(array('message' => 'Error al Guardar la Orden.'));
        }

        //eliminar linea procesada completa de CP_OF
        //creacion de linea  en CP_LOGOF y CP_LOGOT

    }
    public function MethodGET_OP($id)
    {

        if (Session::has('op')) {
           $op = Session::get('op');
           Session::forget('op');
        }else if (Input::has('op'))
        {
$op = Input::get('op');
        }else{
            return redirect()->route('home');
        }

        $t_user = User::find($id);
        if ($t_user == null) {
            return redirect()->back()->withErrors(array('message' => 'Error, el usuario no existe.'));
        }

        $user = Auth::user();
        $actividades = $user->getTareas();
        Session::flash('usertraslados', 2);  //evita que salga el modal


        $Codes = OP::where('U_DocEntry', Input::get('op'))->get();
        if (count($Codes) > 0){
            $index = 0;
            foreach ($Codes as $code) {
                $index = $index + 1;
                $order = DB::table('OWOR')
                    ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                    ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                    ->select(DB::raw(OP::getEstacionActual($code->Code) . ' AS U_CT_ACT'), DB::raw(OP::getEstacionSiguiente($code->Code, 1) . ' AS U_CT_SIG'), DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                        'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                        'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                    ->where('@CP_OF.Code', $code->Code)->get();
                if ($index == 1) {
                    $one = DB::table('OWOR')
                        ->leftJoin('OITM', 'OITM.ItemCode', '=', 'OWOR.ItemCode')
                        ->leftJoin('@CP_OF', '@CP_OF.U_DocEntry', '=', 'OWOR.DocEntry')
                        ->select(DB::raw(OP::getEstacionActual($code->Code) . ' AS U_CT_ACT'), DB::raw(OP::getEstacionSiguiente($code->Code, 1) . ' AS U_CT_SIG'),
                            DB::raw(OP::avanzarEstacion($code->Code, $t_user->U_CP_CT) . ' AS avanzar'),
                            'OWOR.DocEntry', '@CP_OF.Code', '@CP_OF.U_Orden', 'OWOR.Status', 'OWOR.OriginNum', 'OITM.ItemName', '@CP_OF.U_Reproceso',
                            'OWOR.PlannedQty', '@CP_OF.U_Recibido', '@CP_OF.U_Procesado')
                        ->where('@CP_OF.Code', $code->Code)->get();
                    foreach ($one as $o) {
                        $pedido = $o->OriginNum;
                    }
                } else {

                    $one = array_merge($one, $order); //$one->merge($order);
                    //dd($one);
                }
            }
            //  $order = OP::find('492418');
            //return $one;


            // $actual = OP::getEstacionActual(Input::get('op'));
            // $siguiente = OP::getEstacionSiguiente(Input::get('op'));

            $stocksTable = Lava::DataTable();
            $stocksTable->addDateColumn('Day of Month')
                ->addNumberColumn('Projected')
                ->addNumberColumn('Official');
    
            // Random Data For Example
            for ($a = 1; $a < 30; $a++) {
                $stocksTable->addRow([
                    '2015-10-' . $a, rand(800,1000), rand(800,1000)
                ]);
            }
    
            $beto = Lava::AreaChart('beto', $stocksTable, [
                'title' => 'Population Growth',
                'legend' => [
                    'position' => 'in'
                ]
            ]);
            
            return view('Mod01_Produccion.traslados', ['actividades' => $actividades, 'ultimo' => count($actividades),'t_user' => $t_user, 'ofs' => $one, 'op' => $op, 'pedido' => $pedido, 'beto' => $beto]);

        }
        return redirect()->back()->withErrors(array('message' => 'La OP '.Input::get('op').' no existe.'));

    } 
    public function Retroceso(Request $request)
   
    {
     DB::transaction(function () use($request){
        $Est_act = $request->input('Estacion');
        $Est_ant = $request->input('selectestaciones');
        $No_Nomina= $request->input('Nomina');
        $nota = $request->input('nota');
        $Nom_User=$request->input('Nombre');
        $autorizo=$request->input('Autorizar');
        $orden=$request->input('orden');
        $cant_r=$request->input('cant');            
       $reason=$request->input('reason');
       $leido='N';
       $dt = date('Ymd h:m:s'); 
//-------------Notificaciones--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

//$Not_us=DB::select(DB::raw("SELECT top 1 U_EmpGiro,firstname,lastname from OHEM where position='4'and dept ='$cod_dep'"));
$N_Emp  = User::where('position', 4)->where('U_CP_CT','like', '%'.$Est_ant.'%' )->first();
//$N_Emp  = $Not_us[0];
//dd($supervisor);
DB::table('Noticias')->insert(
    [
     'Autor'=>$Nom_User,
     'Destinatario' =>$N_Emp->U_EmpGiro,
     'N_Empleado'=>$No_Nomina,
     'No_Orden'=>$orden,
     'Descripcion' => $reason,
     'Estacion_Act' => $Est_act,
     'Estacion_Destino' => $Est_ant,
     'Cant_Enviada'=>$cant_r,
     'Nota' => $nota,
     'Leido' => $leido,
     'Fecha_Reproceso'=>$dt,
     'Reproceso_Autorizado'=>$autorizo

    ]
);
    //---------Estacion Destino-----------------------------------------------------------------------------------------------------------------------------------------------------//
     $DestinoCp = OP::where('U_DocEntry', $orden)->where('U_CT', $Est_ant)->first();
     $boolvar = $DestinoCp!=NULL;
    
      if($boolvar){
      //  dd('update '.$DestinoCp);
        DB::table('@CP_OF')
        ->where('Code', $DestinoCp->Code)
        ->update([
        //  'U_Recibido'=> $DestinoCp->U_Recibido + $cantidad,
            //'U_Reproceso'=>'S',
            'U_Defectuoso'=>$cant_r + $DestinoCp->U_Defectuoso,
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
            $Nuevo_reproceso->U_CT=$Est_ant;
            $Nuevo_reproceso->U_Entregado=0;
            $Nuevo_reproceso->U_Orden=$Est_ant;
            $Nuevo_reproceso->U_Procesado=0;
            $Nuevo_reproceso->U_Recibido= $cant_r;
            $Nuevo_reproceso->U_Reproceso="S";
            $Nuevo_reproceso->U_Defectuoso=$cant_r;
            $Nuevo_reproceso->U_Comentarios=$nota;
            $Nuevo_reproceso->U_CTCalidad=0;
            $Nuevo_reproceso->save();
    //-------- Tabla Logot----//

    $Con_Loguot =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOT]');
                    $cot = new LOGOT();
                    $cot->Code = ((int)$Con_Loguot[0]->Code)+1;
                    $cot->Name = ((int)$Con_Loguot[0]->Code)+1;
                    $cot->U_idEmpleado=$No_Nomina;
                    $cot->U_CT = $Est_ant;
                    $cot->U_Status = "O";
                    $cot->U_FechaHora = $dt;
                    $cot->U_OP =$orden;
                   $cot->save();
   }  
    //---------Estacion Actual-----------------------------------------------------------------------------------------------------------------------------------------------------//

$Actual_Cp = OP::where('U_DocEntry', $orden)->where('U_CT', $Est_act)->first();
$Actual=$Actual_Cp->U_Recibido;
//dd($Actual_Cp);

if($Actual==$cant_r){
   $Actual_Cp->delete();
}
if($Actual_Cp->PlannedQty > $cant_r){
    DB::table('@CP_OF')
    ->where('Code', $Actual_Cp->Code)
    ->update([
   'U_Recibido'=> $Actual_Cp->U_Recibido - $cant_r,
        ]);
        $OrdenDest = OP::find($DestinoCp->Code);
        if($boolval && $OrdenDest->U_Reproceso == 'N'){         
          $OrdenDest->U_Procesado = $OrdenDest->U_Procesado - $cant_r;
          $OrdenDest->U_Reproceso ='S';
          $OrdenDest->save();
        }
        if($boolval && $OrdenDest->U_Reproceso == 'S'){         
            $OrdenDest->U_Recibido = $OrdenDest->U_Recibido + $cant_r;
            $OrdenDest->save();
          }
}

   //-------------Tabla LOGOF-----------------------------//
   //$Code_actual = OP::find(Input::get('code'));

   
//dd(Input::get('code'));
                                    
           
   
            //---------Count Cantidades negativas  /_(○_○)-/-----------------------------------------------------------------------------------------------------------------------------------------------------//
        $estaciones = OP::getRuta($orden);
        foreach($estaciones as $estacion){
            if($estacion >= $Est_ant && $estacion < $Est_act ){
                $Con_Logof =  DB::select('select max (CONVERT(INT,Code)) as Code FROM  [@CP_LOGOF]');
                $log = new LOGOF();
                $log->Code = ((int)$Con_Logof[0]->Code)+1;
                $log->Name = ((int)$Con_Logof[0]->Code)+1;
                $log->U_idEmpleado = $No_Nomina;
                $log->U_CT =$estacion;
                $log->U_Status = "T";
                $log->U_FechaHora = $dt;
                $log->U_DocEntry = $orden;
                $log->U_Cantidad = $cant_r*-1;
                $log->U_Reproceso = 'S';    
                //$Code_actual->save();
                $log->save();
            }}
  //--------------------correo-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
  $Num_Nominas=DB::select(DB::raw("SELECT No_Nomina from Email_SIZ where Reprocesos='1'"));
  foreach ($Num_Nominas as $Num_Nomina) {
  $user= User::find($Num_Nomina->No_Nomina);
  
  $correo  = utf8_encode ($user['email'].'@zarkin.com');
  
  Mail::send('Emails.Reprocesos',[ 'autorizo'=>$autorizo,'dt'=> date('d/M/Y h:m:s'),
  'No_Nomina'=>$No_Nomina ,'Nom_User'=>$Nom_User,'orden'=>$orden,
  'cant_r'=>$cant_r,'Est_act'=>$Est_act,'Est_ant'=>$Est_ant,'reason'=>$reason,'nota'=>$nota,'leido'=>$leido],function($msj) use($correo){
  $msj-> subject  ('Notificaciones SIZ');//ASUNTO DEL CORREO
  $msj-> to($correo);//Correo del destinatario  
  });
  Session::flash('info', 'El mensaje fue enviado a  ' .$N_Emp->firstname. ' ' .$N_Emp->lastname.' No.Nomina:  '.$N_Emp->U_EmpGiro.'');
  }
        
//-----------------Refresca a la vista-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
 
 return redirect('/');
  // return view('Emails.Reprocesos', ['dt'=>$dt,'No_Nomina' => $No_Nomina,'Leido' => $leido,'reason'=>$reason,'cant_r'=>$cant_r,'orden'=>$orden,'Nom_User'=>$Nom_User,'Num_Nomina'=>$Num_Nomina,'user'=>$user,'Est_act'=>$Est_act,'Est_ant'=>$Est_ant,'nota'=>$nota]); 
});        
     }

}