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

/**
 * Class Base.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
abstract class AbstractProvider implements ProviderInterface
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

        if ($index !== false) {
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
}
