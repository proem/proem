<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2014 Tony R Quilkey <trq@proemframework.org>
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

namespace Proem\Util\Structure\Tests;

use Proem\Util\Loader\Autoloader;

require_once __DIR__ . '/DataCollectionFixture.php';
use \DataCollectionFixture as Fixture;

class DataCollectionTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateFixture()
    {
        $f = new Fixture;
        $this->assertInstanceOf('Proem\Util\Structure\DataCollectionInterface', $f);
    }

    public function testCanGetSetSingleValue()
    {
        $f = new Fixture;
        $f->set('foo', 'bar');
        $this->assertEquals('bar', $f->get('foo'));
    }

    public function testCanGetSetMultipleValue()
    {
        $f = new Fixture;
        $f->set(['foo' => 'bar', 'y' => 'x']);
        $this->assertEquals('bar', $f->get('foo'));
        $this->assertEquals('x', $f->get('y'));
    }

    public function testHasValue()
    {
        $f = new Fixture;
        $f->set('foo', 'bar');
        $this->assertTrue($f->has('foo'));
        $this->assertFalse($f->has('bar'));
    }

    public function testSetMergeReset()
    {
        $f = new Fixture;
        $f->set('foo', 'bar');
        $this->assertEquals('bar', $f->get('foo'));
        $f->set(['foo' => 'x']);
        $this->assertEquals('x', $f->get('foo'));
        $f->set('foo', 'bar');
        $this->assertEquals('bar', $f->get('foo'));
    }

    public function testIteration()
    {
        $f = new Fixture;
        $f->set(['a' => 'a', 'b' => 'b', 'c' => 'c']);
        foreach ($f as $k => $v) {
            $this->assertEquals($k, $v);
        }
    }

    public function testFetchAll()
    {
        $f = new Fixture;
        $f->set(['a' => 'a', 'b' => 'b', 'c' => 'c']);
        foreach ($f->all() as $k => $v) {
            $this->assertEquals($k, $v);
        }
    }

    public function testSerializeUnserialize()
    {
        $f = new Fixture;
        $f->set(['a' => 'a', 'b' => 'b', 'c' => 'c']);

        $saved = serialize($f);
        $saved = unserialize($saved);

        $this->assertEquals('a', $saved->get('a'));
        $this->assertEquals('b', $saved->get('b'));
        $this->assertEquals('c', $saved->get('c'));
        $this->assertEquals($saved, $f);
    }

    public function testCountable()
    {
        $f = new Fixture;
        $f->set(['a' => 'a', 'b' => 'b', 'c' => 'c']);
        $this->assertEquals(3, count($f));
    }
}
