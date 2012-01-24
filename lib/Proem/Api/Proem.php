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
    Proem\Service\Asset\Generic as GenericAsset,
    Proem\Bootstrap\Filter\Event\Response,
    Proem\Bootstrap\Filter\Event\Request,
    Proem\Bootstrap\Filter\Event\Route,
    Proem\Bootstrap\Filter\Event\Dispatch,
    Proem\Bootstrap\Signal\Event\Bootstrap,
    Proem\Filter\Chain,
    Proem\Ext\Generic as Extension;

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
    const VERSION = '0.1.2';

    /**
     * Store events
     *
     * @var Proem\Api\Signal\Manager
     */
    private $events;

    /**
     * Store Modules / Plugins
     */
    private $extensions = [];

    /**
     * Bootstrap Extensions
     */
    private function bootstrapExtensions(ServiceManager $serviceManager, $env = null)
    {
        foreach ($this->extensions as $extension) {
            $extension->init($serviceManager, $env);
        }
    }

    /**
     * Setup bootstraping
     */
    public function __construct()
    {
        $this->events = new GenericAsset;
        $this->events->set('\Proem\Signal\Manager', $this->events->single(function($asset) {
            return new SignalManager;
        }));

        $this->events->get()->trigger(['name' => 'init']);
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
            $this->events->get()->attach($listener);
        }
        return $this;
    }

    /**
     * Register Modules / Plugins
     */
    public function registerExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
        return $this;
    }

    /**
     * Retrieve Extensions
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Setup and execute the Filter Chain
     */
    public function init($env = null)
    {
        $serviceManager = new ServiceManager;
        $serviceManager->set('events', $this->events);

        $this->bootstrapExtensions($serviceManager, $env);

        $chain = new Chain($serviceManager);
        $chain
            ->insertEvent(new Response, Chain::RESPONSE_EVENT_PRIORITY)
            ->insertEvent(new Request, Chain::REQUEST_EVENT_PRIORITY)
            ->insertEvent(new Route, Chain::ROUTE_EVENT_PRIORITY)
            ->insertEvent(new Dispatch, Chain::DISPATCH_EVENT_PRIORITY);

        $chain->init();

        $this->events->get()->trigger(['name' => 'shutdown']);
    }
}
