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
        (new Manager)->attach('do', function($e) use ($r) {
            $r->out .= 'First';
        })->attach('do', function($e) use ($r) {
            $r->out .= 'Second';
        }, 100)
            ->trigger(new Event('do'));

        $this->assertEquals('SecondFirst', $r->out);
    }

    public function testCanTriggerEventMultipleTimes()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach('do', function($e) use ($r) {
            $r->out .= 'Yes';
        })
        ->trigger(new Event('do'))
        ->trigger(new Event('do'));

        $this->assertEquals('YesYes', $r->out);
    }

    public function testListenerCanListenToMultipleEvents()
    {
        $r = new \StdClass;
        $r->out = 0;
        (new Manager)->attach(['a', 'b', 'c'], function($e) use ($r) {
            $r->out++;
        })
        ->trigger(new Event('a'))
        ->trigger(new Event('b'))
        ->trigger(new Event('c'));

        $this->assertEquals(3, $r->out);
    }

    public function testListenerCanListenToAllEvents()
    {
        $r = new \StdClass;
        $r->out = 0;
        (new Manager)->attach('.*', function($e) use ($r) {
            $r->out++;
        })
        ->trigger(new Event('a'))
        ->trigger(new Event('b'))
        ->trigger(new Event('c'));

        $this->assertEquals(3, $r->out);
    }

    public function testNamespaces()
    {
        $r = new \StdClass;
        $r->out = 0;

        (new Manager)->attach('.*', function($e) use ($r) {
            $r->out++;
        })
        ->attach('this.*', function($e) use ($r) {
            $r->out++;
        })
        ->attach('this.is.some.*', function($e) use ($r) {
            $r->out++;
        })
        ->trigger(new Event('this.is.some.event'));

        $this->assertEquals(3, $r->out);
    }

    public function testListenerReceivesParams()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach('do', function($e) use ($r) {
            $r->out = $e->getParams()['hello'];
        })
        ->trigger((new Event('do'))->setParams(['hello' => 'trq']));
        $this->assertEquals('trq', $r->out);
    }

    public function testListenerCanTriggerCallback()
    {
        $r = new \StdClass;
        $r->out = '';
        (new Manager)->attach('do', function($e) {
            return $e;
        })
        ->trigger(new Event('do'),
            function($response) use ($r) {
                $this->assertInstanceOf('Proem\Signal\Event\Template', $response);
                $r->out = 'Callback';
            }
        );

        $this->assertEquals('Callback', $r->out);
    }
}
