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
    protected $data = [];
    protected $data_index = 0;

    public function set($index, $value)
    {
        $this->data[$index] = $value;
        return $this;
    }

    public function get($index, $default = null)
    {
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }

        return $default;
    }

    public function has($index)
    {
        return isset($this->data[$index]);
    }

    public function rewind()
    {
        $this->data_index = 0;
        return $this;
    }

    public function current()
    {
        return $this->data[$this->data_index];
    }

    public function key()
    {
        return $this->data_index;
    }

    public function next()
    {
        ++$this->data_index;
        return $this;
    }

    public function valid()
    {
        return isset($this->data[$this->data_index]);
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($data);
        return $this;
    }
}
