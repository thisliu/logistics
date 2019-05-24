<?php

/*
 * This file is part of the finehco/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Tests\Providers;

use Finecho\Logistics\Providers\Aliyun;
use PHPUnit\Framework\TestCase;

/**
 * Class AliyunProviderTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class AliyunProviderTest extends TestCase
{
    public function setUp()
    {
        \Mockery::globalHelpers();
    }

    public function testGetLogisticsInfo()
    {
        $config = [
            'provider' => 'aliyun',

            'aliyun' => [
                'app_code' => 'xxxxxxx',
            ], ];

        $response = [
            'status' => 0,
            'msg' => 'OK',
            'result' => [
                'number' => '7521488',
                'type' => 'zto',
                'list' => [['datetime' => '2019-05-23', 'remark' => '已发货'], ['datetime' => '2019-05-24', 'remark' => '已签收']],
                'deliverystatus' => 3,
                'courier' => '',
                'courierPhone' => '',
                'expName' => '',
            ],
        ];

        $aliyun = \Mockery::mock(Aliyun::class.'[get]', [$config])->shouldAllowMockingProtectedMethods();

        $query = ['no' => '7521488'];

        $headers = ['Authorization' => 'APPCODE xxxxxxx'];

        $aliyun->shouldReceive('get')->with(
            'http://wuliu.market.alicloudapi.com/kdi', $query, $headers)->once()->andReturn($response);

        $this->assertSame($response, $aliyun->order('7521488')->getOriginal());
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
