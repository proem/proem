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
 * @namespace Proem\Service
 */
namespace Proem\Service;

use Proem\Service\AssetComposerInterface;
use Proem\Service\Asset;

/**
 * A **VERY** simple asset composer.
 *
 * This composer is capable of building assets from an array of
 * arguments. It also contains methods for editing the same.
 */
class AssetComposer implements AssetComposerInterface
{
    /**
     * The class this asset is to provide.
     *
     * @var string
     */
    protected $class;

    /**
     * An Array of arguments to pass to the construct
     * of the object being created.
     *
     * @var array
     */
    protected $constructArgs = [];

    /**
     * An array of arguments to pass to methods on the
     * object being created.
     *
     * This array takes the name of the method as an index,
     * followed by an array of arguments.
     *
     * @var array
     */
    protected $methodArgs = [];

    /**
     * Setup
     *
     * <code>
     * $foo = (new AssetComposer([
     *     'class'     => 'Foo',
     *     'construct' => ['arg1'],
     *     'methods'   => [
     *         'setArg2' => ['arg2']
     *     ]
     * ]))->compose();
     * </code>
     *
     * <code>
     * $foo = (new AssetComposer('Foo'))
     *     ->construct(['arg1'])
     *     ->methods(['setArg2' => ['arg2']])
     *     ->compose();
     * </code>
     *
     * @param string|array $class Either the name of the class to create, or an array of arguments.
     */
    public function __construct($class)
    {
        if (is_array($class)) {
            if (isset($class['class'])) {
                $this->class = $class['class'];
            }

            if (isset($class['construct'])) {
                $this->constructArgs = $class['construct'];
            }

            if (isset($class['methods'])) {
                $this->methodArgs = $class['methods'];
            }
        } else {
            $this->class = $class;
        }
    }

    /**
     * Set an array of arguments to pass to the object's
     * __construct method.
     *
     * @param array
     */
    public function construct($constructArgs)
    {
        $this->constructArgs = $constructArgs;
        return $this;
    }

    /**
     * Set an array of arguments to pass to different methods on the
     * objected being constructed.
     *
     * @param array
     */
    public function methods($methodArgs)
    {
        $this->methodArgs = $methodArgs;
        return $this;
    }

    /**
     * Build a configured Asset and return it.
     *
     * This Asset can optionally be returned implementing a singleton.
     *
     * @param bool $single
     * @return Proem\Service\AssetInterface
     */
    public function compose($single = false)
    {
        $reflection = new \ReflectionClass($this->class);

        $constructArgs  = $this->constructArgs;
        $methodArgs     = $this->methodArgs;

        if ($single) {
            static $obj;
            if ($obj == null) {
                $obj = (new Asset($this->class))->single(
                    function () use ($reflection, $constructArgs, $methodArgs) {
                        if ($constructArgs) {
                            $object = $reflection->newInstanceArgs($constructArgs);
                        } else {
                            $object = $reflection->newInstance();
                        }

                        foreach ($methodArgs as $method => $params) {
                            if ($reflection->hasMethod($method)) {
                                call_user_func_array([$object, $method], $params);
                            }
                        }
                        return $object;
                    }
                );
            }
            return $obj;
        } else {
            return new Asset(
                $this->class,
                function () use ($reflection, $constructArgs, $methodArgs) {
                    if ($constructArgs) {
                        $object = $reflection->newInstanceArgs($constructArgs);
                    } else {
                        $object = $reflection->newInstance();
                    }

                    foreach ($methodArgs as $method => $params) {
                        if ($reflection->hasMethod($method)) {
                            call_user_func_array([$object, $method], $params);
                        }
                    }
                    return $object;
                }
            );
        }
    }
}
