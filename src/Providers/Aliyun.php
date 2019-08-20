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
use Finecho\Logistics\Interfaces\AliyunConfigurationConstant;
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Aliyun.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Aliyun extends AbstractProvider implements AliyunConfigurationConstant
{
    use HasHttpRequest;

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
    public function query($no, $company = null)
    {
        $params = \array_filter([
            'no' => $no,
            'company' => $company,
        ]);

        if (\in_array('company', \array_keys($params))) {
            $params['type'] = $this->getLogisticsCompanyAliases($params['company']);

            unset($params['company']);

            $this->company = $company;
        }

        $headers = ['Authorization' => \sprintf('APPCODE %s', $this->config[\strtolower(self::PROVIDER_NAME)]['app_code'])];

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
        $list = $this->resetList($logisticsOrder['result']['list']);

        list($status, $displayStatus) = $this->claimLogisticsStatus(\intval($logisticsOrder['result']['deliverystatus']));

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,
            'company' => $this->company ?: $logisticsOrder['result']['expName'],
            'no' => $logisticsOrder['result']['number'],
            'status' => $status,
            'display_status' => $displayStatus,
            'abstract_status' => $this->abstractLogisticsStatus($status),
            'courier' => $logisticsOrder['result']['courier'],
            'courier_phone' => $logisticsOrder['result']['courierPhone'],
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

    /**
     * @param $status
     *
     * @return array
     */
    public function claimLogisticsStatus($status)
    {
        switch ($status) {
            case self::STATUS_COURIER_RECEIPT:
                $status = self::LOGISTICS_STATUS_COURIER_RECEIPT;

                break;
            case self::STATUS_ON_THE_WAY:
                $status = self::LOGISTICS_STATUS_IN_TRANSIT;

                break;
            case self::STATUS_SENDING_A_PIECE:
                $status = self::LOGISTICS_STATUS_DELIVERING;

                break;
            case self::STATUS_SIGNED:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_DELIVERY_FAILED:
                $status = self::LOGISTICS_STATUS_DELIVERY_FAILED;

                break;
            case self::STATUS_TROUBLESOME:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_RETURN_RECEIPT:
                $status = self::LOGISTICS_STATUS_RETURN_RECEIPT;

                break;
            default:
                $status = self::LOGISTICS_STATUS_ERROR;

                break;
        }

        return [$status, self::LOGISTICS_STATUS_LABELS[$status]];
    }
}
