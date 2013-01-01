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

namespace Proem\Signal\Tests;

use Proem\Signal\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateEvent()
    {
        $e = new Event('Foo');
        $this->assertInstanceOf('Proem\Signal\EventInterface', $e);
    }

    public function testCanRetreiveName()
    {
        $e = new Event('Foo');
        $this->assertEquals('Foo', $e->getName());
    }

    public function testCanSetAndRetreiveData()
    {
        $e = new Event('Foo', ['x' => 'y']);
        $this->assertEquals('y', $e->get('x'));
    }

    public function testCanHaltQueue()
    {
        $e = new Event('Foo');
        $e->haltQueue();
        $this->assertTrue($e->isQueueHalted());
    }

    public function testCanHaltQueueEarly()
    {
        $e = new Event('Foo');
        $e->haltQueue();
        $this->assertTrue($e->isQueueHalted(true));
    }
}
