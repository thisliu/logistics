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
use Finecho\Logistics\Interfaces\Kd100ConfigurationConstant;
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Kd100.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Kd100 extends AbstractProvider implements Kd100ConfigurationConstant
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
        if (empty($company)) {
            $query = [
                'key' => $this->config[\strtolower(self::PROVIDER_NAME)]['app_code'],
                'num' => $no,
            ];

            $response = $this->sendRequestGet(self::LOGISTICS_COM_CODE_URL, $query, []);

            if (!\is_array($response)) {
                $response = \json_decode($response, true);
            }

            if (empty($response)) {
                throw new InquiryErrorException('未查询到该订单信息!', 404, $response);
            }

            $param['com'] = \current($response)['comCode'];
        } else {
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
     *
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
     *
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

        if (!\is_array($result)) {
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
        $list = $this->resetList($logisticsOrder['data']);

        list($status, $displayStatus) = $this->claimLogisticsStatus(\intval($logisticsOrder['state']));

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,
            'company' => $this->company ?: $logisticsOrder['com'],
            'no' => $logisticsOrder['nu'],
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
        return \strtoupper(\md5(\json_encode($param).$key.$customer));
    }

    /**
     * @param $status
     *
     * @return array
     */
    public function claimLogisticsStatus($status)
    {
        switch ($status) {
            case self::STATUS_PACKAGE:
                $status = self::LOGISTICS_STATUS_COURIER_RECEIPT;

                break;
            case self::STATUS_DIFFICULT:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_REFUND:
                $status = self::LOGISTICS_STATUS_RETURN_RECEIPT;

                break;
            case self::STATUS_PIECE:
                $status = self::LOGISTICS_STATUS_DELIVERING;

                break;
            case self::STATUS_RETURN:
                $status = self::LOGISTICS_STATUS_SEND_BACK;

                break;
            case self::RETURN_TO_BE_CLEARED:
                $status = self::LOGISTICS_STATUS_TO_BE_CLEARED;

                break;
            case self::STATUS_CLEARANCE:
                $status = self::LOGISTICS_STATUS_CLEARANCE;

                break;
            case self::STATUS_CLEARED:
                $status = self::LOGISTICS_STATUS_CLEARED;

                break;
            case self::STATUS_CUSTOMS_CLEARANCE_ABNORMALITY:
                $status = self::LOGISTICS_STATUS_CUSTOMS_CLEARANCE_ABNORMALITY;

                break;
            case self::STATUS_RECIPIENT_REFUSAL:
                $status = self::LOGISTICS_STATUS_REJECTED;

                break;
            default:
                $status = self::LOGISTICS_STATUS_ERROR;

                break;
        }

        return [$status, self::LOGISTICS_STATUS_LABELS[$status]];
    }
}
