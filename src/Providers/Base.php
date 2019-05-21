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

use Finecho\Logistics\Contracts\Resolvable;

/**
 * Class Base.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
abstract class Base implements Resolvable
{
    const DEFAULT_TIMEOUT = 5.0;

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
}
