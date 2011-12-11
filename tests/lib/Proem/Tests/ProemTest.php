<?php

namespace Proem\Tests;

use Proem\Proem;

class ProemTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiate()
    {
        $this->assertInstanceOf('Proem\Proem', new Proem);
    }
}
