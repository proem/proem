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

namespace Proem\Util\Loader\Tests;

use Proem\Util\Loader\Autoloader;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     */
    public function testLoad($className, $testClassName, $message)
    {
        (new AutoLoader())
            ->attachNamespace('PSR', __DIR__ . '/AutoloaderFixtures')
            ->attachPearPrefix('PEAR_', __DIR__ . '/AutoloaderFixtures')
            ->load($testClassName);

        $this->assertTrue(class_exists($className), $message);
    }

    /**
     * @dataProvider getData
     */
    public function testRegister($className, $testClassName, $message)
    {
        (new AutoLoader())
            ->attachNamespace('PSR', __DIR__ . '/AutoloaderFixtures')
            ->attachPearPrefix('PEAR_', __DIR__ . '/AutloaderFixtures')
            ->register();

        $this->assertTrue(class_exists($className), $message);
    }

    public function getData()
    {
        return [
            ['\PSR\Foo',    'PSR\Foo',      'Including PSR\Foo class'],
            ['\PEAR_Foo',   'PEAR_Foo',     'Including PEAR_Foo class'],
            ['\PSR\Bar',    '\PSR\Bar',     'Including \PSR\Bar class'],
            ['\PEAR_Bar',   '\PEAR_Bar',    'Including \PEAR_Bar class']
        ];
    }
}
