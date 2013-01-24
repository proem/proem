<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2013 Tony R Quilkey <trq@proemframework.org>
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

namespace Proem\Signal\Tests;

use Proem\Signal\Event;
use Proem\Signal\EventManager;

/**
 * TODO: There is quite a few Event objects in here that should likely be mocked
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Signal\EventManagerInterface', new EventManager);
    }

    public function testSimpleEvent()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)
            ->attach('do', function($e) use ($r) {
                $r->out .= 'yep';
            })->trigger(new Event('do'));

        $this->assertEquals('yep', $r->out);
    }

    public function testCanTriggerWithoutEventObject()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)
            ->attach('do', function($e) use ($r) {
                $r->out .= 'yep';
            })->trigger('do');

        $this->assertEquals('yep', $r->out);
    }

    public function testCanRemoveEvent()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)
            ->attach('do', function($e) use ($r) {
                $r->out .= 'yep';
            }
        )
        ->remove('do')
        ->trigger(new Event('do'));

        $this->assertEquals('', $r->out);
    }

    public function testCanPriority()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)->attach('do', function($e) use ($r) {
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
        $em = (new EventManager)->attach('do', function($e) use ($r) {
            $r->out .= 'Yes';
        });
        $em->trigger(new Event('do'));
        $em->trigger(new Event('do'));

        $this->assertEquals('YesYes', $r->out);
    }

    public function testMultipleListenersReturnMultipleResults()
    {
        $r = new \StdClass;
        $r->out = '';
        $em = new EventManager;
        $em->attach('do', function($e) use ($r) {
            $e->set('a', 1);
            $r->out .= 'Yes';
            return $e;
        })
        ->attach('do', function($e) use ($r) {
            $e->set('a', 2);
            $r->out .= 'Yes';
            return $e;
        });

        $results = $em->trigger(new Event('do'));

        $total = 0;
        foreach ($results as $result) {
            $total += $result->get('a');
        }

        $this->assertEquals('YesYes', $r->out);
        $this->assertEquals(3, $total);
    }

    public function testListenerCanListenToMultipleEvents()
    {
        $r = new \StdClass;
        $r->out = 0;
        $em = (new EventManager)->attach(['a', 'b', 'c'], function($e) use ($r) {
            $r->out++;
        });
        $em->trigger(new Event('a'));
        $em->trigger(new Event('b'));
        $em->trigger(new Event('c'));

        $this->assertEquals(3, $r->out);
    }

    public function testCanHaltQueue()
    {
        $r = new \StdClass;
        $r->out = 0;
        (new EventManager)
            ->attach('a', function($e) use ($r) {
                $r->out++;
                return $e->haltQueue();
            })
            ->attach('a', function($e) use ($r) {
                $r->out++;
            })
        ->trigger(new Event('a'));

        $this->assertEquals(1, $r->out);
    }

    public function testCanHaltQueueNow()
    {
        $r = new \StdClass;
        $r->out = 0;
        (new EventManager)
            ->attach('a', function($e) {
                return $e; // make sure the callback is triggered.
            })
            ->attach('a', function($e) {
                // halt the queue before the trigger's callback can be called for a second time.
                return $e->haltQueue(true);
            })
            ->trigger(new Event('a'), function ($event) use ($r) {
                $r->out++;
            });

        $this->assertEquals(1, $r->out);
    }

    public function testListenerCanListenToAllEvents()
    {
        $r = new \StdClass;
        $r->out = 0;
        $em = (new EventManager)->attach('.*', function($e) use ($r) {
            $r->out++;
        });
        $em->trigger(new Event('a'));
        $em->trigger(new Event('b'));
        $em->trigger(new Event('c'));

        $this->assertEquals(3, $r->out);
    }

    public function testNamespaces()
    {
        $r = new \StdClass;
        $r->out = 0;

        (new EventManager)->attach('.*', function($e) use ($r) {
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
        (new EventManager)->attach('do', function($e) use ($r) {
            $r->out = $e->get('hello');
        })
        ->trigger((new Event('do'))->set('hello', 'trq'));
        $this->assertEquals('trq', $r->out);
    }

    public function testListenerCanTriggerCallback()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)->attach('do', function($e) {
            return $e;
        })
        ->trigger(new Event('do'),
            function($response) use ($r) {
                $this->assertInstanceOf('Proem\Signal\EventInterface', $response);
                $r->out = 'Callback';
            }
        );

        $this->assertEquals('Callback', $r->out);
    }

    public function testCanReturnArbitrary()
    {
        $r = new \StdClass;
        $r->out = '';
        (new EventManager)->attach('do', function($e) {
            return new \StdClass;
        })
        ->trigger(new Event('do'), function($result) use ($r) { $r->result = $result; });

        $this->assertInstanceOf('\stdClass', $r->result);
    }
}
