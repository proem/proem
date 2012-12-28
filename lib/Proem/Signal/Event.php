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
 * @namespace Proem\Signal
 */
namespace Proem\Signal;

use Proem\Signal\EventInterface;
use Proem\Util\Structure\DataCollectionTrait;

/**
 * A default event.
 */
class Event implements EventInterface
{
    /**
     * Use the generic DataCollectionTrait trait
     *
     * This provides implementations for the DataAccessInterface, Iterator and Serializable
     */
    use DataCollectionTrait;

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
     * Store the name of the event
     *
     * @var string $name
     */
    protected $name = null;

    /**
     * Instantiate the event, setting it's name and optionaly some parameters.
     *
     * @param string $name
     * @param array $data
     */
    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
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
     * Retrieve the event name.
     *
     * @return string The name of the event triggered.
     */
    public function getName()
    {
        return $this->name;
    }
}
