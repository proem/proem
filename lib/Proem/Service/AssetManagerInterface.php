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

/**
 * Interface that all asset managers must implement.
 */
interface AssetManagerInterface
{
    /**
     * Alias a class to a simpler name or an interface/abstract to an implementation.
     *
     * @param string $type
     * @param string $alias
     * @param bool $force Optionally override existing index.
     */
    public function alias($type, $alias = null, $force = false);

    /**
     * Attach an asset to the service manager.
     *
     * Assets can be provided by a *type* Asset object, a closure providing
     * the asst or an actual instance of an object.
     *
     * Setting the bool $single to true will force any asset provided via a closure
     * to be wrapped within another closure which will cache the results. This makes
     * asset return the same instance on each call. (A singleton).
     *
     * @param string|array $name The name to index the asset by. Also excepts an array so as to alias.
     * @param Proem\Service\Asset|closure|object $type
     * @param bool $single
     */
    public function attach($name, $type = null, $single = false);

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
     */
    public function resolve($name);
}
