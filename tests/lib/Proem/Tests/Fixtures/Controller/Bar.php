<?php

namespace Controller;

class Bar extends \Proem\Controller\Standard
{
    public function init()
    {
        $e = $this->assets->get('events');
        $e->attach('proem.pre.action.fooAction', function() {
            echo 'pre';
        });

        $e->attach('proem.post.action.fooAction', function() {
            echo 'post';
        });
    }

    public function fooAction()
    {
        echo 'action';
    }
}
