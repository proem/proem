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
 * @namespace Proem\Routing\Route
 */
namespace Proem\Routing\Route;

use Proem\Util\Storage\KeyValStore;

/**
 * The route payload.
 */
class Payload extends KeyValStore
{
    /**
     * A flag to keep note as to wether or not this Payload is populated
     *
     * @var bool
     */
    protected $populated = false;

    /**
     * Is the Payload Populated?
     *
     * @return bool
     */
    public function isPopulated()
    {
        return $this->populated;
    }

    /**
     * Set the populated flag
     *
     * @return Proem\Routing\Route\Payload
     */
    public function setPopulated()
    {
        $this->populated = true;
        return $this;
    }

    /**
     * Prepare this Payload for injection into the Request object.
     *
     * Merges the params array into indivual properties if they don't already exist.
     * Removes the Request object which is only needed by Routes that are dispatching a callback.
     *
     * @return Proem\Routing\Route\Payload
     */
    public function prepare()
    {
        if ($this->has('params')) {
            foreach ($this->get('params') as $key => $value) {
                if (!$this->has($key)) {
                    $this->set($key, $value);
                }
            }
            $this->remove('params');
        }

        $this->remove('request');

        return $this;
    }
}
