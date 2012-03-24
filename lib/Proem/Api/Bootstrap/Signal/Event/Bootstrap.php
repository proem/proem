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
 * @namespace Proem\Api\Bootstrap\Signal\Event\Bootstrap
 */
namespace Proem\Api\Bootstrap\Signal\Event;

use Proem\Service\Manager;

/**
 * Proem\Api\Bootstrap\Signal\Event\Bootstrap
 *
 * A custom event used by the bootstrap triggered events.
 */
class Bootstrap extends \Proem\Signal\Event\Generic
{
    /**
     * Store the service manager
     */
    private $serviceManager;

    /**
     * Store the environment variable.
     */
    private $environment;

    /**
     * Set the service manager
     *
     * @param Proem\Api\Service\Manager $serviceManager
     */
    public function setServiceManager(Manager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Retrieve the service manager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set the environment
     *
     * @param string $env
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * Retrieve the environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

}
