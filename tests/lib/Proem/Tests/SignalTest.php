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

use Proem\Signal\Event\Standard as Event,
    Proem\Signal\Manager\Standard as Manager;

class SignalTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Signal\Event\Template', new Event(['name' => 'foo', 'params' => []]));
        $this->assertInstanceOf('Proem\Signal\Manager\Template', new Manager);
    }

    public function testCanPriority()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) use ($r) {
                $r->out .= 'First';
            }
        ])->attach([
            'name'      => 'do',
            'priority'  => 100,
            'callback'  => function($e) use ($r) {
                $r->out .= 'Second';
            }
        ])->trigger(['name' => 'do']);

        $this->assertEquals('SecondFirst', $r->out);
    }

    public function testCanTriggerEventMultipleTimes()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) use ($r) {
                $r->out .= 'Yes';
            }
        ])
        ->trigger(['name' => 'do'])
        ->trigger(['name' => 'do']);

        $this->assertEquals('YesYes', $r->out);
    }

    public function testListenerCanListenToMultipleEvents()
    {
        $r = new \StdClass;
        $r->out = 0;
        (new Manager)->attach([
            'name'      => ['a', 'b', 'c'],
            'callback'  => function($e) use ($r) {
                $r->out++;
            }
        ])
        ->trigger(['name' => 'a'])
        ->trigger(['name' => 'b'])
        ->trigger(['name' => 'c']);

        $this->assertEquals(3, $r->out);
    }

    public function testListenerReceivesParams()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) use ($r) {
                $r->out = $e->getParams()['hello'];
            }
        ])
        ->trigger(['name' => 'do', 'params' => ['hello' => 'trq']]);
        $this->assertEquals('trq', $r->out);
    }

    public function testListenerCanTriggerCallback()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                return true;
            }
        ])
        ->trigger([
            'name' => 'do', 'callback' => function($response) use ($r) {
                $this->assertTrue($response);
                $r->out = 'Callback';
            }
        ]);

        $this->assertEquals('Callback', $r->out);
    }

    public function testTargetAndMethod()
    {
        $r = new \StdClass;
        $r->target = '';
        $r->method = '';
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) use ($r) {
                $r->target = $e->getTarget();
                $r->method = $e->getMethod();
            }
        ])
        // There is a caveat here with in reference to __FUNCTION__ over __METHOD__
        // __METHOD__ returns 'Proem\Tests\EventTest::testTargetAndMethod', not what we expect.
        // This will need to be documented.
        ->trigger(['name' => 'do', 'target' => $this, 'method' => __FUNCTION__]);

        $this->assertInstanceOf('\Proem\Tests\SignalTest', $r->target);
        $this->assertEquals('testTargetAndMethod', $r->method);
    }
}
