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
class Option
{
    private $value;
    private $is_required        = false;
    private $is_type;
    private $unless             = [];
    private $type_validators    = [];

    public function __construct($value = null) {
        $this->value = $value;

        $this
            ->addValidator('array',     function($value) { return is_array($value); })
            ->addValidator('bool',      function($value) { return is_bool($value); })
            ->addValidator('float',     function($value) { return is_float($value); })
            ->addValidator('int',       function($value) { return is_int($value); })
            ->addValidator('callable',  function($value) { return is_callable($value); })
            ->addValidator('object',    function($value) { return is_object($value); });
    }

    public function addValidator($type, $callback, $override = false)
    {
        if (!isset($this->type_validators[$type]) || $override) {
            $this->type_validators[$type] = $callback;
        }
        return $this;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function required() {
        $this->is_required = true;
        return $this;
    }

    public function unless($options)
    {
        if (is_array($options)) {
            $this->unless = $options;
        } else {
            $this->unless[] = $options;
        }
    }

    public function type($type)
    {
        $this->is_type = $type;
        return $this;
    }

    public function validate($options) {
        if ($this->unless) {
            $keys = array_keys($options);
            if (!count(array_diff($this->unless, array_keys($options)))) {
                $this->is_required = false;
            }
        }

        if ($this->is_required) {
            if (!isset($this->value)) {
                throw new \InvalidArgumentException(' is required');
            }
        }

        if ($this->is_type) {
            if (isset($this->type_validators[$this->is_type])) {
                $func = $this->type_validators[$this->is_type];
                if (!$func($this->value)) {
                    throw new \InvalidArgumentException(' is required to be of type ' . $this->is_type);
                }
            }
        }
        return true;
    }
}
