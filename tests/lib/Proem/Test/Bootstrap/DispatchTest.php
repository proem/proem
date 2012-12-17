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

namespace Proem\Test\Bootstrap;

use \Mockery as m;
use Proem\Bootstrap\Dispatch;

class DispatchTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Bootstrap\Dispatch', new Dispatch);
    }

    public function testCanTriggerEvent()
    {
        $eventManager = m::mock('Proem\Signal\EventManagerInterface');
        $eventManager
            ->shouldReceive('trigger')
            ->with('Proem\Signal\EventInterface')
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

        $dispatch = new Dispatch;
        $dispatch->in($assetManager);
    }
}
