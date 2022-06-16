<?php

namespace App\Providers;

use Queue;
use Illuminate\Support\ServiceProvider;
use App\Jobs\LdmNotification;
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
            clock($connection, $job, $data, $job->getName());
            //$this->dispatch(new LdmNotification('Beto', 'alberto.medina@zarkin.com'));
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
