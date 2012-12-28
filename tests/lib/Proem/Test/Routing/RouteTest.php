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

namespace Proem\Test\Routing;

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
        $route = new Route([
            'rule' => '/',
        ]);
        $this->assertFalse($route->process($request));
    }

    public function testProcessReturnsResultsOnMatch()
    {
        $request = Request::create('/foo');
        $route = new Route([
            'rule' => '/foo',
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    public function testProcessComplextMatch()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route = new Route([
            'rule' => '/:module/:controller/:action',
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    public function testTokensAreReplaced()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route = new Route([
            'rule' => '/:module/:controller/:action',
        ]);
        $results = $route->process($request);

        $this->assertTrue(is_array($results));
        $this->assertTrue(isset($results['module']));
        $this->assertTrue($results['module'] == 'somemodule');
        $this->assertTrue(isset($results['controller']));
        $this->assertTrue($results['controller'] == 'somecontroller');
        $this->assertTrue(isset($results['action']));
        $this->assertTrue($results['action'] == 'someaction');
    }

    public function testTargetsAreReplaced()
    {
        $request = Request::create('/somemodule/somecontroller/someaction');
        $route = new Route([
            'rule'    => '/:module/:controller/:action',
            'targets' => [
                'module'     => 'thismodule',
                'controller' => 'thiscontroller',
                'action'     => 'thisaction',
            ]
        ]);
        $results = $route->process($request);

        $this->assertTrue(is_array($results));
        $this->assertTrue(isset($results['module']));
        $this->assertTrue($results['module'] == 'thismodule');
        $this->assertTrue(isset($results['controller']));
        $this->assertTrue($results['controller'] == 'thiscontroller');
        $this->assertTrue(isset($results['action']));
        $this->assertTrue($results['action'] == 'thisaction');
    }

    public function testParamsAreExploded()
    {
        $request = Request::create('/a/b/c/d');
        $route = new Route([
            'rule' => '/:params',
        ]);
        $results = $route->process($request);

        $this->assertTrue(is_array($results));
        $this->assertTrue(isset($results['a']));
        $this->assertTrue($results['a'] == 'b');
        $this->assertTrue(isset($results['c']));
        $this->assertTrue($results['c'] == 'd');
    }

    public function testDefaultFiltersMatch()
    {
        $request = Request::create('/foo/1/abc/a-b-c/a/b/c/d');
        $route = new Route([
            'rule' => '/:default/:int/:alpha/:slug/:params',
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    public function testCustomFilterMatches()
    {
        $request = Request::create('/200');
        $route = new Route([
            'rule'    => '/:custom',
            'filters' => ['custom' => '[0-9]{3}']
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    public function testCustomFilterUsingDefaultFilter()
    {
        $request = Request::create('/200');
        $route = new Route([
            'rule'    => '/:custom',
            'filters' => ['custom' => ':int']
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testCustomFilterUsingUndefinedDefaultFilter()
    {
        $request = Request::create('/200');
        $route = new Route([
            'rule'    => '/:custom',
            'filters' => ['custom' => ':foo']
        ]);
        $this->assertTrue(is_array($route->process($request)));
    }

    public function testOptionalSwitchMatches()
    {
        $request_with    = Request::create('/foo');
        $request_without = Request::create('/');
        $route = new Route([
            'rule'    => '/:controller?'
        ]);
        $this->assertTrue(is_array($route->process($request_with)));
        $this->assertTrue(is_array($route->process($request_without)));
    }

    public function testOptionalSwitchMatchesCenter()
    {
        $request_with    = Request::create('/foo/bar/bob');
        $request_without = Request::create('/foo/bob');
        $route = new Route([
            'rule'    => '/:module/:controller?/:action'
        ]);
        $this->assertTrue(is_array($route->process($request_with)));
        $this->assertTrue(is_array($route->process($request_without)));
    }
}
