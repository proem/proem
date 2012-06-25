<?php

require_once 'lib/Proem/Autoloader.php';

use Proem\Autoloader;

$loader = new AutoLoader();
$loader->attachNamespaces([
    'Proem\Tests'   => 'tests/lib',
    'Proem'         => ['tests/lib/Proem/Tests/Fixtures', 'lib']
])->register();
