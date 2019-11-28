<?php

class Dice
{
    private $max_num;

    public function __construct(int $max_num = 6)
    {
        $this->max_num = $max_num;
    }

    public function roll() : int
    {
        return rand(1, $this->max_num);
    }
}