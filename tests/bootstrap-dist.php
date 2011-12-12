<?php

require_once 'lib/Proem/Loader/Autoloader.php';

use Proem\Loader\Autoloader;

$loader = new AutoLoader();
$loader->registerNamespaces([
    'Proem\\Tests'  => 'tests/lib',
    'Proem'         => 'lib'
])->register();
