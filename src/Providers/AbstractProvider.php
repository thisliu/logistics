<?php

/*
 * This file is part of the finecho/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Providers;

use Finecho\Logistics\Contracts\ProviderInterface;

/**
 * Class Base.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
abstract class AbstractProvider implements ProviderInterface
{
    const DEFAULT_TIMEOUT = 5.0;

    const GLOBAL_SUCCESS_CODE = 200;

    const GLOBAL_SUCCESS_MSG = 'OK';

    /**
     * @var array
     */
    protected $config;

    /**
     * Base constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $logisticsOrder
     *
     * @return \Finecho\Logistics\Order
     */
    abstract protected function mapLogisticsOrderToObject($logisticsOrder);
}
