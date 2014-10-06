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
     * Store custom filters.
     *
     * @var array
     */
    protected $customFilters = [];

    /**
     * Store payload data.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * Instantiate this route
     *
     * $options = ['targets', 'filters', 'method', 'callback'];
     *
     * @param string $rule
     * @param array $options
     */
    public function __construct($rule, $options = null, $callback = null)
    {
        parent::__construct($rule, $options, $callback);

        if (isset($this->options['filters'])) {
            $this->customFilters = $this->options['filters'];
        }

        $this->defaultFilters = [
            '{default}' => '[a-zA-Z0-9_\+\-%]+',
            '{gobble}'  => '[a-zA-Z0-9_\+\-%\/]+',
            '{int}'     => '[0-9]+',
            '{alpha}'   => '[a-zA-Z]+',
            '{slug}'    => '[a-zA-Z0-9_-]+'
        ];

        $this->defaultTokens = [
            'module'     => $this->defaultFilters['{default}'],
            'controller' => $this->defaultFilters['{default}'],
            'action'     => $this->defaultFilters['{default}'],
            'params'     => $this->defaultFilters['{gobble}']
        ];
    }

    /**
     * Build a regular expression from the given rule.
     *
     * @param string $rule
     */
    protected function compileRegex($rule)
    {
        $regex = '^' . preg_replace_callback(
            '@\{[\w]+\??\}@',
            function ($matches) {
                $optional = false;
                $key = str_replace(['{', '}'], '', $matches[0]);
                if (substr($key, -1) == '?') {
                    // Flag this token as optional.
                    $optional = true;
                }
                if (isset($this->customFilters[$key])) {
                    if (isset($this->defaultFilters[$this->customFilters[$key]])) {
                        return '(' . $this->defaultFilters[$this->customFilters[$key]] . ')' . (($optional) ? '?' : '');
                    } else {
                        if (
                            substr($this->customFilters[$key], 0, 1) == '{' &&
                            substr($this->customFilters[$key], -1) == '}'
                        ) {
                            throw new \RuntimeException(
                                "The custom filter named \"{$key}\" references a
                                non-existent builtin filter named \"{$this->customFilters[$key]}\"."
                            );
                        } else {
                            return '(' . $this->customFilters[$key] . ')' . (($optional) ? '?' : '');
                        }
                    }
                } elseif (isset($this->defaultTokens[$key])) {
                    return '(' . $this->defaultTokens[$key] . ')' . (($optional) ? '?' : '');
                } else {
                    return '(' . $this->defaultFilters['{default}'] . ')' . (($optional) ? '?' : '');
                }
            },
            $rule
        ) . '$';

        // Fix slash delimeters in regards to optional handling.
        $regex = str_replace(['?/', '??'], ['?/?', '?'], $regex);

        return $regex;
    }

    /**
     * Find tokens within given rule.
     *
     * @param string $rule
     */
    protected function compileTokens($rule)
    {
        $tokens = [];
        preg_match_all('@\{([\w]+)\??\}@', $rule, $tokens, PREG_PATTERN_ORDER);
        return $tokens[0];
    }

    /**
     * Match a regular expression against a given *haystack* string. Return the resulting matches
     * indexed by the values of the given tokens.
     *
     * @param string $regex
     * @param array $tokens
     * @param string $haystack
     */
    protected function compileResults($regex, $tokens, $haystack)
    {
        $values  = [];
        $results = [];

        // Test the regular expression against the supplied *haystack* string.
        if (preg_match('@^' . $regex . '$@', $haystack, $values)) {

            // Discard *all* matches index.
            array_shift($values);

            // Match tokens to values found by the regex.
            foreach ($tokens as $index => $value) {
                if (isset($values[$index])) {
                    $results[str_replace(['{', '}'], '', $value)] = urldecode($values[$index]);
                }
            }

            // If the current $key is "params" & the string within $value looks
            // like a / seperated string, parse it into an associative array.
            if (isset($results['params']) && strpos($results['params'], '/') !== false) {
                preg_match_all("/([^\/]+)\/([^\/]+)/", $results['params'], $m);
                $results = array_merge($results, array_combine($m[1], $m[2]));
                unset($results['params']);
            }

            return $results;
        }

        return false;
    }

    /**
     * Retrieve payload data
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Process this route.
     *
     * Takes a simplified series of patterns such as {controller} and
     * replaces them with more complex regular expressions which are then used
     * to match against a given *haystack* string.
     *
     * @param Proem\Http\Request $request
     * @return false|array False on fail to match, otherwise array of results.
     */
    public function process(Request $request)
    {
        $results = [];

        // Test hostname rule.
        if (isset($this->options['hostname'])) {
            $regex           = $this->compileRegex($this->options['hostname']);
            $tokens          = $this->compileTokens($this->options['hostname']);
            $hostnameResults = $this->compileResults($regex, $tokens, $request->getHttpHost());

            if ($hostnameResults === false) {
                return false;
            } else {
                $results = array_merge($results, $hostnameResults);
            }
        }

        // Test the main url rule.
        $regex      = $this->compileRegex($this->rule);
        $tokens     = $this->compileTokens($this->rule);
        $urlResults = $this->compileResults($regex, $tokens, $request->getRequestUri());

        if ($urlResults === false) {
            return false;
        } else {
            $results = array_merge($results, $urlResults);
        }

        if (isset($this->options['targets'])) {
            $results = array_merge($results, $this->options['targets']);
        }

        $this->payload = $results;
        return true;
    }
}
