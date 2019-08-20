<?php
/*
 * This file is part of the finehco/logistics.
 *
 * (c) Aliliin <PhperAli@Gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Providers;

use Finecho\Logistics\Exceptions\HttpException;
use Finecho\Logistics\Exceptions\InquiryErrorException;
use Finecho\Logistics\Interfaces\KdniaoConfigurationConstant;
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Kdniao.
 *
 * @author Aliliin <PhperAli@Gmail.com>
 */
class Kdniao extends AbstractProvider implements KdniaoConfigurationConstant
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
            $query['LogisticCode'] = $no;

            $params = $this->getRequestParams($query);

            $response = $this->sendRequestGet(self::LOGISTICS_COM_CODE_URL, $params, []);

            if (!\is_array($response)) {
                $response = \json_decode($response, true);
            }

            if (empty($response) || empty($response['Shippers'][0]['ShipperCode'])) {
                throw new InquiryErrorException('未查询到该订单信息!', 404, $response);
            }

            $param['ShipperCode'] = $response['Shippers'][0]['ShipperCode'];
        } else {
            $param['ShipperCode'] = $this->getLogisticsCompanyAliases($company);
            $this->company = $company;
        }

        $param['LogisticCode'] = $no;

        $params = $this->getRequestParams($param);

        $response = $this->sendRequestPost(self::LOGISTICS_INFO_URL, $params, [], self::SUCCESS_STATUS);

        // 处理未付费用户
        if ($response && false == $response['Success']) {
            $params['RequestType'] = self::KDNIAO_NOT_PAY;

            $response = $this->sendRequestPost(
                self::LOGISTICS_INFO_URL,
                $params,
                [],
                self::SUCCESS_STATUS
            );
        }

        return $this->mapLogisticsOrderToObject($response)->merge(['original' => $response]);
    }

    /**
     * @param $requestData
     * @param $requestType
     *
     * @return array
     */
    private function getRequestParams($requestData, $requestType = self::KDNIAO_PAY)
    {
        return [
            'EBusinessID' => $this->config[\strtolower(self::PROVIDER_NAME)]['customer'],
            'DataType' => self::KDNIAO_DATA_TYPE,
            'RequestType' => $requestType,
            'RequestData' => \urlencode(\json_encode($requestData)),
            'DataSign' => $this->generateSign($requestData, $this->config[\strtolower(self::PROVIDER_NAME)]['app_code']),
        ];
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
        $list = $this->resetList($logisticsOrder['Traces']);

        list($status, $displayStatus) = $this->claimLogisticsStatus(empty($logisticsOrder['StateEx']) ? \intval($logisticsOrder['State']) : \intval($logisticsOrder['StateEx']));

        return new Order([
            'code' => self::GLOBAL_SUCCESS_CODE,
            'msg' => self::GLOBAL_SUCCESS_MSG,
            'company' => $this->company ?: $logisticsOrder['ShipperCode'],
            'no' => $logisticsOrder['LogisticCode'],
            'status' => $status,
            'display_status' => $displayStatus,
            'abstract_status' => $this->abstractLogisticsStatus($status),
            'list' => $list,
            'original' => $logisticsOrder,
        ]);
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function resetList($list)
    {
        if (\array_intersect(['AcceptStation', 'AcceptTime'], \array_keys(\current($list))) == ['AcceptStation', 'AcceptTime'] || empty($list)) {
            return $list;
        }

        \array_walk($list, function (&$list, $key, $names) {
            unset($list['time']);
            $list = array_combine($names, $list);
        }, ['AcceptStation', 'AcceptTime']);

        return $list;
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
     * @return string
     */
    public function getProviderName()
    {
        return static::PROVIDER_NAME;
    }

    /**
     * @param $param
     * @param $key
     *
     * @return string
     */
    protected function generateSign($param, $key)
    {
        return urlencode(base64_encode(md5(\json_encode($param).$key)));
    }

    /**
     * @param $status
     *
     * @return array
     */
    public function claimLogisticsStatus($status)
    {
        switch ($status) {
            case self::STATUS_NO_TRACK:
                $status = self::LOGISTICS_STATUS_NO_RECORD;

                break;
            case self::STATUS_PACKAGE:
                $status = self::LOGISTICS_STATUS_COURIER_RECEIPT;

                break;
            case self::STATUS_ON_THE_WAY:
                $status = self::LOGISTICS_STATUS_IN_TRANSIT;

                break;
            case self::STATUS_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_QUESTION_PACKAGE:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_IN_THE_CITY:
                $status = self::LOGISTICS_STATUS_IN_TRANSIT;

                break;
            case self::STATUS_IN_THE_PACKAGE:
                $status = self::LOGISTICS_STATUS_IN_TRANSIT;

                break;
            case self::STATUS_DIEPOSIT_ARK:
                $status = self::LOGISTICS_STATUS_AWAIT_SIGN;

                break;
            case self::STATUS_NORMAL_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_ABNORMAL_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_ISSUING_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_ARK_SIGNING:
                $status = self::LOGISTICS_STATUS_SIGNED;

                break;
            case self::STATUS_NO_DELIVERY_INFO:
                $status = self::LOGISTICS_STATUS_TROUBLESOME;

                break;
            case self::STATUS_TIMEOUT_NOT_SIGNING:
                $status = self::LOGISTICS_STATUS_TIMEOUT;

                break;
            case self::STATUS_TIMEOUT_NOT_UPDATE:
                $status = self::LOGISTICS_STATUS_TIMEOUT;

                break;
            case self::STATUS_RETURN_PACKAGE:
                $status = self::LOGISTICS_STATUS_REJECTED;

                break;
            case self::STATUS_PACKAGE_ERROR:
                $status = self::LOGISTICS_STATUS_DELIVERY_FAILED;

                break;
            case self::STATUS_RETURN_SINGNING:
                $status = self::LOGISTICS_STATUS_RETURN_RECEIPT;

                break;
            case self::STATUS_RETURN_NOT_SINGNING:
                $status = self::LOGISTICS_STATUS_AWAIT_SIGN;

                break;
            case self::STATUS_ARK_NOT_SINGNING:
                $status = self::LOGISTICS_STATUS_AWAIT_SIGN;

                break;
            default:
                $status = self::LOGISTICS_STATUS_ERROR;

                break;
        }

        return [$status, self::LOGISTICS_STATUS_LABELS[$status]];
    }
}
