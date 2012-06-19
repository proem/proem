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

use Proem\Routing\Router\Standard as Router,
    Proem\Routing\Route\Standard as Route,
    Proem\Api\IO\Request\Http\Fake as Request;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $r = new Router('');
        $this->assertInstanceOf('\Proem\Routing\Router\Template', $r);
    }

    public function testTargetedMapedRoute()
    {
        $router = new Router(new Request('/login'));
        $payload = $router->attach(
             'simple',
             new Route([
                'rule'      => '/login',
                'targets'   => ['controller' => 'auth', 'action' => 'login']
            ])
        )->route();

        $this->assertInstanceOf('\Proem\Routing\Route\Payload', $payload);
        $this->assertTrue($payload->isPopulated());
        $this->assertEquals('auth', $payload->controller);
        $this->assertEquals('login', $payload->action);
        $this->assertNull($payload->doesntexist);
    }
/*
    public function testRouteCallback()
    {
        $tmp = false;
        $router = new Router(new Request('/foo'));
        $payload = $router->attach(
             'simple',
             new Route([
                'rule'      => '/foo',
                'callback'  => function($request) use ($tmp) {
                    $tmp = $request->getRequestUri();
                }
            ])
        )->route();

        $this->assertEquals('/foo', $tmp);
    }
 */
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
        $router = new Router(new Request($uri));
        $payload = $router
            ->attach(
                'home-page',
                new Route([
                    'rule'      => '/',
                    'targets'   => ['controller' => 'home']
            ])
            )->attach(
                'login',
                new Route([
                    'rule'      => '/login',
                    'targets'   => ['controller' => 'auth', 'action' => 'login']
            ])
            )->attach(
                'logout',
                new Route([
                    'rule'      => '/logout',
                    'targets'   => ['controller' => 'auth', 'action' => 'logout']
            ])
        )->route();

        $this->assertInstanceOf('\Proem\Routing\Route\Payload', $payload);
        $this->assertTrue($payload->isPopulated());
        $this->assertEquals($controller, $payload->controller);
        $this->assertEquals($action, $payload->get('action', 'index'));
    }
}
