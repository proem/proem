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


/**
 * @namespace Proem\Tests\Util
 */
namespace Proem\Tests\Util;

use Proem\Util\Process\Callback;

/**
 * Proem\Tests\Util\ProcessTest
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleAnonFunction()
    {
        $this->assertTrue((new Callback(function() { return true; }))->call());
    }

    public function testCanPassSingleArg()
    {
        $this->assertTrue((new Callback(function($var) { return $var; }, true))->call());
    }

    public function testCanPassMultipleArgs()
    {
        $this->assertEquals((new Callback(function($var1, $var2) { return count(func_get_args()); }, [1, 1]))->call(), 2);
    }

    public function someCallback()
    {
        return true;
    }

    public function testSomeCallbackWithinObject()
    {
        $this->assertTrue((new Callback([$this, 'someCallback']))->call());
    }

    public static function someStaticCallback()
    {
        return true;
    }

    public function testStaticCallback()
    {
        $this->assertTrue((new Callback(['Proem\Tests\Util\ProcessTest', 'someStaticCallback']))->call());
    }
}
