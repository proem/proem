<?php

group('dev', function() {

    desc('Run the unit tests');
    task('tests', function($args) {
        if (isset($args['verbose'])) {
            system('phpunit --colors --debug --verbose --configuration tests/phpunit.xml');
        } else {
            system('phpunit --colors --configuration tests/phpunit.xml');
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

});

task('default', 'dev:tests');
