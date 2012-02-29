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
 * @namespace Proem\Api\Service
 */
namespace Proem\Api\Service;

use Proem\Service\Asset\Generic as Asset;

/**
 * Proem\Api\Service\Manager
 *
 * A Registry of Assets.
 *
 * Within the manager itself Assets are stored in a hash of key values where each
 * value is an Asset container.
 *
 * These containers contain the parameters required to instantiate an Asset as
 * well as a Closure capable of returning a configured and instantiated Asset.
 *
 * @see Proem\Api\Service\Asset\Generic
 */
class Manager
{
    /**
     * Store assets
     *
     * @var $assets array
     */
    private $assets = [];

    /**
     * Store an array containing information about what
     * Assets this manager provides
     */
    private $provides = [];

    /**
     * Store an Asset container by named index.
     *
     * @param string $index The index the asset will be referenced by.
     * @param Proem\Api\Asset $asset
     */
    public function set($index, Asset $asset)
    {
        $this->assets[$index]   = $asset;
        $this->provides[]       = $asset->provides();
        return $this;
    }

    /**
     * Retrieve an Asset container by named index.
     *
     * @param string $index The index the asset is referenced by.
     */
    public function getContainer($index)
    {
        return isset($this->assets[$index]) ? $this->assets[$index] : null;
    }

    /**
     * Retrieve an actual instantiated Asset object from within it's container.
     *
     * @param string $index The index the asset is referenced by.
     */
    public function get($index)
    {
        return isset($this->assets[$index]) ? $this->assets[$index]->get($this) : null;
    }

    /**
     * Check to see if this manager has a specific asset
     *
     * @param string $index The index the asset is referenced by.
     */
    public function has($index)
    {
        return isset($this->assets[$index]);
    }

    /**
     * Retrieve a "provides" flag
     *
     * @param string $provides
     */
    public function provides($index, $provides = null)
    {
        if (is_array($index)) {
            foreach ($index as $key) {
                if (!in_array($key, $this->provides)) {
                    return false;
                }
            }
            return true;
        } elseif ($provides === null) {
            return in_array($index, $this->provides);
        } else {
            if ($this->has($index)) {
                if ($this->assets[$index]->provides() == $provides) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Retrieve an Asset by what it provides
     *
     * @param string $provides
     */
    public function getProvided($provides)
    {
        foreach ($this->assets as $asset) {
            if ($asset->provides() == $provides) {
                return $asset->get($this);
            }
        }
    }

}
