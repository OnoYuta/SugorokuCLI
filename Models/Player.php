<?php

class Player
{
    private $name;
    private $point;

    public function __construct(string $name) 
    {
        $this->name = $name;
        $this->point = 0;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }
    
    public function setPoint(int $point) : void
    {
        $this->point = 0;
        $this->addPoint($point);
    }

    public function getName() : string
    {
        return $this->name;
    }
    
    public function getPoint() : int
    {
        return $this->point;
    }

    public function roll(Dice $dice) : void
    {
        $dice_num = $dice->roll();
        $this->addPoint($dice_num);
        echo $this->name . " rolled a dice and got " . $dice_num . ".\n";
        echo $this->name . " is in point " . $this->point . ".\n";
    }

    public function addPoint(int $num) : void
    {
        $this->point += $num;
        while ($this->point > GOAL_POINT) {
            $this->point = abs(GOAL_POINT - $this->getPointsLeft());
        }
    }

    public function getPointsLeft() : int
    {
        return abs(GOAL_POINT - $this->point);
    }

    public function getRecord() : array
    {
        return [$this->name, $this->point];
    }

}