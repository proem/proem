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

namespace Proem\Test;

use Proem\Proem;
use \Mockery as m;

class ProemTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateProem()
    {
        $assetManager = m::mock('\Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('singleton')
            ->once()
            ->with(['eventManager' => '\Proem\Signal\EventManager'])

            ->shouldReceive('singleton')
            ->once()
            ->with(['chainManager' => '\Proem\Filter\ChainManager']);

        $this->assertInstanceOf('Proem\Proem', new Proem($assetManager));
    }

    public function testLoadsDefaultEventManager()
    {
        $request  = m::mock('Proem\Bootstrap\Request');
        $route    = m::mock('Proem\Bootstrap\Route');
        $dispatch = m::mock('Proem\Bootstrap\Dispatch');

        $eventManager = m::mock('\Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->once()
            ->with('\Proem\Signal\EventInterface');

        $chainManager = m::mock('\Proem\Filter\ChainManagerInterface');
        $chainManager
            ->shouldReceive('attach')
            ->times(3)
            ->andReturn($chainManager)
            ->shouldReceive('bootstrap')
            ->once();

        $assetManager = m::mock('\Proem\Service\AssetManagerInterface');

        $assetManager
            ->shouldReceive('singleton')
            ->once()
            ->with(['eventManager' => '\Proem\Signal\EventManager'])

            ->shouldReceive('singleton')
            ->once()
            ->with(['chainManager' => '\Proem\Filter\ChainManager'])

            ->shouldReceive('resolve')
            ->once()
            ->with('eventManager')
            ->andReturn($eventManager)

            ->shouldReceive('resolve')
            ->once()
            ->with('chainManager')
            ->andReturn($chainManager)

            ->shouldReceive('resolve')
            ->once()
            ->with('Proem\Bootstrap\Request')
            ->andReturn($request)

            ->shouldReceive('resolve')
            ->once()
            ->with('Proem\Bootstrap\Route')
            ->andReturn($route)

            ->shouldReceive('resolve')
            ->once()
            ->with('Proem\Bootstrap\Dispatch')
            ->andReturn($dispatch);

        (new Proem($assetManager))->bootstrap();
    }
}
