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

use Finecho\Logistics\Traits\HasAttributes;

/**
 * Class Order.
 *
 * @author finecho <liuhao25@foxmail.com>
 */
class Order implements \ArrayAccess, OrderInterface, \JsonSerializable, \Serializable
{
    use HasAttributes;

    /**
     * User constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->getAttribute('code');
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->getAttribute('msg');
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->getAttribute('company');
    }

    /**
     * @return string
     */
    public function getNo()
    {
        return $this->getAttribute('no');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getAttribute('status');
    }

    /**
     * @return string
     */
    public function getDisplayStatus()
    {
        return $this->getAttribute('display_status');
    }

    /**
     * @return string
     */
    public function getAbstractStatus()
    {
        return $this->getAttribute('abstract_status');
    }

    /**
     * @return string
     */
    public function getCourier()
    {
        return $this->getAttribute('courier');
    }

    /**
     * @return string
     */
    public function getCourierPhone()
    {
        return $this->getAttribute('courier_phone');
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->getAttribute('list');
    }

    /**
     * Get the original attributes.
     *
     * @return array
     */
    public function getOriginal()
    {
        return $this->getAttribute('original');
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->attributes;
    }

    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * Constructs the object.
     *
     * @see  https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->attributes = \unserialize($serialized) ?: [];
    }
}
