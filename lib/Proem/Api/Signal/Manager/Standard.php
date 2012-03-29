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
 * @namespace Proem\Api\Signal\Manager
 */
namespace Proem\Api\Signal\Manager;

use Proem\Util\Storage\Queue,
    Proem\Util\Process\Callback,
    Proem\Util\Opt\Options,
    Proem\Util\Opt\Option,
    Proem\Signal\Event\Standard as Event,
    Proem\Signal\Manager\Template;

/**
 * Proem\Api\Signal\Manager\Standard
 *
 * Manage the registration of and triggering of Events.
 */
class Standard implements Template
{
    /**
     * Make use of the Options trait
     */
    use Options;

    /**
     * Stores listeners in a hash of priority queues.
     *
     * @var array $queues
     */
    private $queues = [];

    /**
     * Store listener callbacks
     */
    private $callbacks = [];

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
    public function attach(array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required(),
            'callback'  => (new Option())->required()->type('callable'),
            'priority'  => 0
        ], $options);

        $key = md5(microtime());
        $this->callbacks[$key] = $ops->callback;

        if (is_array($ops->name)) {
            foreach ($ops->name as $event) {
                if (isset($this->queues[$event])) {
                    $this->queues[$event]->insert($key, $ops->priority);
                } else {
                    $this->queues[$event] = new Queue;
                    $this->queues[$event]->insert($key, $ops->priority);
                }
            }
        } else {
            if (isset($this->queues[$ops->name])) {
                $this->queues[$ops->name]->insert($key, $ops->priority);
            } else {
                $this->queues[$ops->name] = new Queue;
                $this->queues[$ops->name]->insert($key, $ops->priority);
            }
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
     *       'event'     => (new Option(new Event))->object('\Proem\Signal\Event\Template')
     *   ], $options);
     * </code>
     */
    public function trigger(array $options)
    {
        $ops = $this->setOptions([
            'name'      => (new Option())->required(),
            'params'    => (new Option())->type('array'),
            'callback'  => (new Option())->type('callable'),
            'target'    => (new Option())->type('object'),
            'method'    => (new Option())->type('string'),
            'event'     => (new Option(new Event))->object('\Proem\Signal\Event\Template')
        ], $options);

        if (isset($this->queues[$ops->name])) {
            foreach ($this->queues[$ops->name] as $key) {
                $event = $ops->event;
                if ($event instanceof \Proem\Signal\Event\Template) {
                    if ($ops->has('params')) {
                        $event->setParams($ops->params);
                    }
                }
                $event->setTarget($ops->target);
                $event->setMethod($ops->method);
                if ($return = (new Callback($this->callbacks[$key], $event))->call()) {
                    if ($ops->has('callback')) {
                        (new Callback($ops->callback, $return))->call();
                    }
                }
            }
        }

        return $this;
    }
}
