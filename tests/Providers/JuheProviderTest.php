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

use Finecho\Logistics\Providers\Juhe;
use PHPUnit\Framework\TestCase;

/**
 * Class JuheProviderTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class JuheProviderTest extends TestCase
{
    public function setUp()
    {
        \Mockery::globalHelpers();
    }

    public function testGetLogisticsInfo()
    {
        $config = [
            'provider' => 'juhe',

            'juhe' => [
                'app_code' => 'xxxxxxx',
            ], ];

        $response = [
            'resultcode' => 200,
            'msg' => 'OK',
            'result' => [
                'no' => '7521488',
                'type' => 'zto',
                'list' => [['datetime' => '2019-05-23', 'remark' => '已发货'], ['datetime' => '2019-05-24', 'remark' => '已签收']],
                'status_detail' => null,
                'courier' => '',
                'courierPhone' => '',
            ],
        ];

        $juhe = \Mockery::mock(Juhe::class.'[get]', [$config])->shouldAllowMockingProtectedMethods();

        $query = ['no' => '7521488', 'key' => 'xxxxxxx', 'com' => 'yt'];

        $juhe->shouldReceive('get')->with(
            'http://v.juhe.cn/exp/index', $query, [])->once()->andReturn($response);

        $this->assertSame($response, $juhe->order('7521488', '圆通快递')->getOriginal());
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
