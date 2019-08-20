<?php

/*
 * This file is part of the finehco/logistics.
 *
 * (c) finecho <liuhao25@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Finecho\Logistics;

/**
 * Interface LogisticsInterface.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
interface OrderInterface
{
    /**
     * Get logistics query status code.
     *
     * @return int
     */
    public function getCode();

    /**
     * Get the logistics query status description.
     *
     * @return string
     */
    public function getMsg();

    /**
     * Get the name of the logistics company.
     *
     * @return string
     */
    public function getCompany();

    /**
     * Get the logistics order number.
     *
     * @return string
     */
    public function getNo();

    /**
     * Get logistics status.
     *
     * @return string
     */
    public function getStatus();

    /**
     * Get logistics display status.
     *
     * @return string
     */
    public function getDisplayStatus();

    /**
     * Get logistics abstract status.
     *
     * @return array
     */
    public function getAbstractStatus();

    /**
     * Get the courier name.
     *
     * @return string
     */
    public function getCourier();

    /**
     * Get the courier phone number.
     *
     * @return string
     */
    public function getCourierPhone();

    /**
     * Get a list of logistics.
     *
     * @return array
     */
    public function getList();
}
