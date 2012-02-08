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
 * @namespace Proem\Api\Util\Storage
 */
namespace Proem\Api\Util\Storage;

/**
 * Proem\Api\Util\Storage\KeyValStore
 *
 * A generic key => value storage mechanism.
 */
class KeyValStore
{
    /**
     * Store the data
     *
     * @param array $data
     */
    protected $data = [];

    /**
     * Instantiate
     *
     * @param array $data
     */
    public function __construct(Array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Retrieve all data
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Retrieve a value by index
     *
     * Optionaly returns a default value.
     *
     * @param string $index
     * @param mixed $default
     */
    public function get($index, $default = null)
    {
        return $this->has($index) ? $this->data[$index] : $default;
    }

    /*
     * Magic proxy to get()
     *
     * @param string $index
     */
    public function __get($index)
    {
        return $this->get($index);
    }

    /**
     * Set a value by index
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
     * Magic proxy to set()
     *
     * @param string $index
     * @param mixed $index
     */
    public function __set($index, $value)
    {
        return $this->set($index, $value);
    }

    /**
     * Remove an item by index
     *
     * @param string $index
     */
    public function remove($index)
    {
        if (isset($this->data[$index])) {
            unset($this->data[$index]);
        }
    }

    /**
     * Has storage have index?
     *
     * @param string $index
     */
    public function has($index)
    {
        return array_key_exists($index, $this->data);
    }
}
