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

interface JuheConfigurationConstant
{
    const PROVIDER_NAME = 'Juhe';

    const LOGISTICS_INFO_URL = 'http://v.juhe.cn/exp/index';

    const SUCCESS_STATUS = 200;

    const STATUS_PENDING = 'PENDING';

    const STATUS_NO_RECORD = 'NO_RECORD';

    const STATUS_IN_TRANSIT = 'IN_TRANSIT';

    const STATUS_DELIVERING = 'DELIVERING';

    const STATUS_SIGNED = 'SIGNED';

    const STATUS_REJECTED = 'REJECTED';

    const STATUS_PROBLEM = 'PROBLEM';

    const STATUS_INVALID = 'INVALID';

    const STATUS_TIMEOUT = 'TIMEOUT';

    const STATUS_FAILED = 'FAILED';

    const STATUS_SEND_BACK = 'SEND_BACK';

    const STATUS_TAKING = 'TAKING';

    const STATUS_LABELS = [
        self::STATUS_PENDING => '待查询',
        self::STATUS_NO_RECORD => '无记录',
        self::STATUS_IN_TRANSIT => '运输中',
        self::STATUS_DELIVERING => '派送中',
        self::STATUS_SIGNED => '已签收',
        self::STATUS_REJECTED => '拒签',
        self::STATUS_PROBLEM => '疑难件',
        self::STATUS_INVALID => '无效件',
        self::STATUS_TIMEOUT => '超时件',
        self::STATUS_FAILED => '派送失败',
        self::STATUS_SEND_BACK => '退回',
        self::STATUS_TAKING => '揽件',
    ];
}
