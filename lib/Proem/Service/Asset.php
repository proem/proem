<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2013 Tony R Quilkey <trq@proemframework.org>
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

use Proem\Service\AssetInterface;
use Proem\Service\AssetManagerInterface;
use Proem\Util\Structure\DataCollectionTrait;

/**
 * Standard asset container.
 *
 * Asset containers are reponsible for instantiating assets. The containers themselves
 * are capable of holding all the parameters that might be required to configure an object
 * as well as having the ability to instantiate an object using these parameters via a
 * closure.
 */
class Asset implements AssetInterface
{
    /**
     * Use the generic DataCollectionTrait trait
     *
     * This provides implementations for the DataAccessInterface, Iterator and Serializable
     */
    use DataCollectionTrait;

    /**
     * The Closure responsible for instantiating the payload.
     *
     * @var closure $asset
     */
    protected $asset = null;

    /**
     * Store a flag indicating the object this asset is a type of.
     *
     * @var string $is
     */
    protected $is = null;

    /**
     * Validate that this object is what it advertises.
     *
     * @param object
     */
    protected function validate($object)
    {
        $object = (object) $object;

        if ($object instanceof $this->is) {
            return $object;
        }

        throw new \DomainException(
            sprintf(
                "The Asset advertised as being of type %s is actually of type %s",
                $this->is,
                get_class($object)
            )
        );
    }

    /**
     * Store the Closure responsible for instantiating an asset.
     *
     * @param string $is The object this asset is a type of
     * @param array|closure|null $data
     * @param closure|null $closure
     * @return Proem\Service\AssetInterface
     */
    public function __construct($is, $data = null, $closure = null)
    {
        $this->is = $is;

        if (is_array($data) && $closure instanceof \Closure) {
            $this->asset  = $closure;
            $this->data = $data;
        } elseif ($data instanceof \Closure) {
            $this->asset  = $data;
            $this->data = [];
        } elseif (is_array($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Retrieve the type of object this asset is, or test it's type
     *
     * @return string
     */
    public function is($type = null)
    {
        if ($type === null) {
            return $this->is;
        } else {
            return $type == $this->is;
        }
    }

    /**
     * Validate and retrieve an instantiated asset.
     *
     * Here the closure is passed this asset container and optionally a
     * Proem\Service\AssetManagerInterface implementation.
     *
     * This provides the closure with the ability to use any required parameters
     * and also be able to call upon any other assets stored in the service manager.
     *
     * @param array $params Allow last minute setting of parameters.
     * @param Proem\Service\AssetManagerInterface $assetManager
     */
    public function fetch(array $params = [], AssetManagerInterface $assetManager = null)
    {
        $asset = $this->asset;

        $this->set($params);

        return $this->validate($asset($this, $assetManager));
    }

    /**
     * Store an asset in such a way that when it is retrieved it will always return
     * the same instance.
     *
     * Here we wrap a closure within a closure and store the returned value (an asset)
     * of the inner closure within a static variable in the outer closure. This insures
     * that whenever this Asset is retrieved it will always return the same instance.
     *
     * <code>
     * $foo = new Asset(
     *     'Foo',
     *     Asset::single(function() {
     *         return new Foo;
     *     })
     * );
     * </code>
     *
     * @param closure $closure
     */
    public function single(\Closure $closure)
    {
        if ($this->asset === null) {
            $this->asset = function ($asset = null, $assetManager = null) use ($closure) {
                static $obj;
                if ($obj === null) {
                    $obj = $this->validate($closure($asset, $assetManager));
                }
                return $obj;
            };
        }
        return $this;
    }
}
