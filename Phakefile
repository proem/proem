<?php

group('dev', function() {

    desc('Run the unit tests');
    task('tests', function($args) {
        if (isset($args['verbose'])) {
            system('phpunit --colors --debug --verbose --configuration tests/phpunit.xml');
        } else {
            system('/usr/bin/phpunit --colors --configuration tests/phpunit.xml');
        }
    });

    desc('Build the Phar archive');
    task('build', 'tests', function($args) {
        chdir('lib');
        $phar = new Phar('proem.phar');
        $phar->buildFromDirectory('.');
        $phar->setStub("<?php
        Phar::mapPhar('proem.phar');
        require_once 'phar://proem.phar/Proem/Autoloader.php';
        (new Proem\Autoloader())->registerNamespaces(['Proem' => 'phar://proem.phar'])->register();
        __HALT_COMPILER();
        ?>");
        rename('proem.phar', '../build/proem.phar');
        chdir('../');
        if (isset($args['runtests'])) {
            system('phpunit --colors tests/phar-test.php');
        }
    });

    desc('Bump the version number');
    task('bump', function($args) {
        $file = file_get_contents('lib/Proem/Api/Proem.php');
        preg_match('/VERSION = \'([0-9]?)\.([0-9]?)\.([a-z0-9])\';/', $file, $matches);
        list($all, $major, $minor, $incr) = $matches;
        if (isset($args['major'])) {
            $major = (string) ++$major;
            $minor = '0';
            $incr = '0';
        } elseif (isset($args['minor'])) {
            $minor = (string) ++$minor;
            $incr = '0';
        } elseif (isset($args['incr'])) {
            if ($args['incr'] === 'true') {
                $incr = (string) ++$incr;
            } else {
                $incr = $args['incr'];
            }
        }
        $version = "$major.$minor.$incr";
        echo "VERSION = '$version'\n";
        if (isset($args['write'])) {
            $file = preg_replace('/VERSION = \'(.*)\';/', "VERSION = '$version';", $file);
            file_put_contents('lib/Proem/Api/Proem.php', $file);
        }
    });

    desc('Create a new class file');
    task('new-file', function($args) {
        if (!isset($args['path'])) {
            exit("path is a required argument");
        }
        $header = <<<EOD
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
 * @namespace Proem\
 */
namespace Proem\;

/**
 * Proem\
 *
 *
 */
class
{

}
EOD;

        file_put_contents($args['path'], $header);
    });

});
