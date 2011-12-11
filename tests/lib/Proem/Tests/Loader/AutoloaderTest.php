<?php

namespace Proem\Tests\Proem\Loader;

use Proem\Loader\Autoloader;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     */
    public function testLoad($className, $testClassName, $message)
    {
        $loader = new AutoLoader();
        $loader->registerNamespace('Namespaced', __DIR__ . '/Fixtures');
        $loader->registerPearPrefix('Pear_', __DIR__ . '/Fixtures');
        $loader->load($testClassName);
        $this->assertTrue(class_exists($className), $message);
    }

    public function getData()
    {
        return [
            ['\\Namespaced\\Foo', 'Namespaced\\Foo',   'Including Namespaced\Foo class'],
            ['\\Pear_Foo',        'Pear_Foo',          'Including Pear_Foo class'],
            ['\\Namespaced\\Bar', '\\Namespaced\\Bar', 'Including \Namespaced\Bar class'],
            ['\\Pear_Bar',        '\\Pear_Bar',        'Including \Pear_Bar class']
        ];
    }

}
