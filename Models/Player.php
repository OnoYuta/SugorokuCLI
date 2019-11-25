<?php

class Player
{
    private $name;
    private $position;

    public function __construct(string $name) 
    {
        $this->name = $name;
        $this->position = 0;
    }
    
    public function show() : void
    {
        echo "$this->name:$this->position\n";
    }
}