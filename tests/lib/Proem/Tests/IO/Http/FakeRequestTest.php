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

use Proem\IO\Http\FakeRequest;

class FakeRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('\Proem\IO\Http\FakeRequest', new FakeRequest(null));
    }

    public function testMagicGet()
    {
        $this->assertInstanceOf('\Proem\Util\Storage\KeyValStore', (new FakeRequest(null))->get);
    }

    public function testParsedComponents()
    {
        $request = new FakeRequest('http://proemframework.org/foo/bar');
        $this->assertEquals('/foo/bar', $request->getRequestUri());
        $this->assertEquals('proemframework.org', $request->getHostName());
    }

    public function testCanSetGetParams()
    {
        $request = new FakeRequest(null);
        $request->param->set('foo', 'bar');
        $this->assertEquals('bar', $request->param->foo);
        $this->assertEquals('boo', $request->param->get('doesnotexist', 'boo'));
    }

    public function testCanManipulateMethodAndType()
    {
        $request = new FakeRequest(null);
        $this->assertEquals('GET', $request->getMethod());
        $request->setMethod('post');
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('application/x-www-form-urlencoded', $request->getContentType());
    }

    public function testCanRetrieveJson()
    {
        $request = new FakeRequest(null , 'PUT', '{"foo": "bar"}');
        $request->setContentType('json');
        $this->assertEquals('bar', $request->getBody(false)->foo);
    }
}
