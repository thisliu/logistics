<?php

namespace Onechoo\LogisticsInquiry\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Onechoo\LogisticsInquiry\Logistics;
use PHPUnit\Framework\TestCase;

/**
 * Class LogisticsTest
 *
 * @author onechoo <liuhao25@foxmail.com>
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

        $w = \Mockery::mock(Logistics::class, ['mock-key'])->makePartial();

        $w->allows()->getHttpClient()->andReturn($client);

        $this->assertSame('{"success": true}', $w->getLogisticsInfo('7521488'));
    }

    public function testGetHttpClient()
    {
        $w = new Logistics('mock-key');

        $this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
    }

    public function testSetGuzzleOptions()
    {
        $w = new Logistics('mock-key');

        $this->assertNull($w->getHttpClient()->getConfig('timeout'));

        $w->setGuzzleOptions(['timeout' => 5000]);

        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }
}