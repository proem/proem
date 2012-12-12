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
 * @namespace Proem\Routing\Router
 */
namespace Proem\Routing;

use Proem\Routing\RouteInterface;
use Proem\Http\Request;

/**
 * The route manager interface.
 */
interface RouteManagerInterface
{
    /**
     * Setup
     *
     * @param Proem\Http\Request $request
     */
    public function __construct(Request $request);

    /**
     * Retrieve routes.
     *
     * @return array
     */
    public function getRoutes();

    /**
     * Store route objects indexed by request method.
     *
     * @param string $name
     * @param Proem\Routing\RouteInterface $route
     */
    public function attach($name, RouteInterface $route);

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
    public function route();
}
