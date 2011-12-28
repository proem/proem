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

namespace Proem\Tests;

use Proem\Tests\Util\Opt\Fixtures\OptionFixture,
    Proem\Proem;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testValidOptions()
    {
        $fixture = new OptionFixture([
            'something' => 'something',
            'bar'       => 'this is bar',
            'boo'       => ['key' => 'value'],
            'bob'       => new Proem
        ]);
        $this->assertInstanceOf('Proem\Tests\Util\Opt\Fixtures\OptionFixture', $fixture);
        $this->assertEquals($fixture->getSomething(), 'something');
        $this->assertEquals($fixture->getFoo(), 'foo');
        $this->assertEquals($fixture->getBar(), 'this is bar');
        $this->assertArrayHasKey('key', $fixture->getBoo());
        $this->assertInstanceOf('Proem\Proem', $fixture->getBob());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBar()
    {
        $fixture = new OptionFixture([
            'boo' => [],
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBoo()
    {
        $fixture = new OptionFixture([
            'bar' => 'this is bar',
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBob()
    {
        $fixture = new OptionFixture([
            'boo' => [],
            'bar' => 'this is bar'
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidBoo()
    {
        $fixture = new OptionFixture([
            'boo' => false,
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidBob()
    {
        $fixture = new OptionFixture([
            'boo' => [],
            'bob' => new \StdClass
        ]);
    }
}
