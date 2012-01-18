<?php

require_once 'lib/Proem/Api/Loader/Auto.php';

use Proem\Api\Loader\Auto as Autoloader;

$loader = new AutoLoader();
$loader->registerNamespaces([
    'Proem\Tests'   => 'tests/lib',
    'Proem'         => ['tests/lib/Proem/Tests/Fixtures', 'lib']
])->register();
