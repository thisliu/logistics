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
use Finecho\Logistics\Interfaces\JuheConfigurationConstant;
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Juhe.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Juhe extends AbstractProvider implements JuheConfigurationConstant
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
        $list = $this->resetList($logisticsOrder['result']['list']);

        list($status, $displayStatus) = $this->claimLogisticsStatus(\intval($logisticsOrder['result']['status_detail']));

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,
            'company' => $this->company ?: $logisticsOrder['result']['company'],
            'no' => $logisticsOrder['result']['no'],
            'status' => $status,
            'display_status' => $displayStatus,
            'abstract_status' => $this->abstractLogisticsStatus($status),
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
            case self::STATUS_PENDING:
                $status = self::LOGISTICS_STATUS_NO_RECORD;

                break;
            case self::STATUS_NO_RECORD:
                $status = self::LOGISTICS_STATUS_NO_RECORD;

                break;
            case self::STATUS_IN_TRANSIT:
                $status = self::LOGISTICS_STATUS_IN_TRANSIT;

                break;
            case self::STATUS_DELIVERING:
                $status = self::LOGISTICS_STATUS_DELIVERING;

                break;
            case self::STATUS_SIGNED:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_REJECTED:
                $status = self::LOGISTICS_STATUS_REJECTED;

                break;
            case self::STATUS_PROBLEM:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_INVALID:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_TIMEOUT:
                $status = self::LOGISTICS_STATUS_TIMEOUT;

                break;
            case self::STATUS_FAILED:
                $status = self::LOGISTICS_STATUS_DELIVERY_FAILED;

                break;
            case self::STATUS_SEND_BACK:
                $status = self::LOGISTICS_STATUS_SEND_BACK;

                break;
            case self::STATUS_TAKING:
                $status = self::LOGISTICS_STATUS_COURIER_RECEIPT;

                break;
            default:
                $status = self::LOGISTICS_STATUS_ERROR;

                break;
        }

        return [$status, self::LOGISTICS_STATUS_LABELS[$status]];
    }
}
