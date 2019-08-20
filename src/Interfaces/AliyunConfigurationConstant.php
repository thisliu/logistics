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

interface AliyunConfigurationConstant
{
    const PROVIDER_NAME = 'Aliyun';

    const LOGISTICS_INFO_URL = 'http://wuliu.market.alicloudapi.com/kdi';

    const LOGISTICS_COMPANY_URL = 'http://wuliu.market.alicloudapi.com/getExpressList';

    const SUCCESS_STATUS = 0;

    const STATUS_ERROR = -1;

    const STATUS_COURIER_RECEIPT = 0;

    const STATUS_ON_THE_WAY = 1;

    const STATUS_SENDING_A_PIECE = 2;

    const STATUS_SIGNED = 3;

    const STATUS_DELIVERY_FAILED = 4;

    const STATUS_TROUBLESOME = 5;

    const STATUS_RETURN_RECEIPT = 6;

    const STATUS_LABELS = [
        self::STATUS_ERROR => '异常',
        self::STATUS_COURIER_RECEIPT => '快递收件(揽件)',
        self::STATUS_ON_THE_WAY => '在途中',
        self::STATUS_SENDING_A_PIECE => '正在派件',
        self::STATUS_SIGNED => '已签收',
        self::STATUS_DELIVERY_FAILED => '派送失败',
        self::STATUS_TROUBLESOME => '疑难件',
        self::STATUS_RETURN_RECEIPT => '退件签收',
    ];
}
