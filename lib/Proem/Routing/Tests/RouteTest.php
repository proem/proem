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

namespace Proem\Routing\Tests;

use \Mockery as m;
use Proem\Routing\Route;
use Proem\Http\Request;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateRoute()
    {
        $this->assertInstanceOf('Proem\Routing\RouteInterface', new Route([]));
    }

    public function testProcessReturnsfalseOnFailedMatch()
    {
        $request = Request::create('/foo');
        $route   = new Route('/');
        $this->assertFalse($route->process($request));
    }

    public function testProcessReturnsTrueOnMatch()
    {
        $request = Request::create('/foo');
        $route   = new Route('/foo');
        $this->assertTrue($route->process($request));
    }

    public function testProcessComplextMatch()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route   = new Route('/{module}/{controller}/{action}');
        $this->assertTrue($route->process($request));
    }

    public function testTokensAreReplaced()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route   = new Route('/{module}/{controller}/{action}');
        $results = $route->process($request);
        $payload = $route->getPayload();

        $this->assertTrue($results);
        $this->assertTrue(isset($payload['module']));
        $this->assertTrue($payload['module'] == 'somemodule');
        $this->assertTrue(isset($payload['controller']));
        $this->assertTrue($payload['controller'] == 'somecontroller');
        $this->assertTrue(isset($payload['action']));
        $this->assertTrue($payload['action'] == 'someaction');
    }

    public function testTargetsAreReplaced()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route   = new Route(
            '/{module}/{controller}/{action}',
            [
                'targets' => [
                    'module'     => 'thismodule',
                    'controller' => 'thiscontroller',
                    'action'     => 'thisaction',
                ]
            ]
        );
        $results = $route->process($request);
        $payload = $route->getPayload();

        $this->assertTrue($results);
        $this->assertTrue(isset($payload['module']));
        $this->assertTrue($payload['module'] == 'thismodule');
        $this->assertTrue(isset($payload['controller']));
        $this->assertTrue($payload['controller'] == 'thiscontroller');
        $this->assertTrue(isset($payload['action']));
        $this->assertTrue($payload['action'] == 'thisaction');
    }

    public function testParamsAreExploded()
    {
        $request = Request::create('/a/b/c/d');
        $route   = new Route('/{params}');
        $results = $route->process($request);
        $payload = $route->getPayload();

        $this->assertTrue($results);
        $this->assertTrue(isset($payload['a']));
        $this->assertTrue($payload['a'] == 'b');
        $this->assertTrue(isset($payload['c']));
        $this->assertTrue($payload['c'] == 'd');
    }

    public function testDefaultFiltersMatch()
    {
        $request = Request::create('/foo/1/abc/a-b-c/a/b/c/d');
        $route   = new Route('/{default}/{int}/{alpha}/{slug}/{params}');
        $this->assertTrue($route->process($request));
    }

    public function testCustomFilterMatches()
    {
        $request = Request::create('/200');
        $route   = new Route(
            '/{custom}',
            [
                'filters' => [
                    'custom' => '[0-9]{3}'
                ]
            ]
        );
        $this->assertTrue($route->process($request));
    }

    public function testCustomFilterUsingDefaultFilter()
    {
        $request = Request::create('/200');
        $route   = new Route(
            '/{custom}',
            [
                'filters' => [
                    'custom' => '{int}'
                ]
            ]
        );
        $this->assertTrue($route->process($request));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCustomFilterUsingUndefinedDefaultFilter()
    {
        $request = Request::create('/200');
        $route   = new Route(
            '/{custom}',
            [
                'filters' => [
                    'custom' => '{foo}'
                ]
            ]
        );
        $route->process($request);
    }

    public function testOptionalSwitchMatches()
    {
        $request_with    = Request::create('/foo');
        $request_without = Request::create('/');
        $route           = new Route('/{controller?}');

        $this->assertTrue($route->process($request_with));
        $this->assertTrue($route->process($request_without));
    }

    public function testOptionalSwitchMatchesCenter()
    {
        $request_with    = Request::create('/foo/bar/bob');
        $request_without = Request::create('/foo/bob');
        $route           = new Route('/{module}/{controller?}/{action}');

        $this->assertTrue($route->process($request_with));
        $this->assertTrue($route->process($request_without));
    }

    public function testCanPositionCallbackAsSecondArg()
    {
        $route = new Route('/', function() { return true; });
        $this->assertTrue($route->hasCallback());
        $callback = $route->getCallback();
        $this->assertTrue($callback());
    }

    public function testCanPositionCallbackAsThirdArg()
    {
        $route = new Route('/', [], function() { return true; });
        $this->assertTrue($route->hasCallback());
        $callback = $route->getCallback();
        $this->assertTrue($callback());
    }

    public function testSecondCallbackIgnored()
    {
        $route = new Route('/', function() { return true; }, function() { return false; });
        $this->assertTrue($route->hasCallback());
        $callback = $route->getCallback();
        $this->assertTrue($callback());
    }

    public function testSimpleHostnameFail()
    {
        $request = Request::create('http://localhost');
        $route   = new Route('/', ['hostname' => 'foo']);
        $this->assertFalse($route->process($request));
    }

    public function testSimpleHostnameMatch()
    {
        $request = Request::create('http://foo');
        $route   = new Route('/', ['hostname' => 'foo']);
        $this->assertTrue($route->process($request));
    }

    public function testComplexHostnameMatch()
    {
        $request = Request::create('http://trq.domain.com');
        $route   = new Route('/', ['hostname' => '{username}.domain.com']);
        $results = $route->process($request);

        $this->assertTrue($results);
        $this->assertEquals('trq', $route->getPayload()['username']);
    }
}
