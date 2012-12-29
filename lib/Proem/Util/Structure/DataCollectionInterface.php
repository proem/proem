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
 * @namespace Proem\Util\Structure
 */
namespace Proem\Util\Structure;

/**
 * A generic interface for accessing object data.
 */
interface DataCollectionInterface extends \Iterator, \Serializable, \Countable
{
    /**
     * Set a property or multiple properties at once
     *
     * @param string $index
     * @param mixed $value
     * @return $this
     */
    public function set($index, $value = null);

    /**
     * Retreive a property or a default value
     *
     * @param string $index
     * @param mixed $default
     * @return mixed
     */
    public function get($index, $default);

    /**
     * Retreive all properties
     */
    public function all();

    /**
     * Does this property exist?
     *
     * @param string $index
     * @return bool
     */
    public function has($index);
}
