<?php

/*
 * This file is part of the finecho/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics\Providers;

use Finecho\Logistics\Contracts\ProviderInterface;
use Finecho\Logistics\Exceptions\InvalidArgumentException;
use Finecho\Logistics\Interfaces\LogisticsStatus;

/**
 * Class Base.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
abstract class AbstractProvider implements ProviderInterface, LogisticsStatus
{
    const DEFAULT_TIMEOUT = 5.0;

    const GLOBAL_SUCCESS_CODE = 200;

    const GLOBAL_SUCCESS_MSG = 'OK';

    protected $company = '';

    /**
     * @var array
     */
    protected $config;

    /**
     * Base constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param      $no
     * @param null $company
     *
     * @return \Finecho\Logistics\Order
     */
    abstract protected function query($no, $company = null);

    /**
     * @param $logisticsOrder
     *
     * @return \Finecho\Logistics\Order
     */
    abstract protected function mapLogisticsOrderToObject($logisticsOrder);

    /**
     * @param array $list
     *
     * @return array
     */
    abstract protected function resetList($list);

    /**
     * @param string $company
     *
     * @return string
     *
     * @throws \Finecho\Logistics\Exceptions\InvalidArgumentException
     */
    public function getLogisticsCompanyAliases($company)
    {
        $companies = \json_decode(\file_get_contents(__DIR__.'/../companies.json'), true);

        $index = \array_search($company, \array_column($companies, 'name'));

        if (false !== $index) {
            return $companies[$index]['aliases'][\strtolower($this->getProviderName())];
        }

        throw new InvalidArgumentException();
    }

    /**
     * @return array
     */
    public function companies()
    {
        $companies = \json_decode(\file_get_contents(__DIR__.'/../companies.json'), true);

        return \array_column($companies, 'name');
    }

    /**
     * @param      $status
     *
     * @return array
     */
    public function abstractLogisticsStatus($status)
    {
        switch ($status) {
            case self::LOGISTICS_STATUS_NO_RECORD:
                break;
            case self::LOGISTICS_STATUS_ERROR:
                break;
            case self::LOGISTICS_STATUS_COURIER_RECEIPT:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_IN_TRANSIT:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_DELIVERING:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_SIGNED:
                $hasProgresses = true;
                $progressCutoff = true;
                $isSigned = true;
                break;
            case self::LOGISTICS_STATUS_DELIVERY_FAILED:
                $hasProgresses = true;
                $progressCutoff = true;
                $isTroublesome = false;
                break;
            case self::LOGISTICS_STATUS_TROUBLESOME:
                $isTroublesome = false;
                break;
            case self::LOGISTICS_STATUS_RETURN_RECEIPT:
                $hasProgresses = true;
                $progressCutoff = true;
                $isTroublesome = true;
                $isReturn = true;
                break;
            case self::LOGISTICS_STATUS_REJECTED:
                $hasProgresses = true;
                $progressCutoff = true;
                break;
            case self::LOGISTICS_STATUS_SEND_BACK:
                $hasProgresses = true;
                $progressCutoff = true;
                $isReturn = true;
                break;
            case self::LOGISTICS_STATUS_TIMEOUT:
                $hasProgresses = true;
                $progressCutoff = true;
                break;
            case self::LOGISTICS_STATUS_TO_BE_CLEARED:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_CLEARANCE:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_CLEARED:
                $hasProgresses = true;
                break;
            case self::LOGISTICS_STATUS_CUSTOMS_CLEARANCE_ABNORMALITY:
                $hasProgresses = true;
                $isTroublesome = true;
                break;
            case self::LOGISTICS_STATUS_AWAIT_SIGN:
                $hasProgresses = true;
                break;
        }

        return [
            'has_progresses' => isset($hasProgresses) ? $hasProgresses : false,
            'progress_cutoff' => isset($progressCutoff) ? $progressCutoff : false,
            'is_signed' => isset($isSigned) ? $isSigned : false,
            'is_troublesome' => isset($isTroublesome) ? $isTroublesome : false,
            'is_return' => isset($isReturn) ? $isReturn : false,
        ];
    }
}
