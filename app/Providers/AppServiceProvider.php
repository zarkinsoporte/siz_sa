<?php

namespace App\Providers;

use Queue;
use App\User;

use App\Jobs\LdmNotification;
use Illuminate\Support\Facades\DB;
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
        Queue::after(function ($connection, $job, $data) {
           //https://stackoverflow.com/questions/42558903/expected-response-code-250-but-got-code-535-with-message-535-5-7-8-username
            if ($job->getQueue() == 'LdmUpdate') {
                # code...
                
                //$this->dispatch(new LdmNotification('Beto', 'alberto.medina@zarkin.com'));
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
                }
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
