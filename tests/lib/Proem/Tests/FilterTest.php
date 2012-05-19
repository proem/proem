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

use Proem\Filter\Manager\Standard as FilterManager,
    Proem\Service\Manager\Standard as ServiceManager;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    private $request;
    private $response;
    private $route;
    private $dispatch;

    public function setUp() {
        $this->response = $this->getMockForAbstractClass('Proem\Filter\Event\Generic');
        $this->request  = $this->getMockForAbstractClass('Proem\Filter\Event\Generic');
        $this->route    = $this->getMockForAbstractClass('Proem\Filter\Event\Generic');
        $this->dispatch = $this->getMockForAbstractClass('Proem\Filter\Event\Generic');
    }
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Filter\Manager\Standard', new FilterManager(new ServiceManager));
    }

    public function testFilterManagerRun() {
        $r = new \StdClass;
        $r->out = '';
        $this->response->expects($this->once())->method('inBound')->will($this->returnCallback(function() use ($r)  {$r->out .= "response in, ";}));
        $this->response->expects($this->once())->method('outBound')->will($this->returnCallback(function() use ($r) {$r->out .= "response out";}));

        $this->request->expects($this->once())->method('inBound')->will($this->returnCallback(function() use ($r)   {$r->out .= "request in, ";}));
        $this->request->expects($this->once())->method('outBound')->will($this->returnCallback(function() use ($r)  {$r->out .= "request out, ";}));

        $this->route->expects($this->once())->method('inBound')->will($this->returnCallback(function() use ($r)     {$r->out .= "route in, ";}));
        $this->route->expects($this->once())->method('outBound')->will($this->returnCallback(function() use ($r)    {$r->out .= "route out, ";}));

        $this->dispatch->expects($this->once())->method('inBound')->will($this->returnCallback(function() use ($r)  {$r->out .= "dispatch in, ";}));
        $this->dispatch->expects($this->once())->method('outBound')->will($this->returnCallback(function() use ($r) {$r->out .= "dispatch out, ";}));


        (new FilterManager)
            ->setServiceManager(new ServiceManager)
            ->attachEvent($this->response, FilterManager::RESPONSE_EVENT_PRIORITY)
            ->attachEvent($this->request, FilterManager::REQUEST_EVENT_PRIORITY)
            ->attachEvent($this->route, FilterManager::ROUTE_EVENT_PRIORITY)
            ->attachEvent($this->dispatch, FilterManager::DISPATCH_EVENT_PRIORITY)
            ->init();

        $this->assertEquals('response in, request in, route in, dispatch in, dispatch out, route out, request out, response out', $r->out);
    }

}
