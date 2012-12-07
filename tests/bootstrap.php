<?php

require_once 'lib/Proem/Util/Loader/Autoloader.php';

( new Proem\Util\Loader\AutoLoader())
    ->attachNamespace('Proem\Tests', 'tests/lib')
    ->register();
