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

/**
 * Interface that all event managers must implement.
 */
interface EventManagerInterface
{
    /**
     * Remove event listeners from a particular index.
     *
     * Be aware that removeing listeners from the wildcard '*' will not literally
     * remove them from *all* events. If they have been attached to a specifically
     * named event that will need to be removed seperately.
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Retrieve listeners by name
     *
     * @param string $name
     * @return array $listeners
     */
    public function getListeners($name);

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
    public function attach($name, \Closure $callback, $priority = 0);

    /**
     * Trigger the execution of all event listeners attached to a named event.
     *
     * @param Proem\Signal\Event\Standard $event The event being triggered.
     * @param closure $callback A callback that can be used to respond to any response sent back from a listener.
     */
    public function trigger(EventInterface $event, \Closure $callback = null);
}
