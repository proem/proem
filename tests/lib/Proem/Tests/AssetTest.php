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

namespace Proem\Tests;

use Proem\Asset,
    Proem\Asset\Manager,
    Proem\Asset\Foo,
    Proem\Asset\Bar;

class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $a = new Asset;
        $this->assertInstanceOf('Proem\Asset', $a);
    }

    public function testCanInstantiateAssetManager()
    {
        $am = new Manager;
        $this->assertInstanceOf('Proem\Asset\Manager', $am);
    }

    public function testAssetCanInstantiate()
    {
        $bar = new Asset;
        $bar->setAsset(function() {
            return new Bar;
        });

        $this->assertInstanceOf('Proem\Asset\Bar', $bar->getAsset());
    }

    public function testAssetCanSetParams()
    {
        $foo = new Asset;
        $foo->setParam('name', 'trq')
            ->setAsset(function($a) {
                return new Foo($a->getParam('name'));
            });

        $asset = $foo->getAsset();

        $this->assertEquals('Hello trq', $asset->say());
    }

    public function testSingleReturnsDifferentInstance()
    {
        $bar = new Asset;
        $bar->setAsset(function() {
            return new Bar;
        });

        $one = $bar->getAsset();
        $this->assertInstanceOf('Proem\Asset\Bar', $one);

        $two = $bar->getAsset();
        $this->assertInstanceOf('Proem\Asset\Bar', $two);

        $this->assertNotSame($one, $two);

    }

    public function testSingleReturnsSameInstance()
    {
        $bar = new Asset;
        $bar->setAsset($bar->single(function() {
            return new Bar;
        }));

        $one = $bar->getAsset();
        $this->assertInstanceOf('Proem\Asset\Bar', $one);

        $two = $bar->getAsset();
        $this->assertInstanceOf('Proem\Asset\Bar', $two);

        $this->assertSame($one, $two);

    }

    public function testAssetManagerCanStoreAndRetrieve()
    {
        $bar = new Asset;
        $bar->setAsset(function() {
            return new Bar;
        });

        $am = new Manager;
        $am->setAsset('bar', $bar);

        $this->assertInstanceOf('Proem\Asset\Bar', $am->getAsset('bar'));
    }

    public function testCanGetDepsThroughManager()
    {
        $bar = new Asset;
        $bar->setAsset(function() {
            return new Bar;
        });

        $foo = new Asset;
        $foo->setAsset(function($a, $am) {
            $f = new Foo('something');
            $f->setBar($am->getAsset('bar'));
            return $f;
        });

        $am = new Manager;
        $am->setAsset('foo', $foo)->setAsset('bar', $bar);

        $this->assertInstanceOf('Proem\Asset\Bar', $am->getAsset('bar'));
        $this->assertInstanceOf('Proem\Asset\Foo', $am->getAsset('foo'));
        $this->assertInstanceOf('Proem\Asset\Bar', $am->getAsset('foo')->getBar());
    }
}
