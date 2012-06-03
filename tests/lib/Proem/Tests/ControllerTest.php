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

use Proem\Autoloader;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    private $serviceManager;
    private $assets;

    public function setUp()
    {
        (new Autoloader)
            ->attachNamespace('Controller', dirname(__FILE__) . '/Fixtures')
            ->register();

        $events = new \Proem\Service\Asset\Standard;
        $events->set('\Proem\Signal\Manager\Template', $events->single(function() {
            return new \Proem\Signal\Manager\Standard;
        }));

        $this->assets = new \Proem\Service\Manager\Standard;

        $this->assets->set('events', $events);
    }

    public function testCanInstantiate()
    {
        $controller = new \Controller\Foo($this->assets);
        $this->assertInstanceOf('Proem\Controller\Template', $controller);
    }

    public function testCanDispatchAction()
    {
        $controller = new \Controller\Foo($this->assets);
        $this->assertTrue($controller->dispatch('bar'));
    }

    public function testPrePostEvents()
    {
        $controller = new \Controller\Bar($this->assets);
        $this->expectOutputString('preactionpost');
        $controller->dispatch('foo');
    }
}
