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

namespace Proem\Tests\Util\Opt\Fixtures;

use Proem\Util\Opt\Options,
    Proem\Util\Opt\Option;

class OptionFixture2
{
    use Options;

    public function __construct(array $options = array())
    {
        $this->options = $this->setOptions([
            'foo'           => (new Option())->required()->unless('bar'),
            'obj'           => (new Option())->classof('Proem\Proem'),
            'emptytest'     => (new Option())->object('Proem\Proem'),
            'custom-arg'    => (new Option())->addTypeValidator('custom', function($value) { return preg_match('/[a-z]/', $value); })->type('custom')
        ], $options);
    }

    public function getFoo()
    {
        return $this->options->foo;
    }

    public function getBar()
    {
        return $this->options->bar;
    }

}
