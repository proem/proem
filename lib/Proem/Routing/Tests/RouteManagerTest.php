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

namespace Proem\Routing\Tests;

use \Mockery as m;
use Proem\Routing\RouteManager;

class RouteManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Routing\RouteManagerInterface', new RouteManager(m::mock('Proem\Http\Request')));
    }

    public function testCanAttachRoutes()
    {
        $routeManager = new RouteManager(m::mock('Proem\Http\Request'));
        $a = m::mock('Proem\Routing\RouteInterface');
        $a->shouldReceive('getOptions')->once();

        $b = m::mock('Proem\Routing\RouteInterface');
        $b->shouldReceive('getOptions')->once();

        $c = m::mock('Proem\Routing\RouteInterface');
        $c->shouldReceive('getOptions')->once();

        $routeManager->attach('a', $a);
        $routeManager->attach('b', $b);
        $routeManager->attach('c', $c);

        $this->assertEquals(3, count($routeManager->getRoutes()['*']));
    }

    public function testCanAttachRoutesToDifferentMethods()
    {
        $routeManager = new RouteManager(m::mock('Proem\Http\Request'));
        $a = m::mock('Proem\Routing\RouteInterface');
        $a->shouldReceive('getOptions')->once();

        $b = m::mock('Proem\Routing\RouteInterface');
        $b->shouldReceive('getOptions')
            ->andReturn(['method' => 'GET'])
            ->once();

        $c = m::mock('Proem\Routing\RouteInterface');
        $c->shouldReceive('getOptions')
            ->andReturn(['method' => 'POST'])
            ->once();

        $routeManager->attach('a', $a);
        $routeManager->attach('b', $b);
        $routeManager->attach('c', $c);

        $this->assertEquals(1, count($routeManager->getRoutes()['*']));
        $this->assertEquals(1, count($routeManager->getRoutes()['GET']));
        $this->assertEquals(1, count($routeManager->getRoutes()['POST']));
    }

    public function testSingleRoute()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->once();

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true);
        $route->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $routeManager = new RouteManager($request);

        $routeManager->attach('simple', $route);

        $this->assertInstanceOf('Proem\Routing\RouteInterface', $routeManager->route());
    }

    public function testMultipleMatchingRoutes()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->once();

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true);
        $route->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $route2 = m::mock('Proem\Routing\RouteInterface');
        $route2->shouldReceive('process')
            ->once()
            ->andReturn(true);
        $route2->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $route);
        $routeManager->attach('r2', $route2);

        $matches = 0;
        while ($match = $routeManager->route()) {
            $matches++;
        }

        $this->assertEquals(2, $matches);
    }

    public function testMultipleMatchingAndFailingRoutes()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->once();

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true); // match
        $route->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $route2 = m::mock('Proem\Routing\RouteInterface');
        $route2->shouldReceive('process')
            ->once()
            ->andReturn(false); // fail
        $route2->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $route3 = m::mock('Proem\Routing\RouteInterface');
        $route3->shouldReceive('process')
            ->once()
            ->andReturn(true); // match
        $route3->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $route4 = m::mock('Proem\Routing\RouteInterface');
        $route4->shouldReceive('process')
            ->once()
            ->andReturn(false); // fail
        $route4->shouldReceive('getOptions')
            ->once()
            ->andReturn(['rule' => '/']);

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $route);
        $routeManager->attach('r2', $route2);
        $routeManager->attach('r3', $route3);
        $routeManager->attach('r4', $route4);

        $matches = 0;
        while ($match = $routeManager->route()) {
            $matches++;
        }

        $this->assertEquals(2, $matches);
    }

    public function testSpecificMethodTakesPrecedence()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->times(4)
            ->andReturn('GET');

        $with = m::mock('Proem\Routing\RouteInterface');
        $with->shouldReceive('process')
            ->twice()
            ->andReturn(true);
        $with->shouldReceive('getOptions')
            ->twice()
            ->andReturn(['rule' => '/', 'method' => 'GET']);

        $without = m::mock('Proem\Routing\RouteInterface');
        $without->shouldReceive('process')
            ->twice()
            ->andReturn(true);
        $without->shouldReceive('getOptions')
            ->twice()
            ->andReturn(['rule' => '/']);

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $with);
        $routeManager->attach('r2', $without);

        $first  = $routeManager->route();
        $second = $routeManager->route();

        $this->assertSame($first, $with);
        $this->assertSame($second, $without);

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $without);
        $routeManager->attach('r2', $with);

        $first  = $routeManager->route();
        $second = $routeManager->route();

        $this->assertSame($first, $with);
        $this->assertSame($second, $without);
    }

    public function testSpecificMethodOfSameNameTakesPrecedence()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->times(4)
            ->andReturn('GET');

        $with = m::mock('Proem\Routing\RouteInterface');
        $with->shouldReceive('process')
            ->twice()
            ->andReturn(true);
        $with->shouldReceive('getOptions')
            ->twice()
            ->andReturn(['rule' => '/', 'method' => 'GET']);

        $without = m::mock('Proem\Routing\RouteInterface');
        $without->shouldReceive('getOptions');

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $with);
        $routeManager->attach('r1', $without);

        $match  = $routeManager->route();

        $this->assertSame($match, $with);

        $routeManager = new RouteManager($request);

        $routeManager->attach('r1', $without);
        $routeManager->attach('r1', $with);

        $match  = $routeManager->route();

        $this->assertSame($match, $with);
    }
}
