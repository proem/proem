<?php

namespace ResolverFixtures;

class SomeBar implements BarInterface
{
    protected $data;
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
