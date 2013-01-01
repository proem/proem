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
 * @namespace Proem\Bootstrap
 */
namespace Proem\Bootstrap;

use Proem\Service\AssetManagerInterface;
use Proem\Service\AssetInterface;
use Proem\Filter\ChainEventAbstract;
use Proem\Signal\Event;

/**
 * The default "Dispatch" filter chain event.
 */
class Dispatch extends ChainEventAbstract
{
    /**
     * Called on the way *in* to the filter chain.
     *
     * @param Proem\Service\AssetManagerInterface $assetManager
     * @triggers proem.in.dispatch
     */
    public function in(AssetManagerInterface $assetManager)
    {
        if ($assetManager->provides('eventManager', 'Proem\Signal\EventManagerInterface')) {
            $assetManager->get('eventManager')->trigger(
                new Event('proem.in.dispatch'),
                function ($responseEvent) use ($assetManager) {
                    // Check for a customized Dispatch\Dispatcher.
                    if (
                        $responseEvent->has('dispatcherAsset') &&
                        $responseEvent->get('dispatcherAsset') instanceof AssetInterface &&
                        $responseEvent->get('dispatcherAsset')->provides('Proem\Dispatch\DispatcherInterface')
                    ) {
                        $assetManager->set('dispatcher', $responseEvent->get('dispatcherAsset'));
                    }

                    // Check for a customized Dispatch\Staging
                    if (
                        $responseEvent->has('stageAsset') &&
                        $responseEvent->get('stageAsset') instanceof AssetInterface &&
                        $responseEvent->get('stageAsset')->provides('Proem\Dispatch\StageInterface')
                    ) {
                        $assetManager->set('stage', $responseEvent->get('stageAsset'));
                    }
                }
            );
        }

        if (!$assetManager->provides('Proem\Dispatch\Dispatcher')) {
            $assetManager->set('dispatcher', (new AssetComposer('Proem\Dispatch\Dispatcher'))->compose(true));
        }

        if (!$assetManager->provides('Proem\Dispatch\Stage')) {
            (new Stage($assetManager))->process();
        } else {
            /**
             * TODO: This call should likely be replaced by some method that resolves
             * to return an asset by type not by index. eg; getProvided().
             *
             * It could in fact replace this entire if statement if it can also resolve
             * to create assets that don't exist within the asset manager.
             */
            $assetManager->get('stage')->process();
        }
    }

    /**
     * Called on the way *out* of the filter chain.
     *
     * @param Proem\Service\AssetManagerInterface $assetManager
     */
    public function out(AssetManagerInterface $assets)
    {
        // Does nothing.
    }
}
