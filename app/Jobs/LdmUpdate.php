<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\SAP;
use Illuminate\Contracts\Bus\SelfHandling;

class LdmUpdate extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SAP::ldmUpdate();
    }
}
