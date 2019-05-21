<?php

/*
 * This file is part of the finecho/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Providers;

use Finecho\Logistics\Exceptions\HttpException;
use Finecho\Logistics\Exceptions\InquiryErrorException;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Aliyun.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Aliyun extends Base
{
    use HasHttpRequest;

    const PROVIDER_NAME = 'Aliyun';

    const LOGISTICS_INFO_URL = 'http://wuliu.market.alicloudapi.com/kdi';

    const LOGISTICS_COMPANY_URL = 'http://wuliu.market.alicloudapi.com/getExpressList';

    const SUCCESS_STATUS = 0;

    /**
     * @param string $no
     * @param null   $type
     *
     * @return array
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    public function show($no, $type = null)
    {
        $params = \array_filter([
            'no' => $no,
            'type' => $type,
        ]);

        $headers = ['Authorization' => \sprintf('APPCODE %s', $this->config['aliyun']['app_code'])];

        return $this->sendRequest(self::LOGISTICS_INFO_URL, $params, $headers, self::SUCCESS_STATUS);
    }

    /**
     * @param string $type
     *
     * @return array
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    public function companies($type = 'ALL')
    {
        $params = \array_filter([
            'type' => $type,
        ]);

        $headers = ['Authorization' => \sprintf('APPCODE %s', $this->config['aliyun']['app_code'])];

        return $this->sendRequest(self::LOGISTICS_COMPANY_URL, $params, $headers);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return static::PROVIDER_NAME;
    }

    /**
     * @param     $url
     * @param     $params
     * @param     $headers
     * @param int $SUCCESS_STATUS
     *
     * @return array
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    protected function sendRequest($url, $params, $headers, $SUCCESS_STATUS = 200)
    {
        try {
            $result = $this->get($url, $params, $headers);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        if ($SUCCESS_STATUS != $result['status']) {
            throw new InquiryErrorException($result['msg'], $result['status'], $result);
        }

        return $result;
    }
}
