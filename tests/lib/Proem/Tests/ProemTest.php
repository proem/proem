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

use Proem\Proem;

class ProemTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Proem', new Proem);
    }

    public function testCascadingFilesystem()
    {
        $proem = new Proem;
        $this->assertTrue($proem->somethingNew());
    }

    /**
     * This test injects a Foo object into the main service manager
     * via a plugin (\Proem\Ext\Plugin\Foo - Found in Fixtures) which
     * attaches on the pre.in.dispatch event. It then uses an event
     * to test to see of that object has indeed been injected properly.
     */
    public function testCanLoadExtensions()
    {
        $r          = new \StdClass;
        $r->result  = false;
        $proem      = new Proem;
        $proem
            ->attachEventListener([
                'name'      => 'post.in.dispatch',
                'callback'  => function($e) use ($r) {
                    if ($e->getServiceManager()->has('foo')) {
                        $r->result = true;
                    }
                }
            ])
            ->attachPlugin(new \Proem\Ext\Plugin\Foo)
            ->init();

        $this->assertTrue($r->result);
    }

    public function testBootstrap()
    {
        $results            = new \StdClass;
        $results->triggered = 0;
        $results->event     = false;
        $results->target    = false;
        $results->method    = false;
        $results->init      = false;
        $results->shutdown  = false;

        (new Proem)
            ->attachEventListener([
                'name'      => 'pre.in.response',
                'callback'  => function($e) use ($results) {
                    $results->event = $e;
                    $results->target = $e->getTarget();
                    $results->method = $e->getMethod();
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'post.in.response',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'pre.in.request',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'post.in.request',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'pre.in.router',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'post.in.router',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'pre.in.dispatch',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])
            ->attachEventListener([
                'name'      => 'post.in.dispatch',
                'callback'  => function($e) use ($results) {
                    $results->triggered++;
                }
            ])->attachEventListeners([
                [
                    'name'      => 'init',
                    'callback'  => function($e) use ($results) {
                        $results->init = true;
                    }
                ],
                [
                    'name'      => 'shutdown',
                    'callback'  => function($e) use ($results) {
                        $results->shutdown = true;
                    }
                ]
            ])
        ->init();

        $this->assertEquals(8, $results->triggered);
        $this->assertInstanceOf('Proem\Bootstrap\Signal\Event\Bootstrap', $results->event);
        $this->assertInstanceOf('Proem\Api\Bootstrap\Filter\Event\Response', $results->target);
        $this->assertEquals('preIn', $results->method);
    }
}
