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
 * @namespace Proem\Util
 */
namespace Proem\Util;

/**
 * A generic data collection trait used by various classes
 * needing to store and provide access to a collection of data.
 */
trait DataCollectionTrait
{
    /**
     * Store the actual data
     *
     * @var array $data
     */
    protected $data = [];

    /**
     * Store the current data index
     *
     * @var int data_index
     */
    protected $data_index = 0;

    /**
     * Set a property
     *
     * @param string $index
     * @param mixed $value
     */
    public function set($index, $value)
    {
        $this->data[$index] = $value;
        return $this;
    }

    /**
     * Retreive a value or an optional default
     *
     * @param string $index
     * @param mixed $default
     */
    public function get($index, $default = null)
    {
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }

        return $default;
    }

    /**
     * Has this collection get a property?
     *
     * @param string $index
     */
    public function has($index)
    {
        return isset($this->data[$index]);
    }

    /**
     * Reset the index
     */
    public function rewind()
    {
        $this->data_index = 0;
        return $this;
    }

    /**
     * Retreieve the current value
     */
    public function current()
    {
        return $this->data[$this->data_index];
    }

    /**
     * Retrieve the current index
     */
    public function key()
    {
        return $this->data_index;
    }

    /**
     * Move cursor forward
     */
    public function next()
    {
        ++$this->data_index;
        return $this;
    }

    /**
     * Is this index valid?
     */
    public function valid()
    {
        return isset($this->data[$this->data_index]);
    }

    /**
     * Serialize our data
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Unserialize our data
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
        return $this;
    }

    /**
     * Return count of items in the queue
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
}
