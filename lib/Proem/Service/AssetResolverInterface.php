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

/**
 * A **VERY** simple asset resolver.
 *
 * Designed to attempt to build an asset from scratch.
 *
 * Presently there is no error handling. If you pass the
 * wrong config into a construct, stuff will blow up in
 * your face.
 */
interface AssetResolverInterface
{
    /**
     * Setup
     *
     * @param array $config
     */
    public function __construct(array $config = []);

    /**
     * Attempt asset resolution from the complete object type and
     * any arguments requiring injection via either the construct
     * other method.
     *
     * @param string $object The complete (namespaced) object type
     * @param array $constructArgs Any arguments that need to be passed as the construct arg.
     * @param array $methodArgs Any arguments that need to be passed as the methods arg.
     * @see \Proem\Service\AssetComposer
     */
    public function resolve($object, $constructArgs = null, $methodArgs = null);
}
