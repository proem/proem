<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2014 Tony R Quilkey <trq@proemframework.org>
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
 * @namespace Proem\Dispatch
 */
namespace Proem\Dispatch;

use \Symfony\Component\HttpFoundation\Request;
use Proem\Service\AssetManagerInterface;
use Proem\Routing\RouteManagerInterface;

/**
 * The default dispatcher.
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * Store the class mapping string
     */
    protected $mapping = "\\Module\\{:module}\\Controller\\{:controller}";

    /**
     * Store the asset manager.
     *
     * @var Proem\Service\AssetManagerInterface
     */
    protected $assetManager;

    /**
     * Store the router.
     *
     * @var Proem\Routing\RouteManagerInterface
     */
    protected $routeManager;

    /**
     * Store any failures
     *
     * @var array
     */
    protected $failures;

    /**
     * Setup the dispatcher
     */
    public function __construct(AssetManagerInterface $assetManager, RouteManagerInterface $routeManager)
    {
        $this->assetManager = $assetManager;
        $this->routeManager = $routeManager;
    }

    /**
     * Return any failed routes.
     *
     * @return array
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Handles a Request, converting it to a Response.
     *
     * @return Proem\Http\Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        while ($route = $this->router->route($request)) {
            $module     = $route->getPayload()['module'];
            $controller = $route->getPayload()['controller'];
            $action     = $route->getPayload()['action'];
            $class      = str_replace(
                ['{:module}', '{:controller}'],
                [$module, $controller],
                $this->mapping
            );
            try {
                return $this->assetManager->resolve($class, ['invoke' => $action]);
            } catch (\LogicException $e) {
                $this->failures[] = ['route' => $route, 'exception' => $e];
            }
        }
    }
}
