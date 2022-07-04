<?php

namespace App\Providers;

use DB;
use Queue;

use App\User;
use App\Jobs\LdmNotification;
use App\Jobs\ItemPrecioUpdate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //cualquier cambio en archivos de Colas, debemos hacer:
        //php artisan config:cache
        //php artisan queue:restart
        //TAREAS PROGRAMADAS TIENE UN PROCESO PARA EJECUTAR LOS JOBS
        //use en pruebas para arrancar el proceso "php artisan queue:work --daemon"
        //ISW Beto Jimenez
        //se ejecuta despues de cada job 
        //cualquier problema lo podemos detectar en el archivo storage/logs/laravel.log
        Queue::after(function ($connection, $job, $data) {
           
           //verificamos que sean solo jobs de la cola LdmUpdate
           if ($job->getQueue() == 'LdmUpdate') {
                //cuando sea el ultimo job de esta cola procedemos a enviar el correo al usuario del job
                $jobs = DB::select("SELECT queue from jobs
                where queue = 'LdmUpdate'");
                if (count($jobs) == 0) {
                    $datos =  unserialize($data['data']['command']);
                    $user_nomina = $datos->user_nomina;

                    $user = User::find($user_nomina);
                    $email = $user->email.'@zarkin.com';
                    if (strlen($email) > 11) {
                      
                        Mail::send('Emails.Notificacion', [
                            'paraUsuario' => $user->firstName, 
                            'mensaje' => 'Hemos terminado de actualizar las Ldm...'
                        ], function ($msj) use ($email) {
                            $msj->subject('SIZ LDM ACTUALIZACION'); //ASUNTO DEL CORREO
                            $msj->to([$email]); //Correo del destinatario
                        });
                    }
                    DB::table('Siz_Noticias')->insert(
                        [
                            'Autor'=> $user_nomina,
                            'Destinatario' =>$user_nomina, 
                            'Descripcion' => 'Hemos terminado de actualizar las Ldm...',
                            //  'Estacion_Act' => $Est_act,
                            //  'Estacion_Destino' => $Est_ant,
                            //  'Cant_Enviada'=>$cant_r,
                            //  'Nota' => $nota,
                            'Leido' => 'N',
                        ]
                    );
                }
            }
           if ($job->getQueue() == 'ItemPrecioUpdate') {
                //cuando sea el ultimo job de esta cola procedemos a enviar el correo al usuario del job
                $jobs = DB::select("SELECT queue from jobs
                where queue = 'ItemPrecioUpdate'");
                if (count($jobs) <= 1) {
                    $datos =  unserialize($data['data']['command']);
                    Log::warning("countJobs .".count($jobs));
                    Log::warning("priceList .".$datos->priceList);
                    $articulos = DB::select('exec SIZ_SP_ROLLOUT_SIMULADOR_COSTOS ?', [(int)$datos->priceList]);
                    Log::warning("countArts .".count($articulos));
                    if (count($articulos) > 0) {
                        foreach ($articulos as $key => $articulo) {
                            $codigo = $articulo->ItemCode;
                            $precio = $articulo->PRICE_SAVE;
                            $moneda = $articulo->MONEDA;
                            //$rs = SAP::updateItemPriceList($codigo, $priceList - 1, $precio, $moneda); 
                            //clock($codigo, $rs);
                            /* if($rs !== 'ok'){
                                $mensajeErr = 'Error : Art#'.$codigo.', SAP:'.$rs;
                                $mensaje = $mensaje.$mensajeErr;
                            } */
                            //break;
                            $user = Auth::user()->U_EmpGiro;
                            $this->dispatch((new ItemPrecioUpdate($codigo, $priceList - 1, $precio, $moneda, $user))->onQueue('ItemPrecioUpdate'));
                            Log::warning("dispatch ItemPrecioUpdate.".$codigo);
                        }
                    }else {
                        $user_nomina = $datos->user_nomina;

                        $user = User::find($user_nomina);
                        $email = $user->email.'@zarkin.com';
                        if (strlen($email) > 11) {
                        
                            Mail::send('Emails.Notificacion', [
                                'paraUsuario' => $user->firstName, 
                                'mensaje' => 'El proceso ROLLOUT de actualización de precios termino...'
                            ], function ($msj) use ($email) {
                                $msj->subject('SIZ ROLLOUT ACTUALIZACION PRECIOS'); //ASUNTO DEL CORREO
                                $msj->to([$email]); //Correo del destinatario
                            });
                        }
                        DB::table('Siz_Noticias')->insert(
                            [
                                'Autor' => $user_nomina,
                                'Destinatario' => $user_nomina,
                                'Descripcion' => 'El proceso ROLLOUT de actualización de precios termino...',
                                //  'Estacion_Act' => $Est_act,
                                //  'Estacion_Destino' => $Est_ant,
                                //  'Cant_Enviada'=>$cant_r,
                                //  'Nota' => $nota,
                                'Leido' => 'N',
                            ]
                        );
                    }
                }//end count jobs
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
