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

namespace Proem\Filter\Tests;

use \Mockery as m;
use Proem\Filter\ChainManager;
use Proem\Service\AssetManagerInterface;

class ChainManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateChainManager()
    {
        $this->assertInstanceOf('Proem\Filter\ChainManagerInterface', new ChainManager(m::mock('Proem\Service\AssetManagerInterface')));
    }

    public function testCanAttachEvent()
    {
        $chainManager = new ChainManager(m::mock('Proem\Service\AssetManagerInterface'));

        $chainEvent = m::mock('Proem\Filter\ChainEventInterface');
        $chainManager->attach($chainEvent);
        $this->assertEquals(1, count($chainManager->getQueue()));
    }

    public function testCanAttachMultipleEvents()
    {
        $chainManager = new ChainManager(m::mock('Proem\Service\AssetManagerInterface'));

        $chainEvent = m::mock('Proem\Filter\ChainEventInterface');
        $chainManager->attach($chainEvent);
        $chainEvent = m::mock('Proem\Filter\ChainEventInterface');
        $chainManager->attach($chainEvent);
        $this->assertEquals(2, count($chainManager->getQueue()));
    }

    public function testCanBootstrapEvents()
    {
        $chainManager = new ChainManager(m::mock('Proem\Service\AssetManagerInterface'));

        $chainEvent = m::mock('Proem\Filter\ChainEventAbstract[in,out]');

        $chainEvent
            ->shouldReceive('in')
            ->with('Proem\Service\AssetManagerInterface')
            ->once();

        $chainEvent
            ->shouldReceive('out')
            ->with('Proem\Service\AssetManagerInterface')
            ->once();

        $chainEvent2 = m::mock('Proem\Filter\ChainEventAbstract[in,out]');

        $chainEvent2
            ->shouldReceive('in')
            ->with('Proem\Service\AssetManagerInterface')
            ->once();

        $chainEvent2
            ->shouldReceive('out')
            ->with('Proem\Service\AssetManagerInterface')
            ->once();

        $chainManager->attach($chainEvent);
        $chainManager->attach($chainEvent2);

        $chainManager->bootstrap();
    }
}
