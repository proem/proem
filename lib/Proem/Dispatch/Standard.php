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
 * @namespace Proem\Dispatch
 */
namespace Proem\Dispatch;

use Proem\Dispatch\Template as Template;
use Proem\Service\Manager\Template as Manager;
use Proem\Routing\Route\Payload as Payload;
use Proem\Util\Storage\Queue;

/**
 * Proem\Dispatch\Standard
 */
class Standard implements Template
{
    /**
     * Priorities
     */
    const DEFAULT_CONTROLLERMAP_PRIORITY = 0;

    /**
     * Placeholders
     */
    const MODULE_PLACEHOLDER     = '{:module}';
    const CONTROLLER_PLACEHOLDER = '{:controller}';
    const ACTION_PLACEHOLDER     = '{:action}';

    /**
     * Store the Assets manager
     *
     * @var Proem\Service\Manager\Template
     */
    protected $assets;

    /**
     * Store an array of patterns used to searching
     * for classes within a namepspace.
     *
     * Controller maps are actually stored within a priority
     * queue with the default controller map sitting at priority 0.
     * If you want custom controller maps to be looked at before
     * the default controller map, give them a higher priority.
     *
     * @var Queue $controllerMaps
     */
    protected $controllerMaps;

    /**
     * Store the *action map* pattern.
     */
    protected $actionMap;

    /**
     * Store the absolute namespace to the current class
     *
     * @var string $class
     */
    protected $class;

    /**
     * Store the Router Payload.
     *
     * @var Proem\Routing\Route\Payload $payload
     */
    protected $payload;

    /**
     * Store the module name
     *
     * @var string $module
     */
    protected $module;

    /**
     * Store the controller name
     *
     * @var string $controller
     */
    protected $controller;

    /**
     * Store the action name
     *
     * @var string $action
     */
    protected $action;

    /**
     * Setup the dispatcher
     *
     * @param Proem\Service\Manager\Template $assets
     */
    public function __construct(Manager $assets)
    {
        $this->assets = $assets;
        $this->controllerMaps = new Queue;
        $this->controllerMaps->insert(
            'Module\\' . self::MODULE_PLACEHOLDER . '\Controller\\' . self::CONTROLLER_PLACEHOLDER,
            self::DEFAULT_CONTROLLERMAP_PRIORITY
        );
        $this->actionMap = self::ACTION_PLACEHOLDER . 'Action';
    }

    /**
     * Set the payload object
     *
     * @param Proem\Routing\Route\Payload $payload
     * @return Proem\Dispatch\Template
     */
    public function setPayload(Payload $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Set the current module
     *
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Get the current module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the current controller
     *
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get the current controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the current action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get the current action
     */
    public function getAction()
    {
        return str_replace(self::ACTION_PLACEHOLDER, strtolower($this->action), $this->actionMap);
    }

    /**
     * Add a new controller map onto the stack of controller
     * maps.
     *
     * This method allows us to add different directory structures
     * which the dispatcher can use to locate controllers.
     *
     * The default controller map looks like: 'Module\{:module}\Controller\{:controller}' where
     * {:module} and {:controller} are respectfully replaced by data provided by the payload.
     *
     * This controller map is injected with a priority of 0.
     *
     * If you want custom controller maps to be looked at before the default controller map,
     * give them a higher priority.
     *
     * You can create your own. The tokens :module and :controller will be replaced
     * with the module and controller that are made available via the payload.
     *
     * @param string $map
     * @param int $priority
     * @return Proem\Dispatch\Template
     */
    public function attachControllerMap($map, $priority = self::DEFAULT_CONTROLLERMAP_PRIORITY)
    {
        $this->controllerMaps->insert($map, $priority);
        return $this;
    }

    /**
     * Allows the customisation of the *action map*.
     *
     * The default implementation looks like {:action}Action
     *
     * {:action} gets replaced by the action returning from the
     * router.
     *
     * @param string $mapping
     */
    public function setActionMap($mapping)
    {
        $this->actionMap = $mapping;
        return $this;
    }

    /**
     * Test to see if the current payload is dispatchable.
     *
     * This method iterates through all of the controller maps
     * and checks to see if the class can be instantiated and the
     * action executed.
     *
     * This method will actually store an instantiated controller
     * within the $class property of this Dispatch object.
     *
     * @return bool
     */
    public function isDispatchable()
    {
        foreach (array_reverse($this->controllerMaps) as $map) {
            $this->class = str_replace(
                [self::MODULE_PLACEHOLDER, self::CONTROLLER_PLACEHOLDER],
                [$this->module, $this->controller],
                $map
            );

            try {
                $class = new \ReflectionClass($this->class);
                if ($class->implementsInterface('\Proem\Controller\Template')) {
                    $method = $class->getMethod($this->getAction());
                    if ($method->isPublic()) {
                        return true;
                    }
                }
            } catch (\ReflectionException $e) {}
        }

        return false;
    }

    /**
     * Dispatch the current controller stored within
     * the $class property.
     *
     * Prior to dispatch this method will send the Payload to the Request
     * object stored within the Service Manager. At this point we also call
     * the Payload's prepare method, which prepares the Payload for the Request.
     *
     * It will then execute the controllers preAction method, the action
     * method provided by the payload, then postAction.
     *
     * @return Proem\Dispatch\Standard
     */
    public function dispatch()
    {
        if ($this->assets->has('request')) {
            $this->assets->get('request')->injectPayload($this->payload->prepare());
        }

        $this->class = new $this->class($this->assets);
        $this->class->dispatch($this->getAction());
        return $this;
    }
}
