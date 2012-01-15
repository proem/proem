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
 * @namespace Proem\Api\Event
 */
namespace Proem\Api;

use Proem\Util\Options,
    Proem\Util\Options\Option;

/**
 * Proem\Api\Event
 *
 * A base Event implementation
 */
class Event
{
    /**
     * Make use of the Options trait
     */
    use Options;

    /**
     * Store options
     *
     * @var array
     */
    private $options;

    /**
     * Instantiate the Event and setup any options
     *
     * @param Array $options
     * <code>
     *   $this->options = $this->setOptions([
     *       'name'      => (new Option())->required(),     // The name of this Event
     *       'params'    => (new Option())->type('array')   // Additional parameters
     *   ], $options);
     * </code>
     */
    public function __construct(Array $options = []) {
        $this->options = $this->setOptions([
            'params'    => (new Option([]))->type('array')
        ], $options);
    }

    /**
     * Retrieve any parameters passed to this Event
     */
    public function getParams() {
        return $this->options->params;
    }

}
