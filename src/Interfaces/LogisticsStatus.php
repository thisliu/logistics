<?php

/*
 * This file is part of the finecho/logistics
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Interfaces;

interface LogisticsStatus
{
    const LOGISTICS_STATUS_ERROR = -1;

    const LOGISTICS_STATUS_NO_RECORD = 0;

    const LOGISTICS_STATUS_COURIER_RECEIPT = 1;

    const LOGISTICS_STATUS_IN_TRANSIT = 2;

    const LOGISTICS_STATUS_DELIVERING = 3;

    const LOGISTICS_STATUS_SIGNED = 4;

    const LOGISTICS_STATUS_DELIVERY_FAILED = 5;

    const LOGISTICS_STATUS_TROUBLESOME = 6;

    const LOGISTICS_STATUS_RETURN_RECEIPT = 7;

    const LOGISTICS_STATUS_REJECTED = 8;

    const LOGISTICS_STATUS_SEND_BACK = 9;

    const LOGISTICS_STATUS_TIMEOUT = 10;

    const LOGISTICS_STATUS_AWAIT_SIGN = 11;

    const LOGISTICS_STATUS_TO_BE_CLEARED = 12;

    const LOGISTICS_STATUS_CLEARANCE = 13;

    const LOGISTICS_STATUS_CLEARED = 14;

    const LOGISTICS_STATUS_CUSTOMS_CLEARANCE_ABNORMALITY = 15;

    const LOGISTICS_STATUS_LABELS = [
        self::LOGISTICS_STATUS_ERROR => '异常',
        self::LOGISTICS_STATUS_NO_RECORD => '无状态',
        self::LOGISTICS_STATUS_COURIER_RECEIPT => '快递收件(揽件)',
        self::LOGISTICS_STATUS_IN_TRANSIT => '运输中',
        self::LOGISTICS_STATUS_DELIVERING => '正在派件',
        self::LOGISTICS_STATUS_SIGNED => '已签收',
        self::LOGISTICS_STATUS_DELIVERY_FAILED => '派送失败',
        self::LOGISTICS_STATUS_TROUBLESOME => '疑难件',
        self::LOGISTICS_STATUS_RETURN_RECEIPT => '退件签收',
        self::LOGISTICS_STATUS_REJECTED => '拒签',
        self::LOGISTICS_STATUS_SEND_BACK => '退回',
        self::LOGISTICS_STATUS_TIMEOUT => '超时件',
        self::LOGISTICS_STATUS_AWAIT_SIGN => '待签收',
        self::LOGISTICS_STATUS_TO_BE_CLEARED => '待清关',
        self::LOGISTICS_STATUS_CLEARANCE => '清关中',
        self::LOGISTICS_STATUS_CLEARED => '已清关',
        self::LOGISTICS_STATUS_CUSTOMS_CLEARANCE_ABNORMALITY => '清关异常',
    ];

    /**
     * @param $status
     *
     * @return array
     */
    public function claimLogisticsStatus($status);

    /**
     * @param $status
     *
     * @return array
     */
    public function abstractLogisticsStatus($status);
}
