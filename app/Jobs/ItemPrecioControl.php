<?php

namespace App\Jobs;

use DB;
use Auth;
use Cache;
use App\User;
use App\Jobs\Job;
use Carbon\Carbon;
use App\Jobs\ItemPrecioUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemPrecioControl extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    public $priceList;
    public $user_nomina;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($priceList, $user_nomina)
    {
        $this->priceList = $priceList;
        $this->user_nomina = $user_nomina;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        $articulos = DB::select('exec SIZ_SP_ROLLOUT_SIMULADOR_COSTOS ?',
        [(int)$this->priceList]);
        //Log::warning("countArts .".count($articulos));
        if (count($articulos) > 0) {
            foreach ($articulos as $key => $articulo) {
                $codigo = $articulo->ItemCode;
                $precio = $articulo->PRICE_SAVE;
                $moneda = $articulo->MONEDA;
                //$rs = SAP::updateItemPriceList($codigo, $priceList , $precio, $moneda); 
                //clock($codigo, $rs);
                /* if($rs !== 'ok'){
                    $mensajeErr = 'Error : Art#'.$codigo.', SAP:'.$rs;
                    $mensaje = $mensaje.$mensajeErr;
                } */
                //break;
                dispatch((new ItemPrecioUpdate($codigo, $this->priceList, $precio, $moneda, $this->user_nomina))->onQueue('ItemPrecioUpdate'));
                //Log::warning("dispatch ItemPrecioUpdate.".$codigo);
            }
        }else {
            //$user_nomina = $datos->user_nomina; 
            
            $fecha_inicial = Carbon::parse(Cache::get('hora_init_rollout'));
            // Log::warning("fecha_inicial");
            // Log::warning($fecha_inicial);
            $fecha_final = Carbon::now();
            // Log::warning("fecha_final");
            // Log::warning($fecha_final);
            $tiempo_proceso = $fecha_final->diff($fecha_inicial)->format('%H:%I:%S');
            //$tiempo_proceso = $fecha_inicial->diffInMinutes($fecha_final);
            // Log::warning("dispatch .".$tiempo_proceso);
            $user = User::find($this->user_nomina);
            $email = $user->email.'@zarkin.com';
            if (strlen($email) > 11) {
            
                Mail::send('Emails.Notificacion', [
                    'paraUsuario' => $user->firstName.' '.$user->lastName,
                    'mensaje' => 'El proceso ROLLOUT de actualización de precios de 
                    LISTA #'.$this->priceList.' finalizó <br> 
                    Duración (h:m:s): '. $tiempo_proceso. ', Inicio: '. $fecha_inicial
                ], function ($msj) use ($email) {
                    $msj->subject('SIZ ROLLOUT ACTUALIZACION PRECIOS'); //ASUNTO DEL CORREO
                    $msj->to([$email]); //Correo del destinatario
                });
            }
            DB::table('Siz_Noticias')->insert(
                [
                    'Autor' => $this->user_nomina,
                    'Destinatario' => $this->user_nomina,
                    'Descripcion' => 'El proceso ROLLOUT de actualización de precios de 
                    LISTA #' . $this->priceList . ' finalizó <br> 
                    Duración (h:m:s): '. $tiempo_proceso. ', Inicio: '. $fecha_inicial,
                    //  'Estacion_Act' => $Est_act,
                    //  'Estacion_Destino' => $Est_ant,
                    //  'Cant_Enviada'=>$cant_r,
                    //  'Nota' => $nota,
                    'Leido' => 'N',
                ]
            );            
        }   
    }
}
