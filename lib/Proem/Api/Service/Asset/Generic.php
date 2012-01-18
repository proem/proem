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
 * @namespace Proem\Api\Service\Asset
 */
namespace Proem\Api\Service\Asset;

use Proem\Service\Manager;

/**
 * Proem\Api\Service\Asset\Generic
 *
 * An Asset container.
 *
 * Asset containers are reponsible for instantiating Assets. The containers themselves
 * are capable of holding all the parameters that might be required to configure an object
 * as well as having the ability to ijnstantiate an object using these parameters via a
 * Closure.
 */
class Generic
{
    /**
     * Store any required parameters
     *
     * @var array @params
     */
    private $params = [];

    /**
     * The Closure responsible for instantiating the payload.
     *
     * @var closure $asset
     */
    private $asset;

    /**
     * Store a flag indicating what object this Asset provides.
     *
     * @var string $provides
     */
    private $provides;

    /**
     * Get or Set a flag indicating what object this Asset provides.
     *
     */
    public function provides($provides = null)
    {
        if ($provides !== null) {
            $this->provides = $provides;
            return $this;
        }

        return $this->provides;
    }

    /**
     * Set a parameters by named index
     *
     * @param string $index
     * @param mixed $value
     */
    public function setParam($index, $value)
    {
        $this->params[$index] = $value;
        return $this;
    }

    /**
     * A magic method shortcut that proxies setParam()
     *
     * @param string $index
     * @param mixed $value
     */
    public function __set($index, $value) {
        return $this->setParam($index, $value);
    }

    /**
     * Set multiple parameters use a key => value array
     *
     * @param array $params
     */
    public function setParams(Array $params)
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Retrieve a parameter by named index
     *
     * @param string $index
     */
    public function getParam($index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : null;
    }

    /**
     * A magic method shortcut that proxies getParam()
     *
     * @param string $index
     */
    public function __get($index) {
        return $this->getParam($index);
    }

    /**
     * Retrieve all parameters as an array.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Store the Closure reponsible for instantiating an Asset
     *
     * @param Closure $closure
     */
    public function set(\Closure $closure)
    {
        $this->asset = $closure;
        return $this;
    }

    /**
     * Retrieve an instantiated Asset.
     *
     * Here the closure is passed this asset container and optionally the
     * Proem\Api\Asset\Manager.
     *
     * This provides the closure with the ability to use any required parameters
     * and also be able to call upon any other assets stored in the asset manager.
     *
     * @param Proem\Api\Asset\Manager $assetManager
     */
    public function get(Manager $assetManager = null)
    {
        $asset = $this->asset;
        return $asset($this, $assetManager);
    }

    /**
     * Store an asset in such a way that when it is retrieved it will always return
     * the same instance.
     *
     * Here we wrap a Closure within a Closure and store the returned value (an Asset)
     * of the inner Closure within a static variable in the outer Closure. Thus ensuring
     * that whenever this Asset is retrieved it will always return the same instance.
     *
     * Example:
     *
     * <code>
     * $foo = new Asset;
     * $foo->setAsset($foo->single(function() {
     *      return new Foo;
     * }));
     * </code>
     *
     * @param Closure $closure
     */
    public function single(\Closure $closure)
    {
        return function ($assetContainer = null, $assetManager = null) use ($closure) {
            static $obj;
            if (is_null($obj)) {
                $obj = $closure($assetContainer, $assetManager);
            }
            return $obj;
        };
    }

}
