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
 * @namespace Proem\Api\Routing\Router
 */
namespace Proem\Api\Routing\Router;

use Proem\Routing\Router\Template,
    Proem\Routing\Route\Template as Route,
    Proem\Routing\Signal\Event\RouteMatch,
    Proem\Signal\Manager\Template as SignalManager;

/**
 * Proem\Api\Routing\Router\Standard
 */
class Standard implements Template
{
    /**
     * Store the request url
     */
    private $requestUrl;

    /**
     * Store our routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Setup
     *
     * @param string $requestUri
     */
    public function __construct($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * Store route objects
     */
    public function map($name, Route $route)
    {
        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * Recurse through the Routes until a match is found.
     *
     * When called multiple times (in a loop for instance)
     * this method will return a new matching route until
     * all routes have been processed.
     *
     * Once exhausted this function returns false and the
     * internal pointer is reset so the Router can be used
     * again.
     */
    public function route()
    {
        if ($route = current($this->routes)) {
            next($this->routes);
            $route->process($this->requestUrl);

            if ($route->isMatch() && $route->getPayload()->isPopulated()) {
                return $route->getPayload();
            } else {
                return $this->route();
            }
        }
        reset($this->routes);
        return false;
    }
}
