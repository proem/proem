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
use Proem\Util\Structure\DataCollectionInterface;

/**
 * Interface that all assets must implement.
 */
interface AssetInterface extends DataCollectionInterface
{
    /**
     * Store the Closure responsible for instantiating an asset.
     *
     * @param string $is The object this asset is a type of
     * @param array|closure $params
     * @param closure|null $closure
     * @return Proem\Service\AssetInterface
     */
    public function __construct($is, $params, $closure = null);

    /**
     * Retrieve the type of object this asset is
     *
     * @return string
     */
    public function is();

    /**
     * Validate and retrieve an instantiated asset.
     *
     * @param array $params Allow last minute setting of parameters.
     * @param Proem\Service\AssetManagerInterface $assetManager
     */
    public function fetch(array $params = [], AssetManagerInterface $assetManager = null);

    /**
     * Store an asset in such a way that when it is retrieved it will always return
     * the same instance.
     *
     * @param closure $closure
     */
    public function single(\Closure $closure);
}
