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
 * @namespace Proem\Api\Signal\Event
 */
namespace Proem\Api\Signal\Event;

use Proem\Util\Opt\Options,
    Proem\Util\Opt\Option,
    Proem\Signal\Event\Template;

/**
 * The standard event implementation
 */
class Standard implements Template
{
    /**
     * @use Proem\Util\Opt\Options
     */
    use Options;

    /**
     * Store params
     *
     * @var array
     */
    private $params;

    /**
     * Store target
     *
     * @var object $target
     */
    private $target = null;

    /**
     * Store the method
     *
     * @var string
     */
    private $method = null;

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
     * Set the target.
     *
     * The target should be an instance of whatever object
     * this event was triggered from.
     *
     * @param object $target
     * @return Proem\Signal\Event\Template
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Retrieve target.
     *
     * @return object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the method.
     *
     * The method should be a string representing the name of
     * the method which has triggered this event.
     *
     * @param string $method
     * @return Proem\Signal\Event\Template
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Retrieve method
     *
     * @return object
     */
    public function getMethod()
    {
        return $this->method;
    }

}
