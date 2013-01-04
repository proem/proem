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
 * @namespace Proem\Routing
 */
namespace Proem\Routing;

use Proem\Routing\RouteAbstract;
use Proem\Http\Request;

/**
 * Proem's standard route.
 */
class Route extends RouteAbstract
{
    /**
     * Store default tokens.
     *
     * @var array
     */
    protected $defaultTokens = [];

    /**
     * Store default filters.
     *
     * @var array
     */
    protected $defaultFilters = [];

    /**
     * Instantiate this route
     *
     * $options = ['targets', 'filters', 'method', 'callback'];
     *
     * @param string $rule
     * @param array $options
     */
    public function __construct($rule, array $options = [])
    {
        parent::__construct($rule, $options);

        $this->defaultFilters = [
            ':default'  => '[a-zA-Z0-9_\+\-%]+',
            ':gobble'   => '[a-zA-Z0-9_\+\-%\/]+',
            ':int'      => '[0-9]+',
            ':alpha'    => '[a-zA-Z]+',
            ':slug'     => '[a-zA-Z0-9_-]+'
        ];

        $this->defaultTokens = [
            'module'     => $this->defaultFilters[':default'],
            'controller' => $this->defaultFilters[':default'],
            'action'     => $this->defaultFilters[':default'],
            'params'     => $this->defaultFilters[':gobble']
        ];
    }

    /**
     * Process the supplied url.
     *
     * This route takes a simplified series of patterns such as :controller and
     * replaces them with more complex regular expressions which are then used
     * within a preg_match_callback to match against the given uri.
     *
     * If a 'filter' regex is set within the $options array that regex will be
     * used within the preg_match_callback. Otherwise a default regex of
     * ([a-zA-Z0-9_\+\-%]+) is used.
     *
     * If one of the 'simplified' patterns within the rule is :results, this is
     * treated specially and uses a ([a-zA-Z0-9_\+\-%\/]+) regex which will match
     * the same as the default as well as / .
     *
     * This causes the pattern to match entire sections of uri's. Allowing a
     * simple pattern like the default /:controller/:action/:results to match
     * uri's like /foo/bar/a/b/c/d/e/f/g/h and cause everything after /foo/bar
     * to be added to the Payload object as results (which are in turn transformed
     * into key => value pairs).
     *
     * TODO: A lot of this fluffing around could (and likely should) be moved into
     * the __construct and other protected internal methods.
     *
     * @param Proem\Http\Request $request
     */
    public function process(Request $request)
    {
        // Setup.
        $rule              = str_replace('/', '/?', $this->rule);
        $targets           = isset($this->options['targets']) ? $this->options['targets'] : [];
        $customFilters     = isset($this->options['filters']) ? $this->options['filters'] : [];

        $defaultTokens     = $this->defaultTokens;
        $defaultFilters    = $this->defaultFilters;
        $url               = $request->getRequestUri();

        $tokens            = [];
        $values            = [];
        $results           = [];

        // Build the main regular expression.
        $regex = '^' . preg_replace_callback(
            '@:[\w]+@',
            function ($matches) use ($customFilters, $defaultTokens, $defaultFilters) {
                $key = str_replace(':', '', $matches[0]);
                if (isset($customFilters[$key])) {
                    if (isset($defaultFilters[$customFilters[$key]])) {
                        return '(' . $defaultFilters[$customFilters[$key]] . ')';
                    } else {
                        if ($customFilters[$key]{0} == ':') {
                            throw new \RuntimeException(
                                "The custom filter named \"{$key}\" references a
                                non-existent builtin filter named \"{$customFilters[$key]}\"."
                            );
                        } else {
                            return '(' . $customFilters[$key] . ')';
                        }
                    }
                } elseif (isset($defaultTokens[$key])) {
                    return '(' . $defaultTokens[$key] . ')';
                } else {
                    return '(' . $defaultFilters[':default'] . ')';
                }
            },
            $rule
        ) . '/?$';

        // Find all tokens.
        preg_match_all('@:([\w]+)@', $rule, $tokens, PREG_PATTERN_ORDER);
        $tokens = $tokens[0];

        // Test the main regular expression against the url.
        if (preg_match('@^' . $regex . '$@', $url, $values)) {

            // Discard *all* matches index.
            array_shift($values);

            // Match tokens to values
            foreach ($tokens as $index => $value) {
                if (isset($values[$index])) {
                    $results[substr($value, 1)] = urldecode($values[$index]);
                }
            }

            // Replace any results with specific targets..
            foreach ($targets as $key => $value) {
                $results[$key] = $value;
            }

            // If the current $key is "params" & the string within $value looks
            // like a / seperated string, parse it into an associative array.
            if (isset($results['params']) && strpos($results['params'], '/') !== false) {
                preg_match_all("/([^\/]+)\/([^\/]+)/", $results['params'], $m);
                $results = array_merge($results, array_combine($m[1], $m[2]));
                unset($results['params']);
            }

            // Route has matched, return results.
            return $results;
        }

        return false;
    }
}
