<?php

/*
 * This file is part of the finehco/logistics.
 *
 * (c) Aliliin <PhperAli@Gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Tests\Providers;

use Finecho\Logistics\Providers\Kdniao;
use PHPUnit\Framework\TestCase;

/**
 * Class KdniaoProviderTest.
 *
 * @author Aliliin <PhperAli@Gmail.com>
 */
class KdniaoProviderTest extends TestCase
{
    public function setUp()
    {
        \Mockery::globalHelpers();
    }

    public function testGetLogisticsInfo()
    {
        $config = [
            'provider' => 'kdniao',

            'kdniao'   => [
                'app_code' => 'd7696d82-95d5-4922-ab95-4e0adee0fe8c',
                'customer' => '1270293',
            ]];
        $response = '{
        "LogisticCode": "805741929402797742",
        "ShipperCode": "YTO",
         "Traces": [
            {
                "AcceptStation": "【山东省淄博市淄川区三部公司】 已收件",
                "AcceptTime": "2019-05-07 18:02:27"
            },
            {
                "AcceptStation": "【山东省淄博市淄川区三部公司】 已打包",
                "AcceptTime": "2019-05-07 18:33:15"
            },
            {
                "AcceptStation": "【山东省淄博市淄川区三部】 已发出 下一站 【山东省淄博市公司】",
                "AcceptTime": "2019-05-07 19:05:04"
            },
            {
                "AcceptStation": "【济南转运中心】 已收入",
                "AcceptTime": "2019-05-07 22:37:27"
            },
            {
                "AcceptStation": "【济南转运中心】 已发出 下一站 【长沙转运中心】",
                "AcceptTime": "2019-05-07 22:41:27"
            },
            {
                "AcceptStation": "【长沙转运中心】 已收入",
                "AcceptTime": "2019-05-08 22:47:28"
            },
            {
                "AcceptStation": "【长沙转运中心】 已发出 下一站 【湖南省长沙市火车站公司】",
                "AcceptTime": "2019-05-08 23:17:46"
            },
            {
                "AcceptStation": "【湖南省长沙市火车站公司】 派件人: 李海波 派件中 派件员电话18684822604",
                "AcceptTime": "2019-05-09 08:37:02"
            },
            {
                "AcceptStation": "客户 签收人: 邮件收发章 已签收 感谢使用圆通速递，期待再次为您服务",
                "AcceptTime": "2019-05-09 12:44:40"
            }
        ],
        "State": "3",
        "EBusinessID": "1270293",
        "Success": true
        }';

        $response = json_decode($response, true);

        $Kdniao = \Mockery::mock(Kdniao::class . '[post]', [$config])->shouldAllowMockingProtectedMethods();

        $param = ['ShipperCode' => 'YTO', 'LogisticCode' => '805741929402797742'];

        $params = [
            'EBusinessID' => $config['kdniao']['customer'],
            'RequestType' => 8001,
            'DataType'    => 2,
            'RequestData' => \urlencode(\json_encode($param)),
            'DataSign'    => urlencode(base64_encode(md5(\json_encode($param) . $config['kdniao']['app_code']))),
        ];

        $Kdniao->shouldReceive('post')->with(
            'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $params, [])->once()->andReturn($response);

        $this->assertSame($response, $Kdniao->order('805741929402797742', '圆通')->getOriginal());
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
