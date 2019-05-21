<?php

/*
 * This file is part of the finecho/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics;

/**
 * Class ServiceProvider.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/logistics.php' => config_path('logistics.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(Logistics::class, function () {
            return new Logistics(config('logistics'));
        });

        $this->app->alias(Logistics::class, 'logistics');
    }

    public function provides()
    {
        return [Logistics::class, 'logistics'];
    }
}
