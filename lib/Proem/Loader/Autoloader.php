<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2011 Tony R Quilkey <trq@proemframework.org>
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
 * @namespace Proem\Loader
 */
namespace Proem\Loader;

/**
 * Proem\
 *
 *
 */
class Autoloader
{
    private $namespaces = array();
    private $pearPrefixes = array();

    /**
     * Register an array of namespaces.
     *
     * @param array $namespaces An array of namespaces
     * @return Proem\Loader\Autoloader
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $path) {
            $this->registerNamespace($namespace, $path);
        }
        return $this;
    }

    /**
     * Register a namespace.
     *
     * @param string       $namespace The namespace
     * @param array|string $paths     The path to the namespace
     * @return Proem\Loader\Autoloader
     */
    public function registerNamespace($namespace, $path)
    {
        $this->namespaces[$namespace] = $path;
        return $this;
    }

    /**
     * Registers an array of classes using the Pear coding standard.
     *
     * @param array $classes
     * @return Proem\Loader\Autoloader
     */
    public function registerPearPrefixes(array $classes)
    {
        foreach ($classes as $prefix => $path) {
            $this->registerPearPrefix($prefix, $path);
        }
        return $this;
    }

    /**
     * Register a class using the PEAR naming convention.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The path
     * @return Proem\Loader\Autoloader
     */
    public function registerPearPrefix($prefix, $path)
    {
        $this->pearPrefixes[$prefix] = $path;
        return $this;
    }

    /**
     * Register the autoloader.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'load'), true);
    }

    /**
     * Load a class.
     *
     * @param string $class The name of the class
     */
    public function load($class)
    {
        if ($file = $this->locate($class)) {
            require_once $file;
        }
    }

    /**
     * Locate the path to the file where $class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|null The path, if found
     */
    public function locate($class)
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }

        if (false !== $pos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $pos);
            $className = substr($class, $pos + 1);
            $normalized = str_replace('\\', '/', $namespace) . '/' . str_replace('_', '/', $className) . '.php';
            foreach ($this->namespaces as $space => $path) {
                if (strpos($namespace, $space) !== 0) {
                    continue;
                }

                $file = $path . '/' . $normalized;

                if (is_file($file)) {
                    return $file;
                }
            }

        } else {
            $normalized = str_replace('_', '/', $class) . '.php';
            foreach ($this->pearPrefixes as $prefix => $path) {
                if (0 !== strpos($class, $prefix)) {
                    continue;
                }

                $file = $path . '/' . $normalized;
                if (is_file($file)) {
                    return $file;
                }
            }

        }
    }
}
