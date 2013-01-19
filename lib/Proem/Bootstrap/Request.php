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
use Proem\Service\AssetComposer;

/**
 * The default "Request" filter chain event.
 */
class Request extends ChainEventAbstract
{
    /**
     * Called on the way *in* to the filter chain.
     *
     * First triggers a *proem.in.request* event. This event allows a client to
     * attach a custom Proem\Http\Request asset to the Asset Manager.
     *
     * If no such asset has been attached, this method will then go ahead and attach
     * a default Proem\Http\Request.
     *
     * @param Proem\Service\AssetManagerInterface $assetManager
     * @triggers proem.in.request
     */
    public function in(AssetManagerInterface $assetManager)
    {
        $assetManager->resolve('eventManager')->trigger(
            new Event('proem.in.request'),
            function ($responseEvent) use ($assetManager) {
                if ($responseEvent->has('requestAsset')) {
                    $assetManager->attach('request', $responseEvent->get('requestAsset'));
                }
            }
        );
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
