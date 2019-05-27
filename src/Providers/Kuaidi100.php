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
 * Class Kuaidi100.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Kuaidi100 extends AbstractProvider
{
    use HasHttpRequest;

    const PROVIDER_NAME = 'Kuaidi100';

    const LOGISTICS_COM_CODE_URL = 'http://www.kuaidi100.com/autonumber/auto';

    const LOGISTICS_INFO_URL = 'http://poll.kuaidi100.com/poll/query.do';

    const SUCCESS_STATUS = 200;

    const STATUS_ERROR = -1;

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
        self::STATUS_ERROR => '异常',
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
        if (empty($company)) {
            $query = [
                'key' => $this->config[\strtolower(self::PROVIDER_NAME)]['app_code'],
                'num' => $no,
            ];

            $response = $this->sendRequestGet(self::LOGISTICS_COM_CODE_URL, $query, []);

            if(!\is_array($response)){
                $response = \json_decode($response, true);
            }

            if (empty($response)) {
                throw new InquiryErrorException('未查询到该订单信息!', 404, $response);
            }

            $param['com'] = \current($response)['comCode'];
        }else{
            $param['com'] = $this->getLogisticsCompanyAliases($company);

            $this->company = $company;
        }

        $param['num'] = $no;

        $params = [
            'customer' => $this->config[\strtolower(self::PROVIDER_NAME)]['customer'],
            'param' => \json_encode($param),
            'sign' => $this->generateSign($param, $this->config[\strtolower(self::PROVIDER_NAME)]['app_code'], $this->config[\strtolower(self::PROVIDER_NAME)]['customer']),
        ];

        $response = $this->sendRequestPost(self::LOGISTICS_INFO_URL, $params, [], self::SUCCESS_STATUS);

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
     *
     * @return array
     * @throws \Finecho\Logistics\Exceptions\HttpException
     */
    protected function sendRequestGet($url, $params, $headers)
    {
        try {
            $result = $this->get($url, $params, $headers);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    /**
     * @param     $url
     * @param     $params
     * @param     $headers
     * @param int $SUCCESS_STATUS
     *
     * @return array
     * @throws \Finecho\Logistics\Exceptions\HttpException
     * @throws \Finecho\Logistics\Exceptions\InquiryErrorException
     */
    protected function sendRequestPost($url, $params, $headers, $SUCCESS_STATUS = self::GLOBAL_SUCCESS_CODE)
    {
        try {
            $result = $this->post($url, $params, $headers);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        if(!\is_array($result)){
            $result = \json_decode($result, true);
        }

        if (isset($result['returnCode']) && $SUCCESS_STATUS != $result['returnCode']) {
            throw new InquiryErrorException($result['message'], $result['returnCode'], $result);
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
        $status = \intval($logisticsOrder['state']);

        $list = $this->resetList($logisticsOrder['data']);

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,

            'company' => $this->company ?: $logisticsOrder['com'],
            'no' => $logisticsOrder['nu'],
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
            unset($list['time']);

            $list = array_combine($names, $list);
        }, ['datetime', 'remark']);

        return $list;
    }

    /**
     * @param $param
     * @param $key
     * @param $customer
     *
     * @return string
     */
    protected function generateSign($param, $key, $customer)
    {
        return \strtoupper(\md5(\json_encode($param) . $key . $customer));
    }
}
