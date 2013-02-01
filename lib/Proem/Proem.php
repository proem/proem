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

/**
 * @namespace Proem
 */
namespace Proem;

use Proem\Service\AssetManagerInterface;
use Proem\Service\AssetManager;
use Proem\Service\AssetComposer;
use Proem\Signal\EventInterface;
use Proem\Signal\Event;

/**
 * The Proem bootstrap wrapper
 *
 * Responsible for aiding in the bootstrap process.
 */
class Proem
{
    /**
     * Store the framework version.
     */
    const VERSION = '0.10.0-dev';

    /**
     * Store the asset manager.
     *
     * @var Proem\Asset\AssetManagerInterface
     */
    protected $assetManager = null;

    /**
     * Setup.
     */
    public function __construct(AssetManagerInterface $assetManager = null)
    {
        if ($assetManager === null) {
            $this->assetManager = new AssetManager;
        } else {
            $this->assetManager = $assetManager;
        }

        $this->assetManager->alias([
            'Proem\Signal\EventManagerInterface' => 'Proem\Signal\EventManager',
            'Proem\Signal\EventManager'          => 'eventManager',
            'Proem\Filter\ChainManagerInterface' => 'Proem\Filter\ChainManager',
            'Proem\Filter\ChainManager'          => 'chainManager'
        ]);
    }

    /**
     * Bootstrap the framework / application.
     *
     * @param Proem\Signal\EventInterface $initEvent An optional event to be triggered on init.
     */
    public function bootstrap(EventInterface $initEvent = null)
    {
        if ($initEvent === null) {
            $initEvent = new Event('proem.init');
        }

        $this->assetManager->resolve('eventManager')->trigger($initEvent);

        $this->assetManager->resolve('chainManager')
            ->attach($this->assetManager->resolve('Proem\Bootstrap\Request'))
            ->attach($this->assetManager->resolve('Proem\Bootstrap\Route'))
            ->attach($this->assetManager->resolve('Proem\Bootstrap\Dispatch'))
            ->bootstrap();

        return $this;
    }
}
