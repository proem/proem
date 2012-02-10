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
 * @namespace Proem\Api\Routing
 */
namespace Proem\Api\Routing;

use Proem\Routing\Route;

/**
 * Proem\Api\Routing\Router
 */
class Router
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
    public function __construct($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * Store route objects
     */
    public function map($name, Route $route)
    {
        $this->_routes[$name] = $route;
        return $this;
    }

    /**
     * Test routes for matching route and found return a DispatchPayload
     */
    public function route()
    {
        foreach ($this->_routes as $name => $route) {
            $route->process($this->requestUri);
            if ($route->isMatch() && $route->getPayload()->isDispatchable()) {
                break;
            }
        }
        return $route->getPayload();
    }
}
