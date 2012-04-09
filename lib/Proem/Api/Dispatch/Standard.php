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
 * @namespace Proem\Api\Dispatch
 */
namespace Proem\Api\Dispatch;

use Proem\Dispatch\Template as Template,
    Proem\Service\Manager\Template as Manager,
    Proem\Routing\Route\Payload as Payload;

/**
 * Proem\Dispatch\Standard
 */
class Standard implements Template
{
    /**
     * Store the Assets manager
     *
     * @var Proem\Service\Manager\Template
     */
    protected $assets;

    /**
     * Store an array of patterns used to searching
     * for classes within a namepspace.
     */
    protected $controllerMaps = [];

    /**
     * Store the absolute namespace to the current class
     */
    protected $class;

    /**
     * Store the Router Payload.
     *
     * @var Proem\Routing\Route\Payload
     */
    protected $payload;

    /**
     * Store the module name from the payload
     */
    protected $module;

    /**
     * Store the controller name from the payload
     */
    protected $controller;

    /**
     * Store the action name from the payload
     */
    protected $action;

    public function __construct(Manager $assets)
    {
        $this->assets = $assets;
        $this->controllerMaps = ['Module\:module\Controller\:controller'];
    }

    public function setPayload(Payload $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    public function registerControllerMap($map) {
        $this->controllerMaps[] = $map;
    }

    public function isDispatchable()
    {
        $this->module     = $this->payload->has('module')           ? ucfirst(strtolower($this->payload->get('module')))      : '';
        $this->controller = $this->payload->has('controller')       ? ucfirst(strtolower($this->payload->get('controller')))  : '';
        $this->action     = $this->payload->has('action')           ? $this->payload->get('action') : '';

        foreach ($this->controllerMaps as $map) {
            $this->class = str_replace(
                [':module', ':controller'],
                [$this->module, $this->controller],
                $map
            );

            if (class_exists($this->class)) {
                $this->class = new $this->class($this->assets);
                if ($this->class instanceof \Proem\Controller\Template) {
                    if (method_exists($this->class, $this->action)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function dispatch()
    {
        if ($this->assets->has('request') && $this->payload->get('params') && is_array($this->payload->get('params'))) {
            $this->assets->get('request')
                ->setGetData($this->payload->get('params'));
        }
        $this->class->preAction();
        $this->class->{$this->action}();
        $this->class->postAction();
        return $this;
    }
}
