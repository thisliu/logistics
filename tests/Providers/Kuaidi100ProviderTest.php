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

use Finecho\Logistics\Providers\Kuaidi100;
use PHPUnit\Framework\TestCase;

/**
 * Class Kuaidi100ProviderTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Kuaidi100ProviderTest extends TestCase
{
    public function setUp()
    {
        \Mockery::globalHelpers();
    }

    public function testGetLogisticsInfo()
    {
        $config = [
            'provider' => 'kuaidi100',

            'kuaidi100' => [
                'app_code' => 'xxxxxxx',
                'customer' => 'xxxxxxx',
            ],];

        $response = [
            'msg' => 'OK',
            'nu' => '7521488',
            'state' => '3',
            'data' => [['time' => '2019-05-23', 'ftime' => '2019-05-23', 'remark' => '已发货'], ['time' => '2019-05-23', 'ftime' => '2019-05-23', 'remark' => '已签收']],
            'courier' => '',
            'courierPhone' => '',
        ];

        $kuaidi100 = \Mockery::mock(Kuaidi100::class . '[post]', [$config])->shouldAllowMockingProtectedMethods();

        $params = [
            'customer' => 'xxxxxxx',
            'param' => \json_encode(['com' => 'suning', 'num' => '7521488']),
            'sign' => \strtoupper(\md5(\json_encode(['com' => 'suning', 'num' => '7521488']).$config['kuaidi100']['app_code'].$config['kuaidi100']['customer'])),
        ];

        $kuaidi100->shouldReceive('post')->with(
            'http://poll.kuaidi100.com/poll/query.do', $params, [])->once()->andReturn($response);

        $this->assertSame($response, $kuaidi100->order('7521488', '苏宁')->getOriginal());
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
