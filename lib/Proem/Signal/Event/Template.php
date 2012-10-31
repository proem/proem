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

/**
 * Interface that all events must implement.
 */
interface Template
{
    /**
     * Instantiate the Event and set it's name.
     */
    public function __construct($name);

    /**
     * Set the halt queue flag to true
     */
    public function haltQueue();

    /**
     * Check to see if the haltQueue flag is true
     */
    public function isQueueHalted();

    /**
     * Set a param
     *
     * @param string $key
     * @param mixed $param
     * @return Proem\Signal\Event\Template
     */
    public function setParam($key, $param);

    /**
     * Retrieve a parameter (or some default value) by key
     *
     * @param string $key
     * @param mixed $default Default value returned if $key does not exist
     * @return mixed
     */
    public function getParam($key, $default);

    /**
     * Set params
     *
     * @param array $params
     * @return Proem\Signal\Event\Template
     */
    public function setParams(array $params);

    /**
     * Retrieve any parameters passed to this Event
     *
     * @return array
     */
    public function getParams();

    /**
     * Set the name
     *
     * The name of the event that was triggered.
     *
     * @param string $name
     * @return Proem\Signal\Event\Template
     */
    public function setName($name);

    /**
     * Retrieve the event name
     *
     * @return string The name of the triggered event.
     */
    public function getName();
}
