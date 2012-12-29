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

namespace Proem\Bootstrap\Tests;

use \Mockery as m;
use Proem\Bootstrap\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Bootstrap\Route', new Route);
    }

    public function testCanTriggerEvent()
    {
        // Handle Event

        $eventManager = m::mock('Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->with('Proem\Signal\EventInterface', 'closure')
            ->once();

        $assetManager = m::mock('Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('provides')
            ->with('eventManager', 'Proem\Signal\EventManagerInterface')
            ->once()
            ->andReturn(true);

        $assetManager
            ->shouldReceive('get')
            ->with('eventManager')
            ->once()
            ->andReturn($eventManager);

        // Event Done

        $assetManager
            ->shouldReceive('provides')
            ->with('Proem\Routing\RouteManagerInterface')
            ->once()
            ->andReturn(true);

        $route = new Route;
        $route->in($assetManager);
    }

    public function testCanSetDefaultRouteAsset()
    {
        $event = m::mock('Proem\Signal\EventInterface', ['proem.in.route']);
        $eventManager = m::mock('Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->with('Proem\Signal\EventInterface', 'closure')
            ->once();

        $assetManager = m::mock('Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('provides')
            ->with('eventManager', 'Proem\Signal\EventManagerInterface')
            ->once()
            ->andReturn(true);

        $assetManager
            ->shouldReceive('get')
            ->with('eventManager')
            ->once()
            ->andReturn($eventManager);

        $assetManager
            ->shouldReceive('provides')
            ->with('Proem\Routing\RouteManagerInterface')
            ->once()
            ->andReturn(false);

        $assetManager
            ->shouldReceive('set')
            ->with('routeManager', 'Proem\Service\AssetInterface')
            ->once();

        $route = new Route;
        $route->in($assetManager);
    }
}
