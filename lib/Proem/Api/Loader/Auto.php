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
 * @namespace Proem\Api\Loader
 */
namespace Proem\Api\Loader;

/**
 * Proem\Api\Loader\Auto
 */
class Auto
{
    private $namespaces     = [];
    private $pearPrefixes   = [];

    /**
     * Register an array of namespaces.
     *
     * @param array $namespaces An array of namespaces
     * @return Proem\Api\Autoloader
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace => $paths) {
            $this->registerNamespace($namespace, $paths);
        }
        return $this;
    }

    /**
     * Register a namespace.
     *
     * @param string       $namespace The namespace
     * @param array|string $paths     The path to the namespace
     * @return Proem\Api\Autoloader
     */
    public function registerNamespace($namespace, $paths)
    {
        $this->namespaces[$namespace] = (array) $paths;
        return $this;
    }

    /**
     * Registers an array of classes using the Pear coding standard.
     *
     * @param array $classes
     * @return Proem\Api\Autoloader
     */
    public function registerPearPrefixes(array $classes)
    {
        foreach ($classes as $prefix => $paths) {
            $this->registerPearPrefix($prefix, $paths);
        }
        return $this;
    }

    /**
     * Register a class using the PEAR naming convention.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The path
     * @return Proem\Api\Autoloader
     */
    public function registerPearPrefix($prefix, $paths)
    {
        $this->pearPrefixes[$prefix] = (array) $paths;
        return $this;
    }

    /**
     * Register the autoloader.
     */
    public function register()
    {
        spl_autoload_register([$this, 'load'], true);
        return $this;
    }

    /**
     * Load a class.
     *
     * @param string $class The name of the class
     */
    public function load($class)
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }

        if ($file = $this->locate($class)) {
            include_once $file;
        } else {
            if (substr($class, 0, 5) == 'Proem') {
                $api_class = str_replace('Proem\\', 'Proem\\Api\\', $class);
                if ($file = $this->locate($api_class)) {
                    include_once $file;
                    class_alias($api_class, $class);
                }
            }
        }
        return $this;
    }

    /**
     * Locate the path to the file where $class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|null The path, if found
     */
    private function locate($class)
    {
        if (false !== $pos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $pos);
            $className = substr($class, $pos + 1);
            $normalized = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
            foreach ($this->namespaces as $space => $paths) {
                if (strpos($namespace, $space) !== 0) {
                    continue;
                }

                foreach ($paths as $path) {
                    $file = $path . DIRECTORY_SEPARATOR . $normalized;

                    if (is_file($file)) {
                        return $file;
                    }
                }
            }

        } else {
            $normalized = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            foreach ($this->pearPrefixes as $prefix => $paths) {
                if (0 !== strpos($class, $prefix)) {
                    continue;
                }

                foreach ($paths as $path) {
                    $file = $path . DIRECTORY_SEPARATOR . $normalized;
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
    }
}
