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
 * @namespace Proem\Api\Dispatch
 */
namespace Proem\Api\Dispatch;

use Proem\Service\Manager\Template as Manager,
    Proem\Routing\Signal\Event\RouteMatch,
    Proem\Routing\Signal\Event\RouteExhausted;

/**
 * Proem\Dispatch\Stage
 */
class Stage
{
    /**
     * Store the Services Manager.
     *
     * @var Proem\Services\Manager\Template
     */
    protected $assets;

    /**
     * Store a *Dispatchable* flag.
     */
    protected $dispatchable = false;

    /**
     * This object represents the Dispatch staging area.
     *
     * It is here that the Dispatch process puts on it's show.
     */
    public function __construct(Manager $assets)
    {
        $this->assets = $assets;

        $this->registerRouteMatchListener();
        $this->registerRouteExhaustedListener();

        if ($this->processRoutes() && $this->dispatchable) {
            $this->dispatch();
        }
    }

    /**
     * Register a callback ready to listen for the route.match Event.
     */
    private function registerRouteMatchListener()
    {
        if ($this->assets->has('events')) {
            $this->assets->get('events')->attach([
                'name'      => 'route.match',
                'callback'  => [$this, 'isDispatchable']
            ]);
        }
    }

    /**
     * Register a callback ready to listen for the route.exhausted Event.
     */
    private function registerRouteExhaustedListener()
    {
        if ($this->assets->has('events')) {
            $this->assets->get('events')->attach([
                'name'      => 'route.exhausted',
                'callback'  => [$this, 'routesExhausted']
            ]);
        }
    }

    /**
     * Iterate through matching routes and trigger a match.route Event
     * on each iteration.
     */
    private function processRoutes()
    {
        if ($this->assets->has('router') && $this->assets->has('events')) {
            $router = $this->assets->get('router');
            while ($payload = $router->route()) {
                $this->assets->get('events')->trigger([
                    'name'      => 'route.match',
                    'event'     => (new RouteMatch())->setPayload($payload),
                    'callback'  => function($e) {
                        if ($e) {
                            $this->dispatchable = true;
                        }
                    }
                ]);

                if ($this->dispatchable) {
                    return true;
                }
            }

            // All routess have been exhaasted
            $this->assets->get('events')->trigger([
                'name'      => 'route.exhausted',
                'event'     => (new RouteExhausted())
            ]);
        }
    }

    /**
     * Dispatch the payload.
     */
    private function dispatch()
    {
        if ($this->assets->has('dispatch')) {
            $this->assets->get('dispatch')->dispatch();
        }
    }

    /**
     * Listen for the route.event Event. If the RouteMatch event passed in
     * produces a Dispatchable Payload return a Dispatchable flag and Dispatch
     * the object.
     */
    public function isDispatchable($e)
    {
        if ($this->assets->has('dispatch')) {
            return $this->assets->get('dispatch')
                ->setPayload($e->getPayload())
                ->isDispatchable();
        }
    }

    /**
     * Listen for a route.exhuasted Event.
     *
     * If triggered, dispatch a 404
     */
    public function routesExhausted()
    {
        if ($this->assets->has('response')) {
            $this->assets->get('response')
                ->setHttpStatus(404)
                ->appendToBody('<h3>404 - Page Not Found</h3>');
        }
    }
}
