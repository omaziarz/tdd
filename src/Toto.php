<?php

namespace App;

class Toto
{
    private string $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function hello(): void
    {
        echo 'hello ' . $this->name;
    }
}
