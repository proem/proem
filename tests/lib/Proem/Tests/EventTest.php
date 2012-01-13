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
    Proem\Event\Base;

class EventTest extends \PHPUnit_Framework_TestCase
{
    private $event;

    public function setUp()
    {
        $this->event = new Event;
    }

    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Event', $this->event);
    }

    public function testCanExecute()
    {
        $this->event->attach([
            'name'      => 'do',
            'callback'  => function($e) {
                echo "Registered first\n";
                return "Hello";
            }
        ]);

        $this->event->attach([
            'name'      => 'do',
            'priority'  => 100,
            'callback'  => function($e) {
                var_dump($e);
                echo "Registered second\n";
            }
        ]);

        $this->event->trigger([
            'event' => new Base([
                'name'      => 'do',
                'params'    => ['a', 'b', 'c']
            ]),
            'callback'  => function($r) {
                echo "$r\n";
            }
        ]);
    }
}
