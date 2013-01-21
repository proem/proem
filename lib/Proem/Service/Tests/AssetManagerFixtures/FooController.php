<?php

class FooController
{
    public function hello()
    {
        return "Hello World";
    }

    public function goodbye(\ControllerDependency $dependency)
    {
        return $dependency->goodbye();
    }
}
