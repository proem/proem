<?php

namespace Controller;

class Bar extends \Proem\Controller\Standard
{
    public function init()
    {
        $e = $this->assets->get('events');
        $e->attach([
            'name' => 'proem.pre.action.foo',
            'callback' => function() {
                echo 'pre';
            }
        ]);

        $e->attach([
            'name' => 'proem.post.action.foo',
            'callback' => function() {
                echo 'post';
            }
        ]);
    }

    public function fooAction()
    {
        echo 'action';
    }
}
