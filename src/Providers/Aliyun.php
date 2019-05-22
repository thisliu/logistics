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
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Aliyun.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Aliyun extends AbstractProvider
{
    use HasHttpRequest;

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

    /**
     * @param      $no
     * @param null $type
     *
     * @return \Finecho\Logistics\Order
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    public function order($no, $type = null)
    {
        $params = \array_filter([
            'no' => $no,
            'type' => $type,
        ]);

        $headers = ['Authorization' => \sprintf('APPCODE %s', $this->config['aliyun']['app_code'])];

        $response = $this->sendRequest(self::LOGISTICS_INFO_URL, $params, $headers, self::SUCCESS_STATUS);

        return $this->mapLogisticsOrderToObject($response)->merge(['original' => $response]);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return static::PROVIDER_NAME;
    }

    /**
     * @param string $url
     * @param array  $params
     * @param array  $headers
     * @param int    $SUCCESS_STATUS
     *
     * @return array
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    protected function sendRequest($url, $params, $headers, $SUCCESS_STATUS = self::GLOBAL_SUCCESS_CODE)
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

    /**
     * @param $logisticsOrder
     *
     * @return \Finecho\Logistics\Order
     */
    protected function mapLogisticsOrderToObject($logisticsOrder)
    {
        $status = \intval($logisticsOrder['result']['deliverystatus']);

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,

            'company' => $logisticsOrder['result']['type'],
            'no' => $logisticsOrder['result']['number'],
            'status' => \in_array($status, \array_keys(self::STATUS_LABELS)) ? self::STATUS_LABELS[$status] : self::STATUS_LABELS[self::STATUS_ERROR],
            'courier' => $logisticsOrder['result']['courier'],
            'courierPhone' => $logisticsOrder['result']['courierPhone'],
            'list' => $logisticsOrder['result']['list'],
        ]);
    }
}
