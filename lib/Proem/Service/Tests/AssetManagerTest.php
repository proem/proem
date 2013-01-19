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

namespace Proem\Service\Tests;

use \Mockery as m;
use Proem\Service\AssetManager;
use Proem\Service\Asset;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAssetManager()
    {
        $am = new AssetManager;
        $this->assertInstanceOf('Proem\Service\AssetManagerInterface', $am);
    }

    public function testCanAttachAndRetreiveClosure()
    {
        $am = new AssetManager;
        $am->attach('foo', function() { return new \stdClass; });

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
    }

    public function testCanAttachAndRetreiveInstance()
    {
        $am = new AssetManager;
        $am->attach('foo', new \stdClass);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
    }

    public function testCanStoreAndRetreiveAssetObject()
    {
        $asset = m::mock('\Proem\Service\Asset', ['stdClass']);
        $asset
            ->shouldReceive('__invoke')
            ->once()
            ->andReturn(new \stdClass);

        $am = new AssetManager;
        $am->attach('foo', $asset);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
    }

    public function testCanAttachAndRetreiveClosureSingleton()
    {
        $am = new AssetManager;
        $am->attach('foo', function() { return new \stdClass; }, true);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
        $this->assertSame($am->resolve('foo'), $am->resolve('foo'));
    }

    public function testInstanceIsSingleton()
    {
        $am = new AssetManager;
        $am->attach('foo', new \stdClass);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
        $this->assertSame($am->resolve('foo'), $am->resolve('foo'));
    }

    public function testCanAlias()
    {
        $am = new AssetManager;
        $am->alias('stdClass', 'foo');
        $am->attach('foo', new \stdClass);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
        $this->assertInstanceOf('\stdClass', $am->resolve('stdClass'));
    }

    public function testCanAliasOnAttach()
    {
        $am = new AssetManager;
        $am->attach(['foo' => 'stdClass'], new \stdClass);

        $this->assertInstanceOf('\stdClass', $am->resolve('foo'));
        $this->assertInstanceOf('\stdClass', $am->resolve('stdClass'));
    }

    public function testCanAutoResolveSimple()
    {
        require_once __DIR__ . '/AssetManagerFixtures/Bar.php';
        $am = new AssetManager;

        $this->assertInstanceOf('Bar', $am->resolve('Bar'));
    }

    public function testCanAutoResolveWithDeps()
    {
        require_once __DIR__ . '/AssetManagerFixtures/Foo.php';
        require_once __DIR__ . '/AssetManagerFixtures/Bar.php';
        $am = new AssetManager;

        $this->assertInstanceOf('Foo', $am->resolve('Foo'));
        $this->assertInstanceOf('Bar', $am->resolve('Foo')->getBar());
    }

    public function testCanAutoResolveAliased()
    {
        require_once __DIR__ . '/AssetManagerFixtures/Bar.php';
        $am = new AssetManager;
        $am->alias('Bar', 'thisisbar');

        $this->assertInstanceOf('Bar', $am->resolve('thisisbar'));
    }

    public function testCanAutoResolveAliasedDependency()
    {
        require_once __DIR__ . '/AssetManagerFixtures/Foo.php';
        require_once __DIR__ . '/AssetManagerFixtures/Bar2.php';
        $am = new AssetManager;
        $am->alias('Bar2', 'Bar');

        $this->assertInstanceOf('Bar2', $am->resolve('Foo')->getBar());
    }

    public function testCanAutoResolveWithDependencyRequiringInterface()
    {
        require_once __DIR__ . '/AssetManagerFixtures/NeedsInterface.php';
        require_once __DIR__ . '/AssetManagerFixtures/SomeInterface.php';
        require_once __DIR__ . '/AssetManagerFixtures/Some.php';

        $am = new AssetManager;
        $am->alias('Some', 'SomeInterface');

        $this->assertInstanceOf('NeedsInterface', $am->resolve('NeedsInterface'));
    }

    public function testCanRemapAnAlias()
    {
        require_once __DIR__ . '/AssetManagerFixtures/NeedsInterface.php';
        require_once __DIR__ . '/AssetManagerFixtures/SomeInterface.php';
        require_once __DIR__ . '/AssetManagerFixtures/Some.php';
        require_once __DIR__ . '/AssetManagerFixtures/Someother.php';

        $am = new AssetManager;
        $am->alias('Some', 'SomeInterface');

        $am->attach('Some', function() { return new \Someother; });

        $this->assertInstanceOf('NeedsInterface', $am->resolve('NeedsInterface'));
        $this->assertEquals('someother', $am->resolve('NeedsInterface')->getBar()->doSomething());
    }

    public function testCanBuildComplexServices()
    {
        require_once __DIR__ . '/AssetManagerFixtures/Transport.php';
        require_once __DIR__ . '/AssetManagerFixtures/Mail.php';

        $transport = new Asset(
            'Transport',
            [
                'server'   => 'smtp.foo.com',
                'username' => 'username',
                'password' => 'password'
            ],
            function($asset) {
                return new \Transport($asset->get('server'), $asset->get('username'), $asset->get('password'));
            }
        );

        $mail = new Asset('Mail', function($asset, $assetManager) {
            return new \Mail($assetManager->resolve('transport'));
        });

        $assetManager = new AssetManager;
        $assetManager->attach('transport', $transport);
        $assetManager->attach('mail', $mail);

        $this->assertInstanceOf('\Transport', $assetManager->resolve('transport'));
        $this->assertInstanceOf('\Mail', $assetManager->resolve('mail'));
        $this->assertInstanceOf('\Transport', $assetManager->resolve('mail')->transport);

        $this->assertEquals('smtp.foo.com', $assetManager->resolve('mail')->transport->server);
        $this->assertEquals('username', $assetManager->resolve('mail')->transport->username);
        $this->assertEquals('password', $assetManager->resolve('mail')->transport->password);
    }
}
