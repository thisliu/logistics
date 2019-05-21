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
 * Class Logistics.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Logistics
{
    protected $factory;

    public function __construct(array $config)
    {
        $this->factory = new Factory($config);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     *
     * @throws \Finecho\Logistics\Exceptions\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        return $this->factory->make($name, $arguments);
    }
}
