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

namespace Proem\Tests\Util;

use Proem\Tests\Util\Options\Fixtures\OptionsFixture,
    Proem\Tests\Util\Options\Fixtures\OptionsFixture2,
    Proem\Tests\Util\Options\Fixtures\OptionsFixture3,
    Proem\Proem,
    Proem\Service\Asset\Standard as GenericAsset,
    Proem\Service\Manager\Standard as ServiceManager;

class OptTest extends \PHPUnit_Framework_TestCase
{
    public function testValidOptions()
    {
        $fixture = new OptionsFixture([
            'something' => 'something',
            'bar'       => 'this is bar',
            'boo'       => ['key' => 'value'],
            'bob'       => new Proem
        ]);
        $this->assertInstanceOf('Proem\Tests\Util\Options\Fixtures\OptionsFixture', $fixture);
        $this->assertEquals($fixture->getSomething(), 'something');
        $this->assertEquals($fixture->getFoo(), 'foo');
        $this->assertEquals($fixture->getBar(), 'this is bar');
        $this->assertArrayHasKey('key', $fixture->getBoo());
        $this->assertInstanceOf('Proem\Proem', $fixture->getBob());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBar()
    {
        $fixture = new OptionsFixture([
            'boo' => [],
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBoo()
    {
        $fixture = new OptionsFixture([
            'bar' => 'this is bar',
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException DomainException
     */
    public function testCustomExcpetion()
    {
        $fixture = new OptionsFixture2([
            'foo' => '',
            'except' => 'something'
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBob()
    {
        $fixture = new OptionsFixture([
            'boo' => [],
            'bar' => 'this is bar'
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidBoo()
    {
        $fixture = new OptionsFixture([
            'boo' => false,
            'bob' => new Proem
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidBob()
    {
        $fixture = new OptionsFixture([
            'boo' => [],
            'bob' => new \StdClass
        ]);
    }

    public function testValidAsset()
    {
        $fixture = new OptionsFixture([
            'boo'   => [],
            'bar'   => 'this is bar',
            'bob'   => new Proem,
            'asset' => (new GenericAsset())
                ->set('StdClass', function() {
                    return new \StdClass;
                })
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidAsset()
    {
        $fixture = new OptionsFixture([
            'boo'   => [],
            'asset' => new \StdClass
        ]);
    }

    public function testValidServiceManager()
    {
        $asset = new GenericAsset;
        $asset->set('StdClass', function() {
            return new \StdClass;
        });

        $proem = new GenericAsset;
        $proem->set('Proem', function() {
            return new Proem;
        });

        $man = new ServiceManager;
        $man->set('StdClass', $asset)
            ->set('Proem', $proem);

        $fixture = new OptionsFixture([
            'boo'   => [],
            'bar'   => 'this is bar',
            'bob'   => new Proem,
            'asset' => $man,
            'am'    => $man
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidServiceManager()
    {
        $fixture = new OptionsFixture([
            'boo'   => [],
            'asset' => new \StdClass
        ]);
    }

    public function testUnless()
    {
        $fixture = new OptionsFixture2([
            'bar' => 'bar!',
            'obj' => 'ProemFixture'
        ]);

        $this->assertEquals($fixture->getBar(), 'bar!');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidObject()
    {
        $fixture = new OptionsFixture2([
            'emptytest' => false
        ]);
    }

    public function testCustomValidatorPass()
    {
        $fixture = new OptionsFixture2([
            'foo' => 100,
            'custom-arg' => "hello"
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCustomValidatorFail()
    {
        $fixture = new OptionsFixture2([
            'foo' => 100,
            'custom-arg' => 100
        ]);
    }

    /**
     * The fowllowing tests test none Option values.
     *
     * This is, functions which don't use Option objects to define there defaults.
     * This in turn allows validation to be skipped all together.
     */

    public function testDefaultArgs()
    {
        $fixture = new OptionsFixture3;
        $this->assertEquals($fixture->getFoo(), 'this is foo');
        $this->assertEquals($fixture->getBar(), 'this is bar');
    }

    public function testCanOverrideDefaultArgs()
    {
        $fixture = new OptionsFixture3([
            'foo' => 'foo',
            'bar' => 'bar'
        ]);
        $this->assertEquals($fixture->getFoo(), 'foo');
        $this->assertEquals($fixture->getBar(), 'bar');
    }
}
