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
 * @namespace Proem\Routing\Router
 */
namespace Proem\Routing;

use Proem\Routing\RouterManagerInterface;
use Proem\Routing\RouteInterface;
use Proem\Http\Request;

/**
 * The standard route manager.
 */
class RouteManager implements RouteManagerInterface
{
    /**
     * Store the request object
     *
     * @var Proem\Http\Request $request
     */
    protected $request;

    /**
     * Store our routes
     *
     * @var array
     */
    protected $routes;

    /**
     * Interested routes.
     *
     * When looking to match a route, only routes indexed by the
     * request method this request involves (and any within *) will
     * be iterated over.
     *
     * This array contains that collection and is built as match()
     * is executed for the first time.
     *
     * @var array
     */
    protected $interestedRoutes = null;

    /**
     * Store a group of shared attributes
     *
     * @var array
     */
    protected $groupAttributes = null;

    /**
     * Default scheme
     *
     * @var string
     */
    protected $defaultScheme;

    /**
     * Setup
     *
     * @param Proem\Http\Request $request
     */
    public function __construct(Request $request, $scheme = 'http')
    {
        $this->request       = $request;
        $this->defaultScheme = $scheme;

        // Store routes index by request method.
        $this->routes  = [
            'http' => [
                '*'      => [],
                'GET'    => [],
                'POST'   => [],
                'PUT'    => [],
                'DELETE' => [],
                'PATCH'  => []
            ],
            'https' => [
                '*'      => [],
                'GET'    => [],
                'POST'   => [],
                'PUT'    => [],
                'DELETE' => [],
                'PATCH'  => []
            ]
        ];
    }

    /**
     * Retrieve routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Group routes by attributes.
     *
     * @param array $attributes
     * @param callable $callback
     */
    public function group(array $attributes, callable $callback)
    {
        $this->groupAttributes = $attributes;
        $callback();
        $this->groupAttributes = null;
        return $this;
    }

    /**
     * Store route objects indexed by request method.
     *
     * @param string $name
     * @param Proem\Routing\RouteInterface $route
     */
    public function attach($name, RouteInterface $route)
    {
        if ($this->groupAttributes !== null) {
            // This route is part of a group of routes that share common attributes.
            // Merge those attributes with the existing options.
            $route->setOptions($this->groupAttributes);
        }

        $options = $route->getOptions();
        $method  = isset($options['method']) ? strtoupper($options['method']) : '*';
        $scheme  = isset($options['scheme']) ? $options['scheme'] : $this->defaultScheme;
        $this->routes[$scheme][$method][$name] = $route;

        return $this;
    }

    /**
     * Iterate through interested routes until a match is found.
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
        // Build array of routes to iterate.
        if ($this->interestedRoutes === null) {
            $scheme = $this->request->getScheme();
            $method = $this->request->getMethod();
            if (isset($this->routes[$scheme][$method])) {
                $this->interestedRoutes = $this->routes[$scheme][$method] + $this->routes[$scheme]['*'];
            } else {
                $this->interestedRoutes = $this->routes[$scheme]['*'];
            }
        }

        if ($route = current($this->interestedRoutes)) {
            next($this->interestedRoutes);

            // If match found, return matching route.
            if ($route->process($this->request) !== false) {
                if ($route->hasCallback()) {
                    $arguments = $route->getPayload();
                    return call_user_func_array($route->getCallback(), $arguments);
                } else {
                    return $route;
                }
            } else {
                // Recurse through the next route.
                return $this->route();
            }
        }

        // All routes exhausted, reset and return false.
        $this->interestedRoutes = null;
        return false;
    }
}
