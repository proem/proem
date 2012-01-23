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
    Proem\Bootstrap\Signal\Event\Bootstrap;

/**
 * Proem\Api\Bootstrap\Filter\Event\Response
 *
 * The default "Response" filter event.
 */
class Response extends \Proem\Filter\Event\Generic
{
    /**
     * preIn
     *
     * Called prior to inBound
     */
    public function preIn(Manager $assets)
    {
        if ($assets->provides('events', '\Proem\Signal\Manager')) {
            $assets->get('events')->trigger([
                'name'      => 'pre.in.response',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) {},
            ]);
        }
    }

    /**
     * inBound
     *
     * Method to be called on the way into the chain.
     */
    public function inBound(Manager $assets)
    {

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
                'name'      => 'post.in.response',
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
                'name'      => 'pre.out.response',
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
     * Method to be called on the way out of the chain.
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
                'name'      => 'post.out.response',
                'params'    => [],
                'target'    => $this,
                'method'    => __FUNCTION__,
                'event'     => (new Bootstrap())->setServiceManager($assets),
                'callback'  => function($e) {},
            ]);
        }
    }
}
