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

use Proem\Service\Asset;

/**
 * A collection of assets.
 *
 * Within the manager itself assets are stored in a hash of key value pairs where each
 * value is an asset container of some sort. These containers can be simple closures, or
 * objects of type \Proem\Service\Asset.a
 *
 * These containers are capable of returning an instantiated object of the type requested.
 */
class AssetManager implements AssetManagerInterface
{
    /**
     * Store our assets.
     *
     * @var array
     */
    protected $assets = [];

    /**
     * Store any instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Store alias mappings.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Alias a class to a simpler name or an interface/abstract to an implementation.
     *
     * @param string $type
     * @param string $alias
     * @param bool $override Optionally override existing index.
     */
    public function alias($type, $alias = null, $override = false)
    {
        if (is_array($type)) {
            foreach ($type as $asset => $alias) {
                $this->setParam('aliases', $alias, $asset, $override);
            }
        } else {
            $this->setParam('aliases', $alias, $type, $override);
        }
    }

    /**
     * Attach an asset to the service manager if it doesn't already exist.
     *
     * Assets can be provided by a *type* Asset object, a closure providing
     * the asst or an actual instance of an object.
     *
     * Setting the bool $single to true will override any asset provided via a closure
     * to be wrapped within another closure which will cache the results. This makes
     * asset return the same instance on each call. (A singleton).
     *
     * @param string|array $name The name to index the asset by. Also excepts an array so as to alias.
     * @param Proem\Service\Asset|closure|object $type
     * @param bool $single
     * @param bool $override Optionally override existing (@see override).
     */
    public function attach($name, $type = null, $single = false, $override = false)
    {
        if (is_array($name)) {
            $type  = current($name);
            $name  = key($name);
            $this->alias($type, $name, $override);
        }

        if ($name instanceof Asset) {
            $this->setParam('assets', $name->is(), $name, $override);
        } elseif ($type instanceof \Closure && $single) {
            $this->setParam('assets', $name, function($params) use ($type) {
                static $obj;

                if ($obj === null) {
                    $obj = $type($params, $this);
                }
                return $obj;
            }, $override);

        } elseif ($type instanceof \Closure || $type instanceof Asset) {
            $this->setParam('assets', $name, $type, $override);

        } elseif (is_object($type)) {
            $this->setParam('instances', $name, $type, $override);
        }
    }

    /**
     * Attach an asset to the service manager overriding any existing of the same index.
     *
     * @param string|array $name The name to index the asset by. Also excepts an array so as to alias.
     * @param Proem\Service\Asset|closure|object $type
     * @param bool $single
     */
    public function override($name, $type = null, $single = false) {
        $this->attach($name, $type, $single, true);
    }

    /**
     * Return an asset by index.
     *
     * First map any alias, then check to see if we already have an instance of this
     * type cached, if so, return it. If not, check to see if we have any assets indexed
     * by this name, if so, execute it's closure and return the results.
     *
     * If the above fails, we start the auto resolve process. This attempts to resolve to
     * instantiate the requested object and any dependencies that it may require to do so.
     *
     * @param string $name
     * @param array $params Any extra paremeters to pass along to a closure|Asset|object
     */
    public function resolve($name, $params = [])
    {
        if (isset($this->assets[$name])) {
            return $this->assets[$name]($params, $this);
        }

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        if (isset($this->assets[$name])) {
            return $this->assets[$name]($params, $this);
        }

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if ($params instanceof \Closure) {
            return $params();
        }

        $reflection = new \ReflectionClass($name);
        if ($reflection->isInstantiable()) {
            $construct = $reflection->getConstructor();
            if ($construct === null) {
                $object = new $name;
            } else {
                $dependencies = $this->getDependencies($construct->getParameters());
                $object = $reflection->newInstanceArgs($dependencies);
            }

            if (method_exists($object, 'setParams')) {
                $object->setParams($params);
            }

            return $object;
        }
    }

    /**
     * A helper used to set aliases, assets and instances.
     *
     * Not pretty! But hey?
     *
     * @param string $type
     * @param string $index
     * @param mixed $value
     * @param bool override override
     */
    protected function setParam($type, $index, $value, $override = false) {
        if ($override) {
            $this->{$type}[$index] = $value;
        } else if (!isset($this->{$type}[$index])) {
            $this->{$type}[$index] = $value;
        }
    }

    /**
     * A simple helper to resolve an assets dependencies.
     */
    protected function getDependencies($params)
    {
        $deps = [];
        foreach ($params as $param) {
            $dependency = $param->getClass();
            if ($dependency !== null) {
                $deps[] = $this->resolve($dependency->name);
            }
        }
        return $deps;
    }
}
