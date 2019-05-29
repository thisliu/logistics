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
use Finecho\Logistics\Order;
use Finecho\Logistics\Traits\HasHttpRequest;

/**
 * Class Kdniao.
 *
 * @author Aliliin <PhperAli@Gmail.com>
 */
class Kdniao extends AbstractProvider
{
    use HasHttpRequest;

    const PROVIDER_NAME = 'Kdniao';

    const KDNIAO_NOT_PAY = 1002;

    const KDNIAO_PAY = 8001;

    const KDNIAO_DATA_TYPE = 2;

    const LOGISTICS_INFO_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    const LOGISTICS_COM_CODE_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    const SUCCESS_STATUS             = 200;
    const STATUS_ERROR               = -1;
    const STATUS_NO_TRACK            = 0;
    const STATUS_PACKAGE             = 1;
    const STATUS_ON_THE_WAY          = 2;
    const STATUS_SIGNING             = 3;
    const STATUS_QUESTION_PACKAGE    = 4;
    const STATUS_IN_THE_CITY         = 201;
    const STATUS_IN_THE_PACKAGE      = 202;
    const STATUS_DIEPOSIT_ARK        = 211;
    const STATUS_NORMAL_SIGNING      = 301;
    const STATUS_ABNORMAL_SIGNING    = 302;
    const STATUS_ISSUING_SIGNING     = 304;
    const STATUS_ARK_SIGNING         = 311;
    const STATUS_NO_DELIVERY_INFO    = 401;
    const STATUS_TIMEOUT_NOT_SIGNING = 402;
    const STATUS_TIMEOUT_NOT_UPDATE  = 403;
    const STATUS_RETURN_PACKAGE      = 404;
    const STATUS_PACKAGE_ERROR       = 405;
    const STATUS_RETURN_SINGNING     = 406;
    const STATUS_RETURN_NOT_SINGNING = 407;
    const STATUS_ARK_NOT_SINGNING    = 412;

    const STATUS_LABELS = [
        self::STATUS_ERROR               => '异常',
        self::STATUS_NO_TRACK            => '无轨迹',
        self::STATUS_PACKAGE             => '已揽收',
        self::STATUS_SIGNING             => '已签收',
        self::STATUS_ON_THE_WAY          => '在途中',
        self::STATUS_QUESTION_PACKAGE    => '问题件',
        self::STATUS_IN_THE_CITY         => '到达派件城市',
        self::STATUS_IN_THE_PACKAGE      => '派件中',
        self::STATUS_DIEPOSIT_ARK        => '已放入快递柜或驿站',
        self::STATUS_NORMAL_SIGNING      => '正常签收',
        self::STATUS_ABNORMAL_SIGNING    => '派件异常后最终签收',
        self::STATUS_ISSUING_SIGNING     => '代收签收',
        self::STATUS_ARK_SIGNING         => '快递柜或驿站签收',
        self::STATUS_NO_DELIVERY_INFO    => '发货无信息',
        self::STATUS_TIMEOUT_NOT_SIGNING => '超时未签收',
        self::STATUS_TIMEOUT_NOT_UPDATE  => '超时未更新',
        self::STATUS_RETURN_PACKAGE      => '拒收(退件)',
        self::STATUS_PACKAGE_ERROR       => '派件异常',
        self::STATUS_RETURN_SINGNING     => '退货签收',
        self::STATUS_RETURN_NOT_SINGNING => '退货未签收',
        self::STATUS_ARK_NOT_SINGNING    => '快递柜或驿站超时未取',
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
            $query['LogisticCode'] = $no;

            $params = $this->getRequestParams($query，self::LOGISTICS_COM_CODE_TYPE);

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
            $this->company        = $company;
        }

        $param['LogisticCode'] = $no;

        $params = $this->getRequestParams($param);

        $response = $this->sendRequestPost(self::LOGISTICS_INFO_URL, $params, [], self::SUCCESS_STATUS);

        // 处理未付费用户
        if ($response && $response['Success'] == false) {
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
     * @param     $requestData
     * @param     $requestType
     *
     * @return array
     *
     */
    private function getRequestParams($requestData, $requestType = self::KDNIAO_PAY)
    {
        return [
            'EBusinessID' => $this->config[\strtolower(self::PROVIDER_NAME)]['customer'],
            'DataType'    => self::KDNIAO_DATA_TYPE,
            'RequestType' => $requestType,
            'RequestData' => \urlencode(\json_encode($requestData)),
            'DataSign'    => $this->generateSign($requestData, $this->config[\strtolower(self::PROVIDER_NAME)]['app_code']),
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
        $status = empty($logisticsOrder['StateEx']) ? \intval($logisticsOrder['State']) : \intval($logisticsOrder['StateEx']);

        $list = $this->resetList($logisticsOrder['Traces']);

        return new Order([
            'code'     => self::GLOBAL_SUCCESS_CODE,
            'msg'      => self::GLOBAL_SUCCESS_MSG,
            'company'  => $this->company ?: $logisticsOrder['ShipperCode'],
            'no'       => $logisticsOrder['LogisticCode'],
            'status'   => \in_array($status, \array_keys(self::STATUS_LABELS)) ? self::STATUS_LABELS[$status] : self::STATUS_LABELS[self::STATUS_ERROR],
            'list'     => $list,
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
     * @param $customer
     *
     * @return string
     */
    protected function generateSign($param, $key)
    {
        return urlencode(base64_encode(md5(\json_encode($param) . $key)));
    }
}
