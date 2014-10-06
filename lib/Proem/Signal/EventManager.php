<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2014 Tony R Quilkey <trq@proemframework.org>
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

use Proem\Util\Structure\PriorityQueue;
use Proem\Signal\Event;
use Proem\Signal\EventInterface;
use Proem\Signal\EventManagerInterface;

/**
 * Standard event manager implementation.
 *
 * Stores event listeners and provides the functionality
 * required to trigger an event.
 */
class EventManager implements EventManagerInterface
{
    /**
     * Wildcard used when listening for all events
     */
    const WILDCARD = '.*';

    /**
     * Stores listeners in a hash of priority queues.
     *
     * @var array $queues
     */
    protected $queues = [];

    /**
     * Store listener callbacks.
     *
     * @var array callbacks
     */
    protected $callbacks = [];

    /**
     * Store a flag regarding searching for wildcards
     */
    protected $wildcardSearching = false;

    /**
     * Given an event name, this method searches for all possible
     * wildcard matches in the queues. When/If a match is found
     * it will then copy it's key and priority from the wildcards
     * queue into the named event's queue.
     *
     * @param $name The event name
     * @return bool True if match is found
     */
    protected function populateQueueFromWildSearch($name)
    {
        $listenerMatched = false;
        $parts = explode('.', $name);
        while (count($parts)) {
            array_pop($parts);
            $part = implode('.', $parts) . self::WILDCARD;

            if (isset($this->queues[$part])) {
                $listenerMatched = true;
                /**
                 * Add to currently named queue
                 */
                foreach ($this->queues[$part] as $listener) {
                    if (isset($this->queues[$name])) {
                        $this->queues[$name]->insert($listener['key'], $listener['priority']);
                    } else {
                        $this->queues[$name] = new PriorityQueue;
                        $this->queues[$name]->insert($listener['key'], $listener['priority']);
                    }
                }
            }
        }

        return $listenerMatched;
    }

    /**
     * Store a callback index by a generated key
     *
     * @param callable $callback
     * @return string $key
     */
    protected function storeCallback(\Closure $callback)
    {
        $key = md5(microtime());
        $this->callbacks[$key] = $callback;
        return $key;
    }

    /**
     * Store a callback's key and priority in a queue indexed
     * by the event they are attached to.
     *
     * @param $event The name of the event this callback is being attached to
     * @param string $key The key the callback is stored under
     * @priority The priority this callback has within this queue
     */
    protected function pushToQueue($event, $key, $priority)
    {
        $end = substr($event, -2);
        if (isset($this->queues[$event])) {
            if ($end == self::WILDCARD) {
                $this->wildcardSearching = true;
                $this->queues[$event][] = ['key' => $key, 'priority' => $priority];
            } else {
                $this->queues[$event]->insert($key, $priority);
            }
        } else {
            if ($end == self::WILDCARD) {
                $this->wildcardSearching = true;
                $this->queues[$event][] = ['key' => $key, 'priority' => $priority];
            } else {
                $this->queues[$event] = new PriorityQueue;
                $this->queues[$event]->insert($key, $priority);
            }
        }
    }

    /**
     * Remove event listeners from a particular index.
     *
     * Be aware that removeing listeners from the wildcard '*' will not literally
     * remove them from *all* events. If they have been attached to a specifically
     * named event that will need to be removed seperately.
     *
     * @param string $name
     */
    public function remove($name)
    {
        if (isset($this->queues[$name])) {
            unset($this->queues[$name]);
        }
        return $this;
    }

    /**
     * Retrieve listeners by name
     *
     * @param string $name
     * @return array $listeners
     */
    public function getListeners($name)
    {
        $listenerMatched = false;

        /**
         * Do we have an exact match?
         */
        if (isset($this->queues[$name])) {
            $listenerMatched = true;
        }

        /**
         * Optionally search for wildcard matches.
         */
        if ($this->wildcardSearching) {
            if ($this->populateQueueFromWildSearch($name)) {
                $listenerMatched = true;
            }
        }

        $listeners = [];
        if ($listenerMatched) {
            foreach ($this->queues[$name] as $key) {
                $listeners[] = $this->callbacks[$key];
            }
        }

        return $listeners;
    }

    /**
     * Register a listener attached to a particular named event.
     *
     * All listeners have there callbacks firstly stored within an associative array
     * using a unique md5 hash as an index and the callback as it's value.
     *
     * All event names are then stored within an associative array of splpriorityqueues. The
     * index of these arrays is the name of the event while the value inserted into the queue
     * is the above metnioned unique md5 hash.
     *
     * This allows a listener to attach itself to be triggered against multiple events
     * without having multiple copies of the callback being stored.
     *
     * Default priority is 0, the higher the number of the priority the earlier the
     * listener will respond, negative priorities are allowed.
     *
     * The name option can optionally take the form of an array of events for the listener
     * to attach itself with. A wildcard '*' is also provided and will attach the
     * listener to be triggered against all events.
     *
     * Be aware that attaching a listener to the same event multiple times will trigger
     * that listener multiple times. This includes using the wildcard.
     *
     * @param string|array The name(s) of the event(s) to listen for.
     * @param closure $callback The callback to execute when the event is triggered.
     * @param int $priority The priority at which to execute this listener.
     */
    public function attach($name, \Closure $callback, $priority = 0)
    {
        $key = $this->storeCallback($callback);

        if (is_array($name)) {
            foreach ($name as $event) {
                $this->pushToQueue($event, $key, $priority);
            }
        } else {
            $this->pushToQueue($name, $key, $priority);
        }

        return $this;
    }

    /**
     * Trigger the execution of all event listeners attached to a named event.
     *
     * @param Proem\Signal\Event\Standard|string $event The event being triggered.
     * @param closure $callback A callback that can be used to respond to any response sent back from a listener.
     */
    public function trigger($event, \Closure $callback = null)
    {
        $results = [];

        if (!$event instanceof EventInterface) {
            $event = new Event($event);
        }

        if ($listeners = $this->getListeners($event->getName())) {
            foreach ($listeners as $listener) {
                $result = call_user_func($listener, $event);
                if ($result instanceof EventInterface) {
                    // Save result
                    $results[] = clone $result;

                    // Was the queue halted early ?
                    if ($result->isQueueHaltedEarly()) {
                        return $this;
                    }

                    if ($callback !== null) {
                        call_user_func($callback, $result);
                    }

                    // Was the queue halted ?
                    if ($result->isQueueHalted()) {
                        return $this;
                    }
                } else {
                    if ($callback !== null) {
                        call_user_func($callback, $result);
                    }
                }
            }
        }
        if ($results) {
            return $results;
        }

        return false;
    }
}
