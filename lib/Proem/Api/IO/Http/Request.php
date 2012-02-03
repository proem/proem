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
 * @namespace Proem\Api\IO\Http
 */
namespace Proem\Api\IO\Http;

use Proem\Util\Storage\KeyValStore;

/**
 * Proem\Api\IO\Http\Request
 */
class Request
{
    /**
     * Store data internally
     */
    protected $data = [];

    /**
     * Store the raw body of the request.
     */
    protected $body;

    /**
     * Instantiate the a from the super globals.
     */
    public function __construct(array $params = []) {
        $this->data = [
            'param'     => new KeyValStore($params),
            'get'       => new KeyValStore($_GET),
            'post'      => new KeyValStore($_POST),
            'cookie'    => new KeyValStore($_COOKIES),
            'file'      => new KeyValStore($_FILES),
            'meta'      => new KeyValStore($_SERVER),
            'header'    => new KeyValStore($this->formHeaders($_SERVER))
        ];
    }

    /**
     * Used to split the HTTP headers into there own store.
     *
     * @param array $meta
     */
    protected function formHeaders($meta)
    {
        $out = [];
        foreach ($meta as $k => $v) {
            if (substr($k, 0, 5) == 'HTTP_') {
                $out[substr($k, 5)] = $v;
            } elseif (in_array($k, ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE', 'X_REQUESTED_WITH'])) {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    /**
     * Retrieve data by index.
     *
     * @param string $index
     */
    public function __get($index)
    {
        return isset($this->data[$index]) ? $this->data[$index] : null;
    }

    /**
     * Retrieve the body of the request.
     *
     * @param bool $raw return body in it's raw format
     */
    public function getBody($raw = true)
    {
        if ($this->body) {
            $this->body = file_get_contents('php://input');
        }

        if (!$raw) {
            switch ($this->getContentType()) {
                case 'application/x-www-form-urlencode':
                    return mb_parse_str($this->body);
                    break;
                case 'application/json':
                    return json_decode($this->body);
                    break;
                case 'text/xml':
                    return simplexml_load_string($this->body);
                    break;
                default:
                    return $this->body;
            }
        }
        return $this->body;
    }

    /**
     * Retrieve request content type
     */
    public function getContentType()
    {
        return $this->data['header']->get('CONTENT_TYPE');
    }

    /**
     * Get request method.
     */
    public function getMethod()
    {
        return $this->data['meta']->get('REQUEST_METHOD');
    }

    /**
     * Is this a GET request?
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Is this a POST request?
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Is this a XmlHttpRequest request?
     */
    public function isAjax() {
        return $this->data['header']->get('X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    /**
     * Retrieve the request uri.
     */
    public function getRequestUri() {
        return $this->data['meta']->get('REQUEST_URI');
    }

    /**
     * Retrieve the host name
     */
    public function getHostName()
    {
        return $this->data['header']->get('HOST');
    }

    /**
     * Retrieve the clinet's ip address.
     */
    public function getClientIp()
    {
        if ($this->meta->has('HTTP_CLIENT_IP')) {
            return $this->data['meta']->get('HTTP_CLIENT_IP');
        }

        if ($this->data['meta']->has('HTTP_X_FORWARDED_FOR')) {
            $ip = explode(',', $this->data['meta']->get('HTTP_X_FORWARDED_FOR'), 2);
            return isset($ip[0]) ? $ip[0] : null;
        }

        return $this->data['meta']->get('REMOTE_ADDR');
    }
}
