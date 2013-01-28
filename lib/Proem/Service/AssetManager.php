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
            foreach ($type as $alias => $asset) {
                $this->setParam('aliases', $alias, $asset, $override);
            }
        } else {
            $this->setParam('aliases', $alias, $type, $override);
        }

        return $this;
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
     * @param Proem\Service\Asset|closure|object $resolver Some means of resolving this asset.
     * @param bool $single
     * @param bool $override Optionally override existing (@see override).
     */
    public function attach($name, $resolver = null, $single = false, $override = false)
    {
        if (is_array($name)) {
            $resolver  = current($name);
            $name  = key($name);
            $this->alias($resolver, $name, $override);
        }

        if ($name instanceof Asset) {
            $this->setParam('assets', $name->is(), $name, $override);
        } elseif ($resolver instanceof \Closure && $single) {
            $this->setParam('assets', $name, function($params) use ($resolver) {
                static $obj;

                if ($obj === null) {
                    $obj = $resolver($params, $this);
                }
                return $obj;
            }, $override);

        } elseif ($resolver instanceof \Closure || $resolver instanceof Asset) {
            $this->setParam('assets', $name, $resolver, $override);

        } elseif (is_object($resolver)) {
            $this->setParam('instances', $name, $resolver, $override);

        } elseif (($resolver === null) && $single) {
            $this->setParam('instances', $name, $name, $override);

        } elseif ($resolver === null) {
            $this->setParam('assets', $name, $name, $override);
        }

        return $this;
    }

    /**
     * A convenience method for adding a singleton.
     *
     * @param string name
     * @param Proem\Service\Asset|closure|object $resolver Some means of resolving this asset.
     * @param bool
     */
    public function singleton($name, $resolver = null, $override = false)
    {
        return $this->attach($name, $resolver, true, $override);
    }

    /**
     * Attach an asset to the service manager overriding any existing of the same index.
     *
     * @param string|array $name The name to index the asset by. Also excepts an array so as to alias.
     * @param Proem\Service\Asset|closure|object $resolver Some means of resolving this asset.
     * @param bool $single
     */
    public function override($name, $resolver = null, $single = false) {
        return $this->attach($name, $resolver, $single, true);
    }

    /**
     * A convenience method for overriding an existing singleton.
     *
     * @param string name
     * @param Proem\Service\Asset|closure|object $resolver Some means of resolving this asset.
     * @param bool
     */
    public function overrideAsSingleton($name, $resolver = null)
    {
        return $this->singleton($name, $resolver, true, true);
    }

    /**
     * Return an asset by index.
     *
     * First, if we have a closure as a second argument, use it to resolve our asset (or at least return
     * whatever it returns).
     *
     * Otherwise if we have an asset stored under  this index, return it. Failing that, check for
     * an instance at this index and return that. Failing that, resolve any aliases recursively.
     *
     * If all of the above fails, we start the auto resolve process. This attempts to resolve to
     * instantiate the requested object and any dependencies that it may require to do so.
     *
     * @param string $name
     * @param array $params Any extra paremeters to pass along to a closure|Asset|object
     */
    public function resolve($name, $params = [])
    {
        // Allow hot resolving. eg; pass a closure to the resolve() method.
        if (isset($params['resolver']) && $params['resolver'] instanceof \Closure) {
            return $params['resolver']();
        }

        // Allow custom reflection resolutions.
        if (isset($params['reflector']) && $params['reflector'] instanceof \Closure) {
            $reflection = new \ReflectionClass($name);
            if ($reflection->isInstantiable()) {
                return $params['reflector']($reflection, $name);
            }
        }

        if (isset($this->assets[$name])) {
            if (is_callable($this->assets[$name])) {
                return $this->assets[$name]($params, $this);
            }
        }

        if (isset($this->instances[$name])) {
            if (is_object($this->instances[$name])) {
                return $this->instances[$name];
            } else {
                $object = $this->autoResolve($name, $params);
                $this->setParam('instances', $name, $object, true);
            }
        }

        // Recurse back through resolve().
        // This allows complex alias mappings.
        if (isset($this->aliases[$name])) {
            return $this->resolve($this->aliases[$name]);
        }

        return $this->autoResolve($name, $params);
    }

    /**
     * A simple helper to resolve dependencies given an array of dependents.
     *
     * @param array $dependencies
     */
    public function getDependencies($params)
    {
        $deps = [];
        foreach ($params as $param) {
            $dependency = $param->getClass();
            if ($dependency !== null) {
                if ($dependency->name == 'AssetManager' || $dependency->name == 'AssetManagerInterface') {
                    $deps[] = $this;
                } else {
                    $deps[] = $this->resolve($dependency->name);
                }
            } else {
                if ($param->isDefaultValueAvailable()) {
                    $deps[] = $param->getDefaultValue();
                }
            }
        }
        return $deps;
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

        return $this;
    }

    /**
     * Method to do the heavy lifting in relation to reolution of assets.
     *
     * In most cases this method returns an asset (object) or null. It can however be
     * coherced into returning the results of a call to a method on the asst (object)
     * itself by passing a method name along within the $params array indexed by "invoke".
     *
     * @param string $name
     * @param array @params Any extra parameters.
     */
    protected function autoResolve($name, $params)
    {
        $reflection = new \ReflectionClass($name);
        if ($reflection->isInstantiable()) {
            $construct = $reflection->getConstructor();
            if ($construct === null) {
                $object = new $name;
            } else {
                $dependencies = $this->getDependencies($construct->getParameters());
                $object = $reflection->newInstanceArgs($dependencies);
            }

            try {
                $method = $reflection->getMethod('setParams');
                $method->invokeArgs($object, $params);
            } catch (\ReflectionException $e) {}

            // Allow a list of methods to be executed.
            if (isset($params['methods'])) {
                foreach ($params['methods'] as $method) {
                    $method = $reflection->getMethod($method);
                    $method->invokeArgs($object, $this->getDependencies($method->getParameters()));
                }
            }

            // If this single method is invoked, its results will be returned.
            if (isset($params['invoke'])) {
                $method = $params['invoke'];
                $method = $reflection->getMethod($method);
                return $method->invokeArgs($object, $this->getDependencies($method->getParameters()));
            }

            return $object;
        }
    }
}
