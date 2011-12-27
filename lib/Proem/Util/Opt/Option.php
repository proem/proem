<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2011 Tony R Quilkey <trq@proemframework.org>
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
 * @namespace Proem\Util\Opt
 */
namespace Proem\Util\Opt;

/**
 * Proem\Util\Opt\Option
 */
trait Option
{
    public function setOptions($defaults, $options)
    {
        $testType = function ($type, $value) {
            if ($type == 'array') {
                return is_array($value);
            }

            if ($type == 'bool') {
                return is_bool($value);
            }

            if ($type == 'float') {
                return is_float($value);
            }

            if ($type == 'int') {
                return is_int($value);
            }

            if ($type == 'callable') {
                return is_callable($value);
            }

            return is_a($value, $type);
        };

        $out = [];

        // Setup default params
        foreach ($defaults as $default => $data) {
            if (isset($data['required'])) {
                if (isset($options[$default])) {
                    if (isset($data['type'])) {
                        if (!$testType($data['type'], $options[$default])) {
                            throw new \InvalidArgumentException("{$default} is required to be of type {$data['type']}");
                        } else {
                            $out[$default] = $options[$default];
                        }
                    }
                } else {
                    throw new \InvalidArgumentException("$default is a required option");
                }
            } elseif (isset($data['type'])) {
                if (isset($options[$default])) {
                    if (!$testType($data['type'], $options[$default])) {
                        throw new \InvalidArgumentException("{$default} is required to be of type {$data['type']}");
                    } else {
                        $out[$default] = $options[$default];
                    }
                } else {
                    throw new \InvalidArgumentException("{$default} is required to be of type {$data['type']}");
                }
            } elseif (isset($data['value'])) {
                $out[$default] = $data['value'];
            }
        }

        // Setup any extra params
        foreach ($options as $option => $value) {
            if (!isset($out[$option])) {
                $out[$option] = $value;
            }
        }
        return (object) $out;
    }
}
