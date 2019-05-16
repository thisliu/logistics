<?php

namespace Finecho\LogisticsInquiry\Tests;

use Finecho\LogisticsInquiry\Aliyun;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class LogisticsTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class LogisticsTest extends TestCase
{
    public function testGetLogisticsInfo()
    {
        $response = new Response(200, [], '{"success": true}');

        $client = \Mockery::mock(Client::class);

        $query = array_filter([
            'no' => '7521488',
            'type' => null,
        ]);

        $client->allows()->get('http://wuliu.market.alicloudapi.com/kdi', [
            'query' => $query,
            'headers' => ['Authorization' => \sprintf('APPCODE %s', 'mock-key')],
        ])->andReturn($response);

        $l = \Mockery::mock(Aliyun::class, ['mock-key'])->makePartial();

        $l->allows()->getHttpClient()->andReturn($client);

        $this->assertSame('{"success": true}', $l->getLogisticsInfo('7521488'));
    }

    public function testGetHttpClient()
    {
        $w = new Aliyun('mock-key');

        $this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
    }

    public function testSetGuzzleOptions()
    {
        $w = new Aliyun('mock-key');

        $this->assertNull($w->getHttpClient()->getConfig('timeout'));

        $w->setGuzzleOptions(['timeout' => 5000]);

        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }

    public function testGetLogisticsCompany()
    {
        $response = new Response(200, [], '{"success": true}');

        $client = \Mockery::mock(Client::class);

        $query = array_filter([
            'type' => 'ALL',
        ]);

        $client->allows()->get('http://wuliu.market.alicloudapi.com/getExpressList', [
            'query' => $query,
            'headers' => ['Authorization' => \sprintf('APPCODE %s', 'mock-key')],
        ])->andReturn($response);

        $l = \Mockery::mock(Aliyun::class, ['mock-key'])->makePartial();

        $l->allows()->getHttpClient()->andReturn($client);

        $this->assertSame('{"success": true}', $l->getLogisticsCompany('ALL'));
    }
}
