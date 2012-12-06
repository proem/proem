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

namespace Proem\Test;

use Proem\Proem;
use \Mockery as m;

class ProemTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateProem()
    {
        $assetManager = \Mockery::mock('\Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('provides')
            ->once()
            ->with('Proem\Signal\EventManagerInterface')
            ->andReturn(true);

        $this->assertInstanceOf('Proem\Proem', new Proem($assetManager));
    }

    public function testLoadsDefaultEventManager()
    {
        $eventManager = m::mock('\Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->once()
            ->with('\Proem\Signal\EventInterface');

        $assetManager = m::mock('\Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('provides')
            ->once()
            ->with('Proem\Signal\EventManagerInterface')
            ->andReturn(false);

        $assetManager
            ->shouldReceive('set')
            ->once()
            ->with('EventManager', 'Proem\Service\Asset');

        $assetManager
            ->shouldReceive('get')
            ->once()
            ->with('EventManager')
            ->andReturn($eventManager);

        (new Proem($assetManager))->bootstrap();
    }

    /*
    public function testBootstrapTriggersInitEvent()
    {
        $eventManager = m::mock('\Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->once()
            ->with('\Proem\Signal\EventInterface');

        $assetManager = m::mock('\Proem\Service\AssetManagerInterface');
        $assetManager
            ->shouldReceive('provides')
            ->once()
            ->with('Proem\Signal\EventManagerInterface')
            ->andReturn(true);

        $assetManager
            ->shouldReceive('get')
            ->once()
            ->with('EventManager')
            ->andReturn($eventManager);

        (new Proem($assetManager))->bootstrap();
    }
     */
}
