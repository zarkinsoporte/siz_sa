<?php

namespace App\Providers;

use DB;
use Queue;
use Auth;

use App\User;
use App\Jobs\LdmNotification;
use App\Jobs\ItemPrecioControl;
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
                            'paraUsuario' => $user->firstName.' '.$user->lastName, 
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
                if (count($jobs) == 0) {
                    $datos =  unserialize($data['data']['command']);
                    
                    dispatch((new ItemPrecioControl($datos->priceList, $datos->user_nomina))->onQueue('ItemPrecioControl')->delay(20));
                    //Log::warning("dispatch ItemPrecioControl.");
                       
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
