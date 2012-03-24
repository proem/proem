<?php

namespace MyApp;

require_once '/home/thorpe/src/proem/lib/Proem/Autoloader.php';

(new \Proem\Autoloader)
    ->registerNamespace('Proem', '/home/thorpe/src/proem/lib')
    ->registerNamespace('MyApp', 'lib')
    ->register();

(new \Proem\Proem)
    ->attachModule(new Module\Foo)
    ->init();
