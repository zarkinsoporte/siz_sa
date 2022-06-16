<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class LdmNotification extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $nombreUsuario;
    protected $emailUsuario;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($nombreUsuario, $emailUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
        $this->emailUsuario = $emailUsuario;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = DB::select("SELECT queue from jobs
            where queue = 'LdmUpdate'");
        if (count($jobs) == 0) {
            $email = $this->emailUsuario;
            Mail::send('Emails.Notificacion', [
                'paraUsuario' => $this->nombreUsuario, 
                'mensaje' => 'Hemos terminado de actualizar las Ldm...'
            ], function ($msj) use ($email) {
                $msj->subject('SIZ LDM ACTUALIZACION'); //ASUNTO DEL CORREO
                $msj->to([$email]); //Correo del destinatario
            });
        }
    }
}
