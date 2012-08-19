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

use Proem\Service\Manager\Template as Manager,
    Proem\Bootstrap\Signal\Event\Bootstrap,
    Proem\Service\Asset\Standard as Asset,
    Proem\Routing\Router\Standard as Router,
    Proem\Routing\Route\Standard as StandardRoute,
    Proem\Filter\Event\Generic as Event;

/**
 * The default "Route" filter event.
 */
class Route extends Event
{
    /**
     * Called prior to inBound.
     *
     * A listener responding with an object implementing the
     * Proem\Routing\Router\Template interface, will result in that
     * object being placed within the main service manager under
     * the index of *router*.
     *
     * @param Proem\Service\Manager\Template $assets
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.in.router
     */
    public function preIn(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger(
                (new Bootstrap('proem.pre.in.router'))->setServiceManager($assets),
                function($response) use ($assets) {
                    if ($response->provides('Proem\Routing\Router\Template')) {
                        $assets->set('router', $response);
                    }
                }
            );
        }
    }

    /**
     * Method to be called on the way into the filter.
     *
     * This method is responsible for setting up the default routes.
     *
     * @param Proem\Service\Manager\Template $assets
     */
    public function inBound(Manager $assets)
    {
        if (!$assets->provides('Proem\Routing\Router\Template')) {
            $asset = new Asset;
            $assets->set(
                'router',
                $asset->set('Proem\Routing\Router\Template', $asset->single(function() use ($assets) {
                    return new Router($assets->get('request'));
                }))
            );
        }
    }

    /**
     * Called after outBound.
     *
     * @param Proem\Service\Manager\Template $assets
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.in.router
     */
    public function postIn(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger((new Bootstrap('proem.post.in.router'))->setServiceManager($assets));
        }

        if ($assets->provides('router', 'Proem\Routing\Router\Template')) {
            $assets->get('router')->attach(
                'default-module-controller-action-params',
                new StandardRoute([
                    'rule' => '/:module/:controller/:action/:params'
                ])
            )
            ->attach(
                'default-module-controller-action-noparams',
                new StandardRoute([
                    'rule' => '/:module/:controller/:action'
                ])
            )
            ->attach(
                'default-module-controller-noaction',
                new StandardRoute([
                    'rule'      => '/:module/:controller',
                    'targets'    => ['action' => 'index']
                ])
            )
            ->attach(
                'default-nomodule-controller-action',
                new StandardRoute([
                    'rule'      => '/:controller/:action',
                    'targets'    => ['module' => 'index']
                ])
            )
            ->attach(
                'default-module-nocontroller',
                new StandardRoute([
                    'rule'      => '/:module',
                    'targets'    => ['controller' => 'index', 'action' => 'index']
                ])
            )
            ->attach(
                'default-nomodule-controller',
                new StandardRoute([
                    'rule'      => '/:controller',
                    'targets'    => ['module' => 'index', 'action' => 'index']
                ])
            )
            ->attach(
                'default-nomodule-nocontroller',
                new StandardRoute([
                    'rule'      => '/:action',
                    'targets'    => ['module' => 'index', 'controller' => 'index']
                ])
            )
            ->attach(
                'default-noparams',
                new StandardRoute([
                    'rule'      => '/',
                    'targets'    => ['module' => 'index', 'controller' => 'index', 'action' => 'index']
                ])
            );
        }
    }

    /**
     * Called prior to outBound.
     *
     * @param Proem\Service\Manager\Template $assets
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.in.router
     */
    public function preOut(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger((new Bootstrap('proem.pre.out.router'))->setServiceManager($assets));
        }
    }

    /**
     * Method to be called on the way out of the filter.
     *
     * @param Proem\Service\Manager\Template $assets
     */
    public function outBound(Manager $assets)
    {

    }

    /**
     * Called after outBound.
     *
     * @param Proem\Service\Manager\Template $assets
     * @triggers Proem\Bootstrap\Signal\Event\Bootstrap proem.pre.in.router
     */
    public function postOut(Manager $assets)
    {
        if ($assets->provides('events', 'Proem\Signal\Manager\Template')) {
            $assets->get('events')->trigger((new Bootstrap('proem.post.out.router'))->setServiceManager($assets));
        }
    }
}
