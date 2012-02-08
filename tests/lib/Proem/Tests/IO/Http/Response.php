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

namespace Proem\Tests\IO\Http;

use Proem\IO\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('\Proem\IO\Http\Response', new Response);
    }

    public function testCanSetGetVersion()
    {
        $r = new Response;
        $r->setHttpVersion(1.0);
        $this->assertEquals(1.0, $r->getHttpVersion());
    }

    public function testStatus()
    {
        $r = new Response;
        $this->assertEquals(200, $r->getHttpStatus());
        $this->assertEquals('OK', $r->getHttpStatus(true));
        $r->setHttpStatus(409);
        $this->assertEquals(409, $r->getHttpStatus());
        $this->assertEquals('Conflict', $r->getHttpStatus(true));
        $r->setHttpStatus('Gone');
        $this->assertEquals(410, $r->getHttpStatus());
        $this->assertEquals('Gone', $r->getHttpStatus(true));
    }

    public function testHeaders()
    {
        $r = new Request;
        $r->setHeader('foo', 'bar');
        $this->assertEquals('bar', $r->getHeader('foo'));
        $r->setHeaders['a' => 'b']);
        $this->assertEquals('b', $r->getHeader('a'));
    }

    public function testContentLength()
    {
        $r = new Request;
        $r->appendToBody('foo');
        $this->assertEquals(3, $r->getHeader('Content-Length'));
        $r->appendToBody('foo');
        $this->assertEquals(6, $r->getHeader('Content-Length'));
    }
}
