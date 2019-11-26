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

    public function getName() : string
    {
        return $this->name;
    }
    
    public function getPoint() : int
    {
        return $this->point;
    }

    public function roll(Dice $dice) : string
    {
        $dice_num = $dice->roll();
        echo "$this->name rolled a dice and got $dice_num.\n";
        $this->point += $dice_num;
        if ($this->point > Game::GOAL_POINT) {
            $this->point = Game::GOAL_POINT - abs(Game::GOAL_POINT - $this->point);
        }
        return $this->name . " is in point " . $this->point . ".";;
    }

    public static function getDistanceToGoal(array $players) : int
    {
        $distances_to_goal = [];

        foreach ($players as $player) {
            $distances_to_goal[] = abs(Game::GOAL_POINT - $player->point);
        }
        $distance_from_top_to_goal = min($distances_to_goal);
        
        return $distance_from_top_to_goal;
    }

}