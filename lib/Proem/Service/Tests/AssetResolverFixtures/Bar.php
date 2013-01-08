<?php

namespace ResolverFixtures;

class Bar implements BarInterface
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
