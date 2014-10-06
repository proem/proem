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
 * @namespace Proem\Routing
 */
namespace Proem\Routing;

use Proem\Http\Request;
use Proem\Http\Response;

/**
 * The Route interface that all routes must implement.
 */
interface RouteInterface
{
    /**
     * Instantiate this route
     *
     * $options = ['targets', 'filters', 'method', 'callback'];
     *
     * @param string $rule
     * @param array|callable $options
     * @param callable $callback
     */
    public function __construct($rule, $options = null, $callback = null);

    /**
     * Retreive route options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Do we have a callback?
     *
     * @return bool
     */
    public function hasCallback();

    /**
     * Retreive callback.
     */
    public function getCallback();

    /**
     * Method to actually test for a match.
     *
     * @param Proem\Http\Request $request
     * @return bool
     */
    public function process(Request $request);
}
