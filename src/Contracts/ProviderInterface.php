<?php

/*
 * This file is part of the finecho/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Contracts;

/**
 * Interface Resolvable.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
interface ProviderInterface
{
    /**
     * @return string
     */
    public function getProviderName();
}
