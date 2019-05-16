<?php

/*
 * This file is part of the finecho/logistics-inquiry.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\LogisticsInquiry;

/**
 * Class ServiceProvider
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
        $this->app->singleton(LogisticsInquiry::class, function () {
            return new LogisticsInquiry(config('logistics'));
        });

        $this->app->alias(LogisticsInquiry::class, 'logisticsInquiry');
    }

    public function provides()
    {
        return [LogisticsInquiry::class, 'logisticsInquiry'];
    }
}
