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

interface KdniaoConfigurationConstant
{
    const PROVIDER_NAME = 'Kdniao';

    const KDNIAO_NOT_PAY = 1002;

    const KDNIAO_PAY = 8001;

    const KDNIAO_DATA_TYPE = 2;

    const LOGISTICS_INFO_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    const LOGISTICS_COM_CODE_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    const SUCCESS_STATUS = 200;

    const STATUS_NO_TRACK = 0;

    const STATUS_PACKAGE = 1;

    const STATUS_ON_THE_WAY = 2;

    const STATUS_SIGNING = 3;

    const STATUS_QUESTION_PACKAGE = 4;

    const STATUS_IN_THE_CITY = 201;

    const STATUS_IN_THE_PACKAGE = 202;

    const STATUS_DIEPOSIT_ARK = 211;

    const STATUS_NORMAL_SIGNING = 301;

    const STATUS_ABNORMAL_SIGNING = 302;

    const STATUS_ISSUING_SIGNING = 304;

    const STATUS_ARK_SIGNING = 311;

    const STATUS_NO_DELIVERY_INFO = 401;

    const STATUS_TIMEOUT_NOT_SIGNING = 402;

    const STATUS_TIMEOUT_NOT_UPDATE = 403;

    const STATUS_RETURN_PACKAGE = 404;

    const STATUS_PACKAGE_ERROR = 405;

    const STATUS_RETURN_SINGNING = 406;

    const STATUS_RETURN_NOT_SINGNING = 407;

    const STATUS_ARK_NOT_SINGNING = 412;

    const STATUS_LABELS = [
        self::STATUS_NO_TRACK => '无轨迹',
        self::STATUS_PACKAGE => '已揽收',
        self::STATUS_SIGNING => '已签收',
        self::STATUS_ON_THE_WAY => '在途中',
        self::STATUS_QUESTION_PACKAGE => '问题件',
        self::STATUS_IN_THE_CITY => '到达派件城市',
        self::STATUS_IN_THE_PACKAGE => '派件中',
        self::STATUS_DIEPOSIT_ARK => '已放入快递柜或驿站',
        self::STATUS_NORMAL_SIGNING => '正常签收',
        self::STATUS_ABNORMAL_SIGNING => '派件异常后最终签收',
        self::STATUS_ISSUING_SIGNING => '代收签收',
        self::STATUS_ARK_SIGNING => '快递柜或驿站签收',
        self::STATUS_NO_DELIVERY_INFO => '发货无信息',
        self::STATUS_TIMEOUT_NOT_SIGNING => '超时未签收',
        self::STATUS_TIMEOUT_NOT_UPDATE => '超时未更新',
        self::STATUS_RETURN_PACKAGE => '拒收(退件)',
        self::STATUS_PACKAGE_ERROR => '派件异常',
        self::STATUS_RETURN_SINGNING => '退货签收',
        self::STATUS_RETURN_NOT_SINGNING => '退货未签收',
        self::STATUS_ARK_NOT_SINGNING => '快递柜或驿站超时未取',
    ];
}
