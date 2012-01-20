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
 * @namespace Proem\Api\Signal
 */
namespace Proem\Api\Signal;

use Proem\Util\Storage\Queue,
    Proem\Util\Process\Callback,
    Proem\Util\Opt\Options,
    Proem\Util\Opt\Option,
    Proem\Signal\Event\Generic as Event;

/**
 * Proem\Api\Signal\Manager
 *
 * Manage the registration of and triggering of Events.
 */
class Manager
{
    /**
     * Make use of the Options trait
     */
    use Options;

    /**
     * Stores Events in a hash of priority queues.
     *
     * @var array $queues
     */
    private $queues = [];

    /**
     * Register a listener attached to a particular named Event.
     *
     * All listeners are store within a hash of priority queues. Each Queue contains
     * all listeners registred to listen to a particular Event. The priority Queue
     * enables lsiteners to respond to an Event according to the priority that is set.
     *
     * Default priority is 0, the higher the number of the priority the earlier the
     * listener will respond.
     *
     * @param array $options
     * <code>
     *   $ops = $this->setOptions([
     *       'name'      => (new Option())->required(),                     // The name of the event to listen to
     *       'callback'  => (new Option())->required()->type('callable'),   // The Callable that will be executed when the event occurs
     *       'priority'  => 0                                               // The priority at which this listner will be executed
     *   ], $options);
     * </code>
     */
    public function attach(Array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required(),
            'callback'  => (new Option())->required()->type('callable'),
            'priority'  => 0
        ], $options);

        if (isset($this->queues[$ops->name])) {
            $this->queues[$ops->name]->insert($ops->callback, $ops->priority);
        } else {
            $this->queues[$ops->name] = new Queue;
            $this->queues[$ops->name]->insert($ops->callback, $ops->priority);
        }

        return $this;
    }

    /**
     * Trigger the execution of all event listeners attached to an Event.
     *
     * @param array $options
     * <code>
     *   $ops = $this->setOptions([
     *       'name'      => (new Option())->required(),
     *       'params'    => (new Option())->type('array'),
     *       'callback'  => (new Option())->type('callable'),
     *       'target'    => (new Option())->type('object'),
     *       'method'    => (new Option())->type('string'),
     *       'event'     => (new Option(new Event))->object('\Proem\Signal\Event\Generic')
     *   ], $options);
     * </code>
     */
    public function trigger(Array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required(),
            'params'    => (new Option())->type('array'),
            'callback'  => (new Option())->type('callable'),
            'target'    => (new Option())->type('object'),
            'method'    => (new Option())->type('string'),
            'event'     => (new Option(new Event))->object('\Proem\Signal\Event\Generic')
        ], $options);

        if (isset($this->queues[$ops->name])) {
            $this->queues[$ops->name]->rewind();
            foreach ($this->queues[$ops->name] as $event) {
                $eventObj = $ops->event;
                if (isset($ops->params)) {
                    $eventObj = new $eventObj(['params' => $ops->params]);
                } else {
                    $eventObj = new $eventObj;
                }
                $eventObj->setTarget($ops->target);
                $eventObj->setMethod($ops->method);
                if ($return = $event($eventObj)) {
                    if (isset($ops->callback)) {
                        (new Callback($ops->callback, $return))->call();
                    }
                }
            }
        }

        return $this;
    }
}
