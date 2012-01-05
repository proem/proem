<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2011 Tony R Quilkey <trq@proemframework.org>
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
 * @namespace Proem\Api\Util
 */
namespace Proem\Api\Util;

/**
 * Proem\Api\Util\Callback
 *
 * A wrapper around call_user_func_array
 */
class Callback
{
    public $callback;
    public $params = array();

    /**
     * Instantiate the Callback object
     *
     * @param callable $callback A valid callback
     * @param Mixed $params Params passed to the callback
     */
    public function __construct(callable $callback, $params = array())
    {
        $this->callback = $callback;
        $this->params   = is_array($params) ? $params : [$params];
    }

    /**
     * Execute the callback and return it's results.
     */
    public function call()
    {
        return call_user_func_array($this->callback, $this->params);
    }
}
