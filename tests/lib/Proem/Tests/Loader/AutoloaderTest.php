<?php

namespace Proem\Tests\Proem\Loader;

use Proem\Loader\Autoloader;
use Namespaced;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getData
     */
    public function testLoad($className, $testClassName, $message)
    {
        (new AutoLoader())
            ->registerNamespace('Namespaced', __DIR__ . '/Fixtures')
            ->registerPearPrefix('Pear_', __DIR__ . '/Fixtures')
            ->load($testClassName);

        $this->assertTrue(class_exists($className), $message);
    }

    /**
     * @dataProvider getData
     */
    public function testRegister($className, $testClassName, $message)
    {
        (new AutoLoader())
            ->registerNamespace('Namespaced', __DIR__ . '/Fixtures')
            ->registerPearPrefix('Pear_', __DIR__ . '/Fixtures')
            ->register();

        $this->assertTrue(class_exists($className), $message);
    }

    public function getData()
    {
        return [
            ['\Namespaced\Foo', 'Namespaced\Foo',   'Including Namespaced\Foo class'],
            ['\Pear_Foo',       'Pear_Foo',         'Including Pear_Foo class'],
            ['\Namespaced\Bar', '\Namespaced\Bar',  'Including \Namespaced\Bar class'],
            ['\Pear_Bar',       '\Pear_Bar',        'Including \Pear_Bar class']
        ];
    }

    public function testOverload()
    {
        (new AutoLoader())
            ->registerNamespace('Namespaced', [
                __DIR__ . '/Override',
                __DIR__ . '/Fixtures'
            ])
            ->load('Namespaced\Boo');

        $boo = new Namespaced\Boo;

        $this->assertEquals($boo->getMessage(), 'override', 'Including overriden Proem\Boo');
    }
}
