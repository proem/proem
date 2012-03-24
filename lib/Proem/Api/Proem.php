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

use Proem\Service\Manager as ServiceManager,
    Proem\Signal\Manager as SignalManager,
    Proem\Service\Asset\Generic as Asset,
    Proem\Bootstrap\Filter\Event,
    Proem\Bootstrap\Signal\Event\Bootstrap,
    Proem\Filter\Manager as FilterManager,
    Proem\Ext\Generic as Extension,
    Proem\Ext\Module\Generic as Module,
    Proem\Ext\Plugin\Generic as Plugin;

/**
 * Proem\Api\Proem
 *
 * The Proem boostrap wrapper (eventually)
 */
class Proem
{
    /**
     * Store the framework version
     */
    const VERSION = '0.2.0';

    /**
     * Store events
     *
     * @var Proem\Api\Signal\Manager
     */
    private $events;

    private $serviceManager;

    /**
     * Register Modules / Plugins
     */
    private function attachExtension(Extension $extension, $event = 'proem.init', $priority = 0)
    {
        $this->attachEventListener([
            'name'      => $event,
            'priority'  => $priority,
            'callback'  => function($e) use ($extension) {
                $extension->init($e->getServiceManager(), $e->getEnvironment());
            }
        ]);
        return $this;
    }

    /**
     * Setup bootstraping
     */
    public function __construct()
    {
        $this->events = new Asset;
        $this->events->set('\Proem\Signal\Manager', $this->events->single(function($asset) {
            return new SignalManager;
        }));

        $this->serviceManager = new ServiceManager;
    }

    /**
     * Attach a listener to the Signal Event Manager
     */
    public function attachEventListener(Array $listener)
    {
        $this->events->get()->attach($listener);
        return $this;
    }

    /**
     * Attach a series of event to the Signal Event Manager
     */
    public function attachEventListeners(Array $listeners)
    {
        foreach ($listeners as $listener) {
            $this->attachEventListener($listener);
        }
        return $this;
    }

    /**
     * Register a Plugin
     */
    public function attachPlugin(Extension $plugin, $event = 'proem.init', $priority = 0)
    {
        return $this->attachExtension($plugin);
    }

    /**
     * Register a Module
     */
    public function attachModule(Extension $module, $event = 'proem.init', $priority = 0)
    {
        return $this->attachExtension($module, $event, $priority);
    }

    /**
     * Setup and execute the Filter Manager
     */
    public function init($environment = null)
    {
        $this->serviceManager->set('events', $this->events);

        $this->events->get()->trigger([
            'name'  => 'proem.init',
            'event' => (new Bootstrap)
                ->setServiceManager($this->serviceManager)
                ->setEnvironment($environment)
        ]);

        (new FilterManager($this->serviceManager))
            ->attachEvent(new Event\Response, FilterManager::RESPONSE_EVENT_PRIORITY)
            ->attachEvent(new Event\Request, FilterManager::REQUEST_EVENT_PRIORITY)
            ->attachEvent(new Event\Route, FilterManager::ROUTE_EVENT_PRIORITY)
            ->attachEvent(new Event\Dispatch, FilterManager::DISPATCH_EVENT_PRIORITY)
            ->init();
    }
}
