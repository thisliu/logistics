<?php

/*
 * This file is part of the finehco/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Exceptions;

/**
 * Class InquiryErrorException.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class InquiryErrorException extends Exception
{
    /**
     * @var array
     */
    public $raw = [];

    /**
     * InquiryErrorException constructor.
     *
     * @param string $message
     * @param int    $code
     * @param array  $raw
     */
    public function __construct($message, $code, array $raw = [])
    {
        parent::__construct($message, intval($code));

        $this->raw = $raw;
    }
}
