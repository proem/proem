<?php

class Bar
{
    protected $v = 0;

    public function doSomething()
    {
        return "bar";
    }

    public function a() {
        $this->v++;
    }

    public function b() {
        $this->v++;
    }

    public function c() {
        $this->v++;
    }

    public function d(ADependency $dep) {
        $this->v += $dep->get(100);
    }

    public function getV() {
        return $this->v;
    }

    public function hello()
    {
        return "Hello World";
    }

    public function goodbye(ADependency $dep) {
        return $dep->goodbye();
    }
}
