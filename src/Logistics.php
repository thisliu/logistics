<?php

namespace Finecho\LogisticsInquiry;

use GuzzleHttp\Client;
use Finecho\LogisticsInquiry\Exceptions\HttpException;

/**
 * Class Logistics
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Logistics
{
    protected $appCode;

    protected $guzzleOptions = [];

    /**
     * Logistics constructor.
     *
     * @param string $appCode
     */
    public function __construct($appCode)
    {
        $this->appCode = $appCode;
    }

    /**
     * @param             $no
     * @param string|null $type
     *
     * @return string
     *
     * @throws \Finecho\LogisticsInquiry\Exceptions\HttpException
     */
    public function getLogisticsInfo($no, $type = null)
    {
        $url = 'http://wuliu.market.alicloudapi.com/kdi';

        $query = array_filter([
            'no' => $no,
            'type' => $type,
        ]);

        return $this->sendRequest($url, $query);
    }

    /**
     * @param string|null $type
     *
     * @return string
     *
     * @throws \Finecho\LogisticsInquiry\Exceptions\HttpException
     */
    public function getLogisticsCompany($type = 'ALL')
    {
        $url = 'http://wuliu.market.alicloudapi.com/getExpressList';

        $query = array_filter([
            'type' => $type,
        ]);

        return $this->sendRequest($url, $query);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param array $options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param string $url
     * @param array  $query
     *
     * @return string
     *
     * @throws \Finecho\LogisticsInquiry\Exceptions\HttpException
     */
    private function sendRequest($url, $query)
    {
        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
                'headers' => ['Authorization' => \sprintf('APPCODE %s', $this->appCode)],
            ])->getBody()->getContents();

            return $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}