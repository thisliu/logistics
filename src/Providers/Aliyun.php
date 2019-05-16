<?php

/*
 * This file is part of the finecho/logistics-inquiry.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\LogisticsInquiry\Providers;

use Finecho\LogisticsInquiry\Traits\HasHttpRequest;

/**
 * Class Aliyun
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Aliyun extends Base
{
    use HasHttpRequest;

    const PROVIDER_NAME = 'Aliyun';

    const LOGISTICS_INFO_URL = 'http://wuliu.market.alicloudapi.com/kdi';

    const LOGISTICS_COMPANY_URL = 'http://wuliu.market.alicloudapi.com/getExpressList';

    /**
     * @param string $no
     * @param null   $type
     *
     * @return array
     */
    public function show($no, $type = null)
    {
        $params = \array_filter([
            'no' => $no,
            'type' => $type,
        ]);

        $headers = ['Authorization' => \sprintf('APPCODE %s', $this->config['aliyun']['app_code'])];

        return $this->get(self::LOGISTICS_INFO_URL, $params, $headers);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return static::PROVIDER_NAME;
    }
}
