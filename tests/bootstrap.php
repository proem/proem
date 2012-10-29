<?php

require_once 'lib/Proem/Util/Autoloader.php';

use Proem\Util\Autoloader;

$loader = new AutoLoader();
$loader->attachNamespaces([
    'Proem\Tests'   => 'tests/lib',
    'Proem'         => ['tests/lib/Proem/Tests/Fixtures', 'lib']
])->register();
