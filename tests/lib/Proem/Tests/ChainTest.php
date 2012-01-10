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

use Proem\Chain;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    private $request;
    private $response;
    private $route;
    private $dispatch;

    public function setUp() {
        $this->response = $this->getMockForAbstractClass('Proem\Chain\Event');
        $this->request  = $this->getMockForAbstractClass('Proem\Chain\Event');
        $this->route    = $this->getMockForAbstractClass('Proem\Chain\Event');
        $this->dispatch = $this->getMockForAbstractClass('Proem\Chain\Event');
    }
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Chain', new Chain);
    }

    public function testChainRun() {
        $this->response->expects($this->once())->method('inBound')->will($this->returnCallback(function() {echo "response in, ";}));
        $this->response->expects($this->once())->method('outBound')->will($this->returnCallback(function() {echo "response out";}));

        $this->request->expects($this->once())->method('inBound')->will($this->returnCallback(function() {echo "request in, ";}));
        $this->request->expects($this->once())->method('outBound')->will($this->returnCallback(function() {echo "request out, ";}));

        $this->route->expects($this->once())->method('inBound')->will($this->returnCallback(function() {echo "route in, ";}));
        $this->route->expects($this->once())->method('outBound')->will($this->returnCallback(function() {echo "route out, ";}));

        $this->dispatch->expects($this->once())->method('inBound')->will($this->returnCallback(function() {echo "dispatch in, ";}));
        $this->dispatch->expects($this->once())->method('outBound')->will($this->returnCallback(function() {echo "dispatch out, ";}));

        $this->expectOutputString('response in, request in, route in, dispatch in, dispatch out, route out, request out, response out');

        $chain = new Chain;
        $chain
            ->insertEvent($this->response, Chain::RESPONSE_EVENT_PRIORITY)
            ->insertEvent($this->request, Chain::REQUEST_EVENT_PRIORITY)
            ->insertEvent($this->route, Chain::ROUTE_EVENT_PRIORITY)
            ->insertEvent($this->dispatch, Chain::DISPATCH_EVENT_PRIORITY)
            ->init();

    }

}
