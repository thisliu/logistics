<?php

/*
 * This file is part of the finehco/logistics-inquiry.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\LogisticsInquiry\Tests;

use Finecho\LogisticsInquiry\LogisticsInquiry;
use Finecho\LogisticsInquiry\Providers\Aliyun;
use GuzzleHttp\Psr7\Response;
use Mockery\Exception\BadMethodCallException;
use PHPUnit\Framework\TestCase;

/**
 * Class AliyunLogisticsInquiryTest.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class AliyunLogisticsInquiryTest extends TestCase
{
    public function testGetAliyunLogisticsInquiryInfo()
    {
        $config = [
            'provider' => 'aliyun',

            'aliyun' => [
                'app_code' => 'xxxxxxx',
            ], ];

        $response = new Response(200, [], '{"success": true}');

        $aliyunLogistics = \Mockery::mock(Aliyun::class, $config);
        $aliyunLogistics->shouldReceive('show')->with('7521488')->andReturn($response);

        $this->assertSame($response, $aliyunLogistics->show('7521488'));
    }

    public function testGetAliyunLogisticsInquiryCompany()
    {
        $config = [
            'provider' => 'aliyun',

            'aliyun' => [
                'app_code' => 'xxxxxxx',
            ], ];

        $response = new Response(200, [], '{"success": true}');

        $aliyunLogistics = \Mockery::mock(Aliyun::class, $config);
        $aliyunLogistics->shouldReceive('companies')->andReturn($response);

        $this->assertSame($response, $aliyunLogistics->companies());
    }

    public function testFactoryCanGetLogisticsInquiryInfo()
    {
        $config = [
            'provider' => 'aliyun',

            'aliyun' => [
                'app_code' => 'xxxxxxx',
            ], ];

        $response = new Response(200, [], '{"success": true}');

        $logistics = \Mockery::mock(LogisticsInquiry::class, $config);

        $logistics->shouldReceive('info')->andThrow(new BadMethodCallException());
        $logistics->shouldReceive('show')->with('7521488')->andReturn($response);

        $this->assertSame($response, $logistics->show('7521488'));
    }
}
