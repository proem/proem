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
 * @namespace Proem\Filter
 */
namespace Proem\Filter;

use Proem\Service\AssetManagerInterface;
use Proem\Filter\ChainEventInterface;

/**
 * The filter chain manager interface.
 */
interface ChainManagerInterface
{
    /**
     * Instantiate the Filter Manager.
     *
     * This sets up the queues and service manager.
     *
     * @param Proem\Service\AssetManagerInterface
     */
    public function __construct(AssetManagerInterface $assetManager);

    /**
     * Insert an event into the queue
     *
     * @param Proem\Filter\ChainEventInterface $event
     * @param int $priority
     */
    public function attach(ChainEventInterface $event, $priority = 0);

    /**
     * Retreieve the priority queue used to queue events.
     *
     * @return Proem\Util\Structure\PriorityQueue
     */
    public function getQueue();

    /**
     * Get the first event in the filter and execute it's init() method
     *
     * @param array $params
     */
    public function bootstrap(array $params = []);
}
