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
 * @namespace Proem\Api\Filter
 */
namespace Proem\Api\Filter;

use Proem\Filter\Event\Generic as Event,
    Proem\Util\Storage\Queue,
    Proem\Service\Manager as ServiceManager;

/**
 * Proem\Api\Filter\Manager
 */
class Manager
{
    /**
     * Constants used to priorities default events
     */
    const RESPONSE_EVENT_PRIORITY    = 300;
    const REQUEST_EVENT_PRIORITY     = 200;
    const ROUTE_EVENT_PRIORITY       = 100;
    const DISPATCH_EVENT_PRIORITY    = 0;

    /**
     * @var Proem\Util\Storage\Queue $queue
     *
     * Store the Queue object
     */
    private $queue;

    /**
     * Store an asset manager
     *
     * @var Proem\Api\Service\Manager
     */
    private $serviceManager;

    /**
     * Instantiate the Filter Manager
     *
     * @param Proem\Api\Service\Manager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->queue            = new Queue;
        $this->serviceManager   = $serviceManager;
    }

    /**
     * Insert an event into the queue
     *
     * @return Proem\Api\Filter\Chain
     */
    public function attachEvent(Event $event, $priority = self::RESPONSE_EVENT_PRIORITY)
    {
        $this->queue->insert($event, $priority);
        return $this;
    }

    /**
     * Rewind the queue to the start and return the first event
     *
     * @return Proem\Api\Filter\Event\Generic
     */
    public function getInitialEvent()
    {
        return $this->queue->current();
    }

    /**
     * Retrieve the next event in the filter
     *
     * @return Proem\Api\Filter\Event\Generic
     */
    public function getNextEvent()
    {
        $this->queue->next();
        return $this->queue->current();
    }

    /**
     * Retrieve the Service Manager
     *
     * @return Proem\Api\Service\Manager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Check to see if there are more events left in the filter.
     *
     * @return bool
     */
    public function hasEvents()
    {
        return $this->queue->valid();
    }

    /**
     * Get the first event in the filter and execute it's init() method
     */
    public function init()
    {
        return $this->getInitialEvent()->init($this);
    }

}
