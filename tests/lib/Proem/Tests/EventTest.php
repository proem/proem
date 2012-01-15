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

use Proem\Event,
    Proem\Event\Manager;

class EventTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Event', new Event(['name' => 'foo', 'params' => []]));
        $this->assertInstanceOf('Proem\Event\Manager', new Manager);
    }

    public function testCanPriority()
    {
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                echo "First";
            }
        ])->attach([
            'name'      => 'do',
            'priority'  => 100,
            'callback'  => function($e) {
                echo "Second";
            }
        ])->trigger(['name' => 'do']);

        $this->expectOutputString('SecondFirst');
    }

    public function testUniquenessOfTriggers()
    {
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                echo "Yes";
            }
        ])
        ->trigger(['name' => 'do'])
        ->trigger(['name' => 'do']);

        $this->expectOutputString('Yes');
    }

    public function testListenerReceivesParams()
    {
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                echo $e->getParams()['hello'];
            }
        ])
        ->trigger(['name' => 'do', 'params' => ['hello' => 'trq']]);

        $this->expectOutputString('trq');
    }

    public function testListenerCanTriggerCallback()
    {
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                return true;
            }
        ])
        ->trigger([
            'name' => 'do', 'callback' => function() {
                echo "Callback";
            }
        ]);

        $this->expectOutputString('Callback');
    }

    public function testTargetAndMethod()
    {
        (new Manager)->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                $this->assertInstanceOf('\Proem\Tests\EventTest', $e->getTarget());
                $this->assertEquals('testTargetAndMethod', $e->getMethod());
            }
        ])
        // There is a caveat here with in reference to __FUNCTION__ over __METHOD__
        // __METHOD__ returns 'Proem\Tests\EventTest::testTargetAndMethod', not what we expect.
        // This will need to be documented.
        ->trigger(['name' => 'do', 'target' => $this, 'method' => __FUNCTION__]);
    }
}
