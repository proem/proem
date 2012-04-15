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
 * The dispatch stage.
 *
 * This object sets up a staging area where the router and dispatcher
 * can put on there show
 */
class Stage
{
    /**
     * Store the Services Manager
     *
     * @var Proem\Services\Manager\Template
     */
    protected $assets;

    /**
     * Store a flag
     */
    protected $dispatchable = false;

    /**
     * Setup the stage and start the dispatch process
     *
     * Within this single construct we register listeners
     * for both the route.macth & route.exhausted events
     *
     * We then start processing the routes. Once the dispatchable
     * flag is true the route is dispatched and execution moves
     * into userland *controller* code
     *
     * @param Proem\Service\Manager\Template $assets
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
    protected function registerRouteMatchListener()
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
    protected function registerRouteExhaustedListener()
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
     *
     * A listener returning the bool true indicates that the payload is
     * dispatchable. This sets the dispatchable flag to true and will
     * exit this method.
     *
     * If all matching routes have been exhausted a route.exhausted event
     * is triggered.
     *
     * @triggers Proem\Routing\Signal\Event\RouteMatch route.match
     * @triggers Proem\Routing\Signal\Event\RouteExhausted route.exhausted
     * @todo Instead of setting a dispatchable flag, a new event could likely be triggered
     */
    protected function processRoutes()
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
    protected function dispatch()
    {
        if ($this->assets->has('dispatch')) {
            $this->assets->get('dispatch')->dispatch();
        }
    }

    /**
     * Listen for the route.match Event.
     *
     * Pass the RouteMatch event to the dispatcher and have it tested
     * to see if it is dispatchable. Return the result.
     *
     * @param Proem\Routing\Signal\Event\RouteMatch $e
     * @return bool
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
     * If triggered, dispatch a very standard default 404
     *
     * @param Proem\Routing\Signal\Event\RouteMatch $e
     */
    public function routesExhausted($e)
    {
        if ($this->assets->has('response')) {
            $this->assets->get('response')
                ->setHttpStatus(404)
                ->appendToBody('<h3>404 - Page Not Found</h3>');
        }
    }
}
