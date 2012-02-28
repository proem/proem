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

namespace Proem\Tests\Util\Options\Fixtures;

use Proem\Util\Opt\Options,
    Proem\Util\Opt\Option;

class OptionsFixture
{
    use Options;

    public function __construct(array $options = [])
    {
        $this->options = $this->setOptions([
            'foo'   => (new Option('foo')),
            'asset' => (new Option())->asset('StdClass'),
            'bar'   => (new Option())->required(),
            'boo'   => (new Option())->required()->type('array'),
            'bob'   => (new Option())->required()->object('Proem\Proem'),
        ], $options);
    }

    public function getSomething()
    {
        return $this->options->something;
    }

    public function getFoo()
    {
        return $this->options->foo;
    }

    public function getBar()
    {
        return $this->options->bar;
    }

    public function getBoo()
    {
        return $this->options->boo;
    }

    public function getBob()
    {
        return $this->options->bob;
    }
}
