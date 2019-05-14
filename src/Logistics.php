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
    public function __construct(string $appCode)
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
    public function getLogisticsInfo($no, string $type = null)
    {
        $url = 'http://wuliu.market.alicloudapi.com/kdi';

        $query = array_filter([
            'no' => $no,
            'type' => $type,
        ]);

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
}