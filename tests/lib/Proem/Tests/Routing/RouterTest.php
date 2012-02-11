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

use Proem\Routing\Router,
    Proem\Routing\Route\Standard;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $r = new Router('');
        $this->assertInstanceOf('\Proem\Routing\Router', $r);
    }

    public function testTargetedMapedRoute()
    {
        $router = new Router('/login');
        $payload = $router->map(
             'simple',
             new Standard([
                'rule'      => '/login',
                'targets'   => ['controller' => 'auth', 'action' => 'login']
            ])
        )->route();

        $this->assertInstanceOf('\Proem\Dispatch\Payload', $payload);
        $this->assertTrue($payload->isPopulated());
        $this->assertEquals('auth', $payload->getParam('controller'));
        $this->assertEquals('login', $payload->getParam('action'));
        $this->assertNull($payload->getParam('doesntexist'));
    }

    public function dataProvider()
    {
        return [
            ['/', 'home', 'index'],
            ['/login', 'auth', 'login'],
            ['/logout', 'auth', 'logout']
         ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSeriesOfMappedRoutes($uri, $controller, $action)
    {
        $router = new Router($uri);
        $payload = $router
            ->map(
                'home-page',
                new Standard([
                    'rule'      => '/',
                    'targets'   => ['controller' => 'home']
            ])
            )->map(
                'login',
                new Standard([
                    'rule'      => '/login',
                    'targets'   => ['controller' => 'auth', 'action' => 'login']
            ])
            )->map(
                'logout',
                new Standard([
                    'rule'      => '/logout',
                    'targets'   => ['controller' => 'auth', 'action' => 'logout']
            ])
        )->route();

        $this->assertInstanceOf('\Proem\Dispatch\Payload', $payload);
        $this->assertTrue($payload->isPopulated());
        $this->assertEquals($controller, $payload->getParam('controller'));
        $this->assertEquals($action, $payload->getParam('action', 'index'));
    }
}
