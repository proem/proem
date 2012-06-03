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

use Proem\Autoloader,
    MyApp\Module\Foo;

class ExtTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        (new Autoloader)
            ->attachNamespace('MyApp', dirname(__FILE__) . '/Ext/Fixtures')
            ->register();
    }

    public function testFooModuleLoads()
    {
        $this->expectOutputString('Foo Module Loaded<h3>404 - Page Not Found</h3>');

        (new \Proem\Proem)
            ->attachModule(new Foo)
            ->init();
    }

    /**
     * The Foo module listens for the pre.in.route signal event,
     * Loading it now (at post.in.route) will never give it a chance
     * to set itself up.
     */
    public function testFooModuleWontLoadWhenAttachedTooLate()
    {
        $this->expectOutputString('<h3>404 - Page Not Found</h3>');

        (new \Proem\Proem)
            ->attachModule(new Foo, 'post.in.route')
            ->init();
    }

}
