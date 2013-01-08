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
 * A **VERY** simple asset resolver.
 *
 * Designed to attempt to build an asset from scratch.
 *
 * Presently there is no error handling. If you pass the
 * wrong config into a construct, stuff will blow up in
 * your face.
 */
class AssetResolver implements AssetResolverInterface
{
    /**
     * Store any configuration options.
     *
     * @var array
     */
    protected $config;

    /**
     * Setup
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Attempt asset resolution from the complete object type and
     * any arguments requiring injection via either the construct
     * other method.
     *
     * @param string $object The complete (namespaced) object type
     * @param array $args Any arguments that need to be passed to the asset.
     * @see \Proem\Service\AssetComposer
     */
    public function resolve($object, array $args = [])
    {
        $object = str_replace(['Interface', 'Abstract'], '', $object);

        // Interface and Abstract configs should be merged first.
        foreach (['Interface', 'Abstract'] as $type) {
            if (isset($this->config[$object . $type])) {
                $args = array_merge_recursive($this->config[$object . $type], $args);
            }
        }

        // More specific object configs.
        if (isset($this->config[$object])) {
            $args = array_merge_recursive($this->config[$object], $args);
        }

        // Resolve any @ and # dependcies.
        $args = $this->resolveDependencies($args);

        $object   = isset($args['class']) ? $args['class'] : $object;
        $composer = new AssetComposer($object);

        // Prepare to compose.
        foreach (['construct', 'methods'] as $type) {
            if (isset($args[$type])) {
                $composer->$type($args[$type]);
            }
        }
        $single = isset($args['single']) ? $args['single'] : false;

        return $composer->compose($single);
    }

    /**
     * Within the *construct* and *methods* options the special symbols # and @
     * appearing at the start of the value represent a dependant asset or object
     * dependency which should also be resolved.
     *
     * This method attempts to do so.
     *
     * @param array $args Any arguments that need to be passed to the asset.
     * @see \Proem\Service\AssetComposer
     */
    protected function resolveDependencies($args)
    {
        foreach (['construct', 'methods'] as $type) {
            if (isset($args[$type])) {
                foreach ($args[$type] as $key => $value) {
                    if ($value{0} == '#') {
                        // # Asset
                        $args[$type][$key] = $this->resolve(substr($value, 1));
                    }

                    if ($value{0} == '@') {
                        // @ Object
                        $args[$type][$key] = $this->resolve(substr($value, 1))->fetch();
                    }
                }
            }
        }

        return $args;
    }
}
