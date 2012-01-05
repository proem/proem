<?php

require_once 'lib/Proem/Api/Autoloader.php';

use Proem\Api\Autoloader;

$loader = new AutoLoader();
$loader->registerNamespaces([
    'Proem\Tests'   => 'tests/lib',
    'Proem'         => ['tests/lib/Proem/Tests/Fixtures', 'lib']
])->register();
