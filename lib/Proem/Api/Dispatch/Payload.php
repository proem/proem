<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2012 Tony R Quilkey <trq@proemframework.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


/**
 * @namespace Proem\Api\Dispatch
 */
namespace Proem\Api\Dispatch;

use Proem\Util\Storage\KeyValStore;

/**
 * Proem\Api\Dispatch\Payload
 */
class Payload
{
    /**
     * A flag to keep note as to wether or not this Payload is populated
     *
     * @var bool
     */
    private $populated = false;

    /**
     * Store the actual data.
     *
     * @var array
     */
    private $data = array();

    public function __construct()
    {
        $this->data = new KeyValStore;
    }

    /**
     * Store a parameter.
     *
     * @param string $name
     * @param string|array $value
     * @return Command
     */
    public function setParam($name, $value)
    {
        $this->data->set($name, $value);
        return $this;
    }

    /**
     * Store multiple params.
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $index => $value) {
            $this->setParam($index, $value);
        }

        return $this;
    }

    /**
     * Retrieve a parameter or an optional default.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return $this->data->get($name, $default);
    }

    /**
     * Retrieve all parameters as KeyValStore.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->data;
    }

    /**
     * Is the Payload Populated?
     */
    public function isPopulated()
    {
        return $this->populated;
    }

    /**
     * Set the populated flag
     */
    public function setPopulated()
    {
        $this->populated = true;
    }
}
