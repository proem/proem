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
 * @namespace Proem\Service
 */
namespace Proem\Service;

/**
 * A **VERY** simple asset composer interface.
 */
interface AssetComposerInterface
{
    /**
     * Setup
     *
     * @param string|array $class Either the name of the class to create, or an array of arguments.
     */
    public function __construct($class);

    /**
     * Set an array of arguments to pass to the object's
     * __construct method.
     *
     * @param array
     */
    public function construct($constructArgs);

    /**
     * Set an array of arguments to pass to different methods on the
     * objected being constructed.
     *
     * @param array
     */
    public function methods($methodArgs);

    /**
     * Build a configured Asset and return it.
     *
     * This Asset can optionally be returned implementing a singleton.
     *
     * @param bool $single
     * @return Proem\Service\AssetInterface
     */
    public function compose($single = false);
}
