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

use Proem\Routing\Route\Standard,
    Proem\IO\Request\Http\Fake as Request;

class StandardRouteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $r = new Standard(['rule' => '/']);
        $this->assertInstanceOf('Proem\Routing\Route\Generic', $r);
    }

    public function testMapViaMethod()
    {
        $route = new Standard([
            'rule'      => '/',
            'method'    => 'POST',
        ]);

        $route->process(new Request('/'));
        $this->assertFalse($route->getPayload()->isPopulated());

        $route->process(new Request('/', 'POST'));
        $this->assertTrue($route->getPayload()->isPopulated());
    }

    public function testController()
    {
        $route = new Standard([
            'rule'      => '/:controller/:action/:params'
        ]);
        $route->process(new Request('/foo/bar/a/b'));

        $this->assertTrue($route->getPayload()->isPopulated());
        $this->assertEquals('foo', $route->getPayload()->controller);
        $this->assertEquals('bar', $route->getPayload()->action);
        $this->assertEquals('b', $route->getPayload()->params['a']);
    }

    public function testModule()
    {
        $route = new Standard([
            'rule'      => '/:module/:controller/:action/:params'
        ]);
        $route->process(new Request('/bob/foo/bar/a/b'));

        $this->assertTrue($route->getPayload()->isPopulated());
        $this->assertEquals('bob', $route->getPayload()->module);
        $this->assertEquals('foo', $route->getPayload()->controller);
        $this->assertEquals('bar', $route->getPayload()->action);
        $this->assertEquals('b', $route->getPayload()->params['a']);
    }

    public function testAnotherRoute()
    {
        $route = new Standard([
            'rule'      => '/foo/bar/:id',
            'targets'   => [
                'module'        => 'default',
                'controller'    => 'foo',
                'action'        => 'bar'
            ],
            'filters' => ['id' => ':int']
        ]);
        $route->process(new Request('/foo/bar/12'));

        $this->assertTrue($route->getPayload()->isPopulated());
        $this->assertEquals('default', $route->getPayload()->module);
        $this->assertEquals('foo', $route->getPayload()->controller);
        $this->assertEquals('bar', $route->getPayload()->action);
        $this->assertEquals(12, $route->getPayload()->id);
    }

    public function testAlphaFilter()
    {
        $route = new Standard([
            'rule'      => '/:data',
            'filters'   => ['data' => ':alpha']
        ]);
        $route->process(new Request('/foo'));

        $this->assertTrue($route->getPayload()->isPopulated());
        $this->assertEquals('foo', $route->getPayload()->data);

        $route = new Standard([
            'rule'      => '/:data',
            'filters'   => ['data' => ':alpha']
        ]);
        $route->process(new Request('/123'));

        $this->assertFalse($route->getPayload()->isPopulated());
    }
}
