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
 * @namespace Proem\Api\Bootstrap\Filter\Event
 */
namespace Proem\Api\Bootstrap\Filter\Event;

use Proem\Service\Manager,
    Proem\Bootstrap\Signal\Event\Bootstrap,
    Proem\IO\Http\Request as HTTPRequest,
    Proem\Service\Asset\Generic as Asset;

/**
 * Proem\Api\Bootstrap\Filter\Event\Request
 *
 * The default "Request" filter event.
 */
class Request extends \Proem\Filter\Event\Generic
{
    /**
     * preIn
     *
     * Called prior to inBound
     *
     * The preIn Filter event will trigger a pre.in.request Signal.
     *
     * If this Signal returns a Proem\IO\Http\Request object load it into the Asset Manager.
     */
    public function preIn(Manager $assets)
    {
        if ($assets->provides('events', '\Proem\Signal\Manager')) {
            $assets->get('events')->trigger([
                'name'      => 'pre.in.request',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) use ($assets) {
                    if ($e->provides('Proem\IO\Http\Request')) {
                        $assets->set('request', $e);
                    }
                },
            ]);
        }
    }

    /**
     * inBound
     *
     * Method to be called on the way into the filter.
     *
     * Checks to see if we already have an Asset providing Proem\IO\Http\Request, if not, we provide one.
     */
    public function inBound(Manager $assets)
    {
        if (!$assets->provides('Proem\IO\Http\Request')) {
            $asset = new Asset;
            $assets->set(
                'request',
                $asset->set('Proem\IO\Http\Request', $asset->single(function() {
                    return new HTTPRequest;
                }))
            );
        }
    }

    /**
     * postIn
     *
     * Called after outBound
     */
    public function postIn(Manager $assets)
    {
        if ($assets->provides('events', '\Proem\Signal\Manager')) {
            $assets->get('events')->trigger([
                'name'      => 'post.in.request',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) {},
            ]);
        }
    }

    /**
     * preOut
     *
     * Called prior to outBound
     */
    public function preOut(Manager $assets)
    {
        if ($assets->provides('events', '\Proem\Signal\Manager')) {
            $assets->get('events')->trigger([
                'name'      => 'pre.out.request',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) {},
            ]);
        }
    }

    /**
     * outBound
     *
     * Method to be called on the way out of the filter.
     */
    public function outBound(Manager $assets)
    {

    }

    /**
     * postOut
     *
     * Called after outBound
     */
    public function postOut(Manager $assets)
    {
        if ($assets->provides('events', '\Proem\Signal\Manager')) {
            $assets->get('events')->trigger([
                'name'      => 'post.out.request',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) {},
            ]);
        }
    }
}
