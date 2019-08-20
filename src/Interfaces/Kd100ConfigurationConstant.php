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

interface Kd100ConfigurationConstant
{
    const PROVIDER_NAME = 'Kd100';

    const LOGISTICS_COM_CODE_URL = 'http://www.kuaidi100.com/autonumber/auto';

    const LOGISTICS_INFO_URL = 'http://poll.kuaidi100.com/poll/query.do';

    const SUCCESS_STATUS = 200;

    const STATUS_ON_THE_WAY = 0;

    const STATUS_PACKAGE = 1;

    const STATUS_DIFFICULT = 2;

    const STATUS_SIGNING = 3;

    const STATUS_REFUND = 4;

    const STATUS_PIECE = 5;

    const STATUS_RETURN = 6;

    const RETURN_TO_BE_CLEARED = 10;

    const STATUS_CLEARANCE = 11;

    const STATUS_CLEARED = 12;

    const STATUS_CUSTOMS_CLEARANCE_ABNORMALITY = 13;

    const STATUS_RECIPIENT_REFUSAL = 14;

    const STATUS_LABELS = [
        self::STATUS_PACKAGE => '揽件',
        self::STATUS_DIFFICULT => '疑难',
        self::STATUS_SIGNING => '签收',
        self::STATUS_REFUND => '退签',
        self::STATUS_PIECE => '派件',
        self::STATUS_RETURN => '退回',
        self::RETURN_TO_BE_CLEARED => '待清关',
        self::STATUS_CLEARANCE => '清关中',
        self::STATUS_CLEARED => '已清关',
        self::STATUS_CUSTOMS_CLEARANCE_ABNORMALITY => '清关异常',
        self::STATUS_RECIPIENT_REFUSAL => '收件人拒签',
    ];
}
