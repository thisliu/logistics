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
use Finecho\Logistics\Exceptions\InvalidArgumentException;
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Juhe.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Juhe extends AbstractProvider
{
    use HasHttpRequest;

    const PROVIDER_NAME = 'Juhe';

    const LOGISTICS_INFO_URL = 'http://v.juhe.cn/exp/index';

    const SUCCESS_STATUS = 200;

    const STATUS_ERROR = -1;

    const STATUS_NO_CONTENT = 0;

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
        self::STATUS_ERROR => '异常',
        self::STATUS_NO_CONTENT => '无信息',
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

    /**
     * @param      $no
     * @param null $company
     *
     * @return \Finecho\Logistics\Order
     *
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     * @throws \Finecho\Logistics\Exceptions\InvalidArgumentException
     */
    public function order($no, $company = null)
    {
        $params = \array_filter([
            'key' => $this->config[\strtolower(self::PROVIDER_NAME)]['app_code'],
            'no' => $no,
            'company' => $company,
        ]);

        if (\in_array('company', \array_keys($params))) {
            $params['com'] = $this->getLogisticsCompanyAliases($params['company']);

            unset($params['company']);

            $this->company = $company;
        } else {
            throw new InvalidArgumentException();
        }

        $response = $this->sendRequest(self::LOGISTICS_INFO_URL, $params, [], self::SUCCESS_STATUS);

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

        if ($SUCCESS_STATUS != $result['resultcode']) {
            throw new InquiryErrorException($result['reason'], $result['resultcode'], $result);
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
        $status = \intval($logisticsOrder['result']['status_detail']);

        $list = $this->resetList($logisticsOrder['result']['list']);

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,

            'company' => $this->company ?: $logisticsOrder['result']['company'],
            'no' => $logisticsOrder['result']['no'],
            'status' => \in_array($status, \array_keys(self::STATUS_LABELS)) ? self::STATUS_LABELS[$status] : self::STATUS_LABELS[self::STATUS_ERROR],
            'list' => $list,
        ]);
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function resetList($list)
    {
        if (\array_intersect(['datetime', 'remark'], \array_keys(\current($list))) == ['datetime', 'remark'] || empty($list)) {
            return $list;
        }

        \array_walk($list, function (&$list, $key, $names) {
            $list = array_combine($names, $list);
        }, ['datetime', 'remark']);

        return $list;
    }
}
