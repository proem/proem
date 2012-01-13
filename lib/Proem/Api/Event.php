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
 * @namespace Proem\Api
 */
namespace Proem\Api;

use Proem\Util\Queue,
    Proem\Util\Callback,
    Proem\Util\Options,
    Proem\Util\Options\Option;

/**
 * Proem\Api\Event
 *
 * Manage the registration opf and triggering of custom events.
 */
class Event
{
    use Options;
    /**
     * Store reigistered events in a priority queue
     */
    private $queue;

    /**
     * Instantiate the Event manager
     */
    public function __construct()
    {
        $this->queue = new Queue;
    }

    public function attach(array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required(),
            'callback'  => (new Option())->required()->type('callable'),
            'priority'  => 0
        ], $options);

        $this->queue->insert(array($ops->name, $ops->callback), $ops->priority);
    }

    public function trigger(array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required()->unless('event'),
            'params'    => (new Option())->required()->unless('event')->type('array'),
            'callback'  => (new Option())->type('callable'),
            'context'   => (new Option())->type('object'),
            'event'     => (new Option())->object('\Proem\Event\Base')
        ], $options);

        foreach ($this->queue as $event) {
            if ($event[0] == $ops->name) {
                $eventObj = $ops->event;
                $eventObj = new $eventObj(['name' => $ops->name, 'params' => $ops->params]);
                if ($return = $event[1]($eventObj)) {
                    if (isset($ops->callback)) {
                        $callback = new Callback($ops->callback, $return);
                        $callback->call();
                    }
                }
            }
        }
    }
}
