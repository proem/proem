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

namespace Proem\Test\Service;

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

    public function testCanStoreAndRetreive()
    {
        $asset = m::mock('\Proem\Service\Asset', ['stdClass']);
        $asset
            ->shouldReceive('is')
            ->once()
            ->andReturn('stdClass');
        $asset
            ->shouldReceive('fetch')
            ->once()
            ->andReturn(new \stdClass);

        $am = new AssetManager;
        $am->set('foo', $asset);

        $this->assertInstanceOf('\stdClass', $am->get('foo'));
    }

    public function testCanBuildComplexDependencies()
    {
        require_once __DIR__ . '/Fixtures/Transport.php';
        require_once __DIR__ . '/Fixtures/Mail.php';

        $transport = new Asset(
            'Transport',
            [
                'server'   => 'smtp.foo.com',
                'username' => 'username',
                'password' => 'password'
            ],
            function($asset) {
                return new \Transport($asset->server, $asset->username, $asset->password);
            }
        );

        $mail = new Asset('Mail', function($asset, $assetManager) {
            return new \Mail($assetManager->get('transport', true));
        });

        $assetManager = new AssetManager;
        $assetManager->set('transport', $transport);
        $assetManager->set('mail', $mail);

        $this->assertInstanceOf('\Transport', $assetManager->get('transport'));
        $this->assertInstanceOf('\Mail', $assetManager->get('mail'));
        $this->assertInstanceOf('\Transport', $assetManager->get('mail')->transport);

        $this->assertEquals('smtp.foo.com', $assetManager->get('mail')->transport->server);
        $this->assertEquals('username', $assetManager->get('mail')->transport->username);
        $this->assertEquals('password', $assetManager->get('mail')->transport->password);
    }
}
