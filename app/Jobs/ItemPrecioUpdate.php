<?php

namespace App\Jobs;

use App\SAP;
use App\Jobs\Job;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemPrecioUpdate extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $codigo;
    protected $precio;
    protected $moneda;
    public $priceList;
    public $user_nomina;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($codigo, $priceList, $precio,$moneda, $user_nomina)
    {
        $this->codigo = $codigo;
        $this->priceList = $priceList;
        $this->precio = $precio;
        $this->moneda = $moneda;
        $this->user_nomina = $user_nomina;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobs = DB::select("SELECT queue from jobs
            where queue = 'stop'");
        if (count($jobs) > 0) {
           //DB::delete("delete jobs where queue = 'ItemPrecioUpdate' OR queue = 'ItemPrecioControl'");
        } else {
            SAP::updateItemPriceList($this->codigo, $this->priceList -1, $this->precio, $this->moneda);
        }
    }
}
