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
 * @namespace Proem\Filter\Manager
 */
namespace Proem\Filter\Manager;

use Proem\Filter\Event\Template as Event;
use Proem\Util\Storage\Queue;
use Proem\Service\Manager\Template as ServiceManager;

/**
 * Interface that service managers must implement.
 */
interface Template
{
    public function setServiceManager(ServiceManager $serviceManager);

    /**
     * Insert an event into the queue
     *
     * @param Proem\Filter\Event\Template $event
     * @param int $priority
     * @return Proem\Filter\Manager\Template
     */
    public function attachEvent(Event $event, $priority);

    /**
     * Rewind the queue to the start and return the first event
     *
     * @return Proem\Filter\Event\Template
     */
    public function getInitialEvent();

    /**
     * Retrieve the next event in the filter
     *
     * @return Proem\Filter\Event\Generic
     */
    public function getNextEvent();

    /**
     * Check to see if there are more events left in the filter.
     *
     * @return bool
     */
    public function hasEvents();

    /**
     * Get the first event in the filter and execute it's init() method
     *
     * @return Proem\Filter\Event\Template
     */
    public function init();
}
