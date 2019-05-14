<?php

namespace Finecho\LogisticsInquiry;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Logistics::class, function(){
            return new Logistics(config('services.logistics.app_code'));
        });

        $this->app->alias(Logistics::class, 'logistics');
    }

    public function provides()
    {
        return [Logistics::class, 'logistics'];
    }
}
