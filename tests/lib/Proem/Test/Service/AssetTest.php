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

namespace Proem\Test\Service;

use \Mockery as m;
use Proem\Service\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $a = new Asset('Foo', function() {});
        $this->assertInstanceOf('Proem\Service\AssetInterface', $a);
    }

    public function testCanMakeAsset()
    {
        $asset = new Asset('stdClass', function() {
            return new \stdClass;
        });

        $this->assertInstanceOf('\stdClass', $asset->fetch());
    }

    public function testCanMakeWithParamsParam()
    {
        $asset = new Asset('stdClass', [], function() {
            return new \stdClass;
        });

        $this->assertInstanceOf('\stdClass', $asset->fetch());
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAsset()
    {
        (new Asset('Foo', function() {
            return new \stdClass;
        }))->fetch();
    }

    public function testAssetType()
    {
        $asset = new Asset('stdClass', function() {
            return new \stdClass;
        });

        $this->assertTrue($asset->is('stdClass'));
        $this->assertEquals('stdClass', $asset->is());
    }

    public function testCanSetParams()
    {
        $asset = new Asset('stdClass', ['foo' => 'bar'], function($asset) {
            $class = new \stdClass;
            $class->foo = $asset->foo;
            return $class;
        });

        $this->assertEquals('bar', $asset->fetch()->foo);
    }

    public function testReturnsDifferentInstance()
    {
        $asset = new Asset('stdClass', function() {
            return new \StdClass;
        });

        $this->assertNotSame($asset->fetch(), $asset->fetch());
    }

    public function testReturnsSingleton()
    {
        $asset = (new Asset('stdClass'))->single(function() {
            return new \StdClass;
        });

        $this->assertSame($asset->fetch(), $asset->fetch());
    }

    public function testReturnsSingletonWithParams()
    {
        $asset = (new Asset('stdClass', ['foo' => 'bar']))->single(function($asset) {
            $class = new \stdClass;
            $class->foo = $asset->foo;
            return $class;
        });

        $this->assertSame($asset->fetch(), $asset->fetch());
        $this->assertEquals('bar', $asset->fetch()->foo);
    }
}
