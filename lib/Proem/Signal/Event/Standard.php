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
 * @namespace Proem\Signal\Event
 */
namespace Proem\Signal\Event;

use Proem\Signal\Event\Template;

/**
 * The standard event implementation
 */
class Standard implements Template
{
    /**
     * Halted queue flag
     *
     * @var bool
     */
    protected $haltedQueue = false;

    /**
     * Halt the queue *early* flag.
     *
     * @var bool
     */
    protected $haltedQueueEarly = false;

    /**
     * Store params
     *
     * @var array
     */
    protected $params;

    /**
     * Store the name of the event
     *
     * @var string $name
     */
    protected $name = null;

    /**
     * Instantiate the event and set it's name.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Set the halt queue flag to true
     *
     * @param bool $early If true, the queue will be halted prior to the triggers callback being executed
     */
    public function haltQueue($early = false)
    {
        if ($early) {
            $this->haltedQueueEarly = true;
        }

        $this->haltedQueue = true;
        return $this;
    }

    /**
     * Check to see if the haltedQueueEarly flag is true
     */
    public function isQueueHaltedEarly()
    {
        return $this->haltedQueueEarly;
    }

    /**
     * Check to see if the haltedQueue flag is true
     */
    public function isQueueHalted()
    {
        return $this->haltedQueue;
    }

    /**
     * Set a param
     *
     * @param string $index
     * @param mixed $value
     * @return Proem\Signal\Event\Template
     */
    public function setParam($index, $value)
    {
        $this->params[$index] = $value;
        return $this;
    }

    /**
     * Retrieve a parameter.
     *
     * @return mixed
     */
    public function getParam($index, $default = null)
    {
        if (isset($this->params[$index])) {
            return $this->params[$index];
        }

        return $default;
    }

    /**
     * Check for the existance of a parameter.
     *
     * @return bool
     */
    public function has($index)
    {
        return isset($this->params[$index]);
    }

    /**
     * Set params
     *
     * @param array $params
     * @return Proem\Signal\Event\Template
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Retrieve any parameters passed to this Event
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the name
     *
     * Set the name of the event being triggered.
     *
     * @param string $name
     * @return Proem\Signal\Event\Template
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retrieve the event name.
     *
     * @return string The name of the event triggered.
     */
    public function getName()
    {
        return $this->name;
    }
}
