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
 * @namespace Proem\Bootstrap\Filter\Event
 */
namespace Proem\Bootstrap\Filter\Event;

use Proem\Service\Manager\Template as Manager;
use Proem\Service\Asset\Standard as Asset;
use Proem\Bootstrap\Signal\Event\Bootstrap;
use Proem\Filter\Event\Generic as Event;
use Proem\Dispatch\Standard as DispatchStandard;
use Proem\Dispatch\Stage as DispatchStage;

/**
 * The default "Dispatch" filter event.
 */
class Dispatch extends Event
{
    /**
     * Called prior to inBound.
     *
     * A listener responding with an event containing a DI container holding an
     * implemention of the Proem\Dispatch\Template interface, will result in
     * that implementation being placed within the main service manager under
     * the index of *dispatch*.
     *
     * @see Proem\Dispatch\Template
     * @param Proem\Service\Manager\Template
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.in.dispatch
     */
    public function preIn(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger(
                (new Bootstrap('proem.pre.in.dispatch'))->setServiceManager($assets),
                function ($response) use ($assets) {
                    if (
                        $response->has('dispatch.asset') &&
                        $response->getParam('dispatch.asset')->provides('Proem\Dispatch\Template')
                    ) {
                        $assets->set('dispatch', $response->getParam('dispatch.asset'));
                    }
                }
            );
        }
    }

    /**
     * Method to be called on the way into the filter.
     *
     * If not already provided, this method will add a default
     * Proem\Dispatch\Template implementation to the main service
     * manager under the index of *dispatch*.
     *
     * @see Proem\Dispatch\Template
     * @param Proem\Service\Manager\Template
     */
    public function inBound(Manager $assets)
    {
        if (!$assets->provides('Proem\Dispatch\Template')) {
            $asset = new Asset;
            $assets->set(
                'dispatch',
                $asset->set(
                    'Proem\Dispatch\Template',
                    $asset->single(
                        function () use ($assets) {
                            return new DispatchStandard($assets);
                        }
                    )
                )
            );
        }
    }

    /**
     * Called after outBound.
     *
     * This method is reponsible for executing the DispatchStage process.
     *
     * @see Proem\Dispatch\Stage
     * @param Proem\Service\Manager\Template
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.post.in.dispatch
     */
    public function postIn(Manager $assets)
    {
        $skipDispatch       = false;
        $dispatchStageAsset = null;

        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger(
                (new Bootstrap('proem.post.in.dispatch'))->setServiceManager($assets),
                function ($response) use (&$skipDispatch) {
                    if ($response->has('skip.dispatch') && $response->getParam('skip.dispatch')) {
                        $skipDispatch = true;
                    }
                    if ($response->has('stage.asset')) {
                        $dispatchStageAsset = $response->get('stage.asset');
                    }
                }
            );

            if (!$skipDispatch) {
                if ($dispatchStageAsset !== null) {
                    $dispatchStageAsset->setParam('assets', $assets);
                    $dispatchStageAsset->get();
                } else {
                    (new DispatchStage($assets));
                }
            }
        }
    }

    /**
     * Called prior to outBound.
     *
     * @param Proem\Service\Manager\Template
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.out.dispatch
     */
    public function preOut(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger((new Bootstrap('proem.pre.out.dispatch'))->setServiceManager($assets));
        }
    }

    /**
     * Method to be called on the way out of the filter.
     *
     * @param Proem\Service\Manager\Template
     */
    public function outBound(Manager $assets)
    {

    }

    /**
     * Called after outBound.
     *
     * @param Proem\Service\Manager\Template
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.post.out.dispatch
     */
    public function postOut(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger((new Bootstrap('proem.post.out.dispatch'))->setServiceManager($assets));
        }
    }
}
