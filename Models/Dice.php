<?php

class Dice
{
    public function roll() : int
    {
        return rand(1, 6);
    }
}