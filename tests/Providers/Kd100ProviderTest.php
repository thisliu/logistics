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


use Finecho\Logistics\Providers\Kd100;
use PHPUnit\Framework\TestCase;

/**
 * Class Kd100ProviderTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Kd100ProviderTest extends TestCase
{
    public function setUp()
    {
        \Mockery::globalHelpers();
    }

    public function testGetLogisticsInfo()
    {
        $config = [
            'provider' => 'kd100',

            'kd100' => [
                'app_code' => 'xxxxxxx',
                'customer' => 'xxxxxxx',
            ], ];

        $response = [
            'msg' => 'OK',
            'nu' => '7521488',
            'state' => '3',
            'data' => [['time' => '2019-05-23', 'ftime' => '2019-05-23', 'remark' => '已发货'], ['time' => '2019-05-23', 'ftime' => '2019-05-23', 'remark' => '已签收']],
            'courier' => '',
            'courierPhone' => '',
        ];

        $Kd100 = \Mockery::mock(Kd100::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $params = [
            'customer' => 'xxxxxxx',
            'param' => \json_encode(['com' => 'suning', 'num' => '7521488']),
            'sign' => \strtoupper(\md5(\json_encode(['com' => 'suning', 'num' => '7521488']).$config['kd100']['app_code'].$config['kd100']['customer'])),
        ];

        $Kd100->shouldReceive('post')->with(
            'http://poll.kuaidi100.com/poll/query.do', $params, [])->once()->andReturn($response);

        $this->assertSame($response, $Kd100->query('7521488', '苏宁')->getOriginal());
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
