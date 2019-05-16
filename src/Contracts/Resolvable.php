<?php

/*
 * This file is part of the finecho/logistics-inquiry.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\LogisticsInquiry\Contracts;

/**
 * Interface Resolvable
 *
 * @author finecho <liuhao25@foxmail.com>
 */
interface Resolvable
{
    /**
     * @param string $no
     *
     * @return array
     */
    public function show($no);

    /**
     * @return string
     */
    public function getProviderName();
}
