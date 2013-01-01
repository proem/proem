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
 * @namespace Proem\Dispatch
 */
namespace Proem\Dispatch;

use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpFoundation\Request;
use Proem\Service\AssetManagerInterface;

/**
 * Interface all dispatchers must implement.
 */
interface DispatcherInterface extends HttpKernelInterface
{
    /**
     * Setup the dispatcher
     */
    public function __construct(AssetManagerInterface $assetManager);

    /**
     * Set the current payload data.
     */
    public function setPayload(array $payload = []);

    /**
     * Test to see if the current payload is dispatchable.
     *
     * @return bool
     */
    public function isDispatchable();

    /**
     * Handles a Request, converting it to a Response.
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true);
}
