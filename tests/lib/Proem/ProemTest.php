<?php

require_once 'PHPUnit/Autoload.php';
require_once 'lib/Proem/Proem.php';

class Proem_ProemTest extends PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('\Proem\Proem', new Proem\Proem);
    }
}
