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
use Proem\Routing\Route;
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

        $this->assertEquals(3, count($routeManager->getRoutes()['http']['*']));
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

        $this->assertEquals(1, count($routeManager->getRoutes()['http']['*']));
        $this->assertEquals(1, count($routeManager->getRoutes()['http']['GET']));
        $this->assertEquals(1, count($routeManager->getRoutes()['http']['POST']));
    }

    public function testSingleRoute()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->once()

            ->shouldReceive('getScheme')
            ->once()
            ->andReturn('http');

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->once()
            ->andReturn([])

            ->shouldReceive('hasCallback')
            ->once()
            ->andReturn(false);

        $routeManager = new RouteManager($request);

        $routeManager->attach('simple', $route);

        $this->assertInstanceOf('Proem\Routing\RouteInterface', $routeManager->route());
    }

    public function testMultipleMatchingRoutes()
    {
        $request = m::mock('Proem\Http\Request');

        $request->shouldReceive('getMethod')
            ->once();

        $request->shouldReceive('getScheme')
            ->once()
            ->andReturn('http');

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->once()

            ->shouldReceive('hasCallback')
            ->once()
            ->andReturn(false);

        $route2 = m::mock('Proem\Routing\RouteInterface');
        $route2->shouldReceive('process')
            ->once()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->once()

            ->shouldReceive('hasCallback')
            ->once()
            ->andReturn(false);

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

        $request->shouldReceive('getScheme')
            ->once()
            ->andReturn('http');

        $route = m::mock('Proem\Routing\RouteInterface');
        $route->shouldReceive('process')
            ->once()
            ->andReturn(true) // match

            ->shouldReceive('getOptions')
            ->once()

            ->shouldReceive('hasCallback')
            ->once()
            ->andReturn(false);

        $route2 = m::mock('Proem\Routing\RouteInterface');
        $route2->shouldReceive('process')
            ->once()
            ->andReturn(false) // fail

            ->shouldReceive('getOptions')
            ->once();

        $route3 = m::mock('Proem\Routing\RouteInterface');
        $route3->shouldReceive('process')
            ->once()
            ->andReturn(true) // match

            ->shouldReceive('getOptions')
            ->once()

            ->shouldReceive('hasCallback')
            ->once()
            ->andReturn(false);

        $route4 = m::mock('Proem\Routing\RouteInterface');
        $route4->shouldReceive('process')
            ->once()
            ->andReturn(false) // fail

            ->shouldReceive('getOptions')
            ->once();

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
            ->times(2)
            ->andReturn('GET');

        $request->shouldReceive('getScheme')
            ->times(2)
            ->andReturn('http');

        $with = m::mock('Proem\Routing\RouteInterface');
        $with->shouldReceive('process')
            ->twice()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->twice()
            ->andReturn(['method' => 'GET'])

            ->shouldReceive('hasCallback')
            ->twice()
            ->andReturn(false);

        $without = m::mock('Proem\Routing\RouteInterface');
        $without->shouldReceive('process')
            ->twice()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->twice()

            ->shouldReceive('hasCallback')
            ->twice()
            ->andReturn(false);

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
            ->times(2)
            ->andReturn('GET');

        $request->shouldReceive('getScheme')
            ->times(2)
            ->andReturn('http');

        $with = m::mock('Proem\Routing\RouteInterface');
        $with->shouldReceive('process')
            ->twice()
            ->andReturn(true)

            ->shouldReceive('getOptions')
            ->twice()
            ->andReturn(['method' => 'GET'])

            ->shouldReceive('hasCallback')
            ->twice()
            ->andReturn(false);

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

    public function testCanGroupByAttributes()
    {
        $request = m::mock('Proem\Http\Request');

        $routeManager = new RouteManager($request);

        $routeManager->group(['foo' => 'bar'], function() use ($routeManager) {
            $routeManager->attach('r1', new \Proem\Routing\Route('/'));
            $routeManager->attach('r2', new \Proem\Routing\Route('/'));
        });

        foreach ($routeManager->getRoutes()['http']['*'] as $route) {
            $this->assertEquals('bar', $route->getOptions()['foo']);
        }
    }

    public function testCanGroupByMethodAttribute()
    {
        $request = m::mock('Proem\Http\Request');

        $routeManager = new RouteManager($request);

        $routeManager->group(['method' => 'GET'], function() use ($routeManager) {
            $routeManager->attach('r1', new \Proem\Routing\Route('/'));
            $routeManager->attach('r2', new \Proem\Routing\Route('/'));
        });

        $this->assertEquals(2, count($routeManager->getRoutes()['http']['GET']));
    }

    public function testCanGroupByHostname()
    {
        $request = \Proem\Http\Request::create('http://domain.com');

        $routeManager = new RouteManager($request);

        $routeManager->group(['hostname' => 'domain.com'], function() use ($routeManager) {
            $routeManager->attach('r1', new \Proem\Routing\Route('/'));
            $routeManager->attach('r2', new \Proem\Routing\Route('/'));
        });

        $routeManager->attach('r3', new \Proem\Routing\Route('/', ['hostname' => 'sub.domain.com']));

        $matchCount = 0;

        while ($routeManager->route()) {
            $matchCount++;
        }

        $this->assertEquals(2, $matchCount);

    }

    public function testScheme()
    {
        $request = \Proem\Http\Request::create('https://domain.com');
        $result  = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/'))
            ->route();

        $this->assertFalse($result);

        $result  = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/', ['scheme' => 'https']))
            ->route();

        $this->assertInstanceOf('\Proem\Routing\Route', $result);
    }

    public function testSchemeDefault()
    {
        $request = \Proem\Http\Request::create('http://domain.com');
        $result  = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/'))
            ->route();

        $this->assertInstanceOf('\Proem\Routing\Route', $result);
    }

    public function testCanSwitchSchemeDefault()
    {
        $request = \Proem\Http\Request::create('https://domain.com');
        $result  = (new RouteManager($request, 'https'))
            ->attach('r1', new \Proem\Routing\Route('/'))
            ->route();

        $this->assertInstanceOf('\Proem\Routing\Route', $result);
    }

    public function testCanHandleCallbackRoute()
    {
        $request = \Proem\Http\Request::create('/foo');
        $result  = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/foo', [], function() { return "bar"; }))
            ->route();

        $this->assertEquals('bar', $result);
    }

    public function testCallbackGetsRequest()
    {
        $request = \Proem\Http\Request::create('/foo');
        $result  = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/foo', [], function() use($request) { return $request; }))
            ->route();

        $this->assertInstanceOf('\Proem\Http\Request', $result);
    }

    public function testCallbackGetsNamedArgs()
    {
        $request = \Proem\Http\Request::create('/view/trq');
        list($action, $username) = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route('/{action}/{username}', [], function($a, $u) { return [$a, $u]; }))
            ->route();

        $this->assertEquals('view', $action);
        $this->assertEquals('trq', $username);
    }

    public function testCallbackGetsArgsOfType()
    {
        $request = \Proem\Http\Request::create('/user/123');
        list($action, $id) = (new RouteManager($request))
            ->attach('r1', new \Proem\Routing\Route(
                '/{action}/{id}',
                ['filters' => [
                    'action' => '{alpha}',
                    'id' => '{int}'
                ]],
                function($action, $id) {
                    return [$action, $id];
                }
            ))
            ->route();

        $this->assertEquals('user', $action);
        $this->assertEquals(123, $id);
    }
}
