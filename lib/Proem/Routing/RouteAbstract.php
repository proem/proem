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

use Proem\Routing\RouteInterface;
use Proem\Http\Request;

/**
 * The Route interface that all routes must implement.
 */
abstract class RouteAbstract implements RouteInterface
{
    /**
     * Store this routes rule
     *
     * @var string $rule
     */
    protected $rule;

    /**
     * Store options
     *
     * @var array $options
     */
    protected $options = [];

    /**
     * Store this routes callback
     *
     * @var callable
     */
    protected $callback = null;

    /**
     * Instantiate this route
     *
     * $options = ['targets', 'filters', 'method'];
     *
     * @param string $rule
     * @param array|callable $options
     * @param callable $callback
     */
    public function __construct($rule, $options = null, $callback = null)
    {
        $this->rule     = $rule;

        if (is_array($options)) {
            $this->options = $options;
        }

        if ($callback instanceof \Closure) {
            $this->callback = $callback;
        }

        if ($options instanceof \Closure) {
            $this->callback = $options;
        }
    }

    /**
     * Retreive route rule.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Retreive route options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options.
     *
     * This will merge the options provided with this
     * this routes existing options taking priority.
     */
    public function setOptions(array $options = [])
    {
        $this->options = array_merge($options, $this->options);
    }

    /**
     * Do we have a callback?
     *
     * @return bool
     */
    public function hasCallback()
    {
        return $this->callback !== null;
    }

    /**
     * Retreive callback.
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Method to actually test for a match.
     *
     * @param Proem\Http\Request $request
     * @return bool
     */
    abstract public function process(Request $request);
}
