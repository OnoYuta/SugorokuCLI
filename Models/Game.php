<?php

class Game
{
    private $board;
    private $dice;
    private $players = [];

    public function getInstance() : self
    {
        return new Game();
    }

    public function setBoard(Board $board) : void
    {
        $this->board = $board;
    }

    public function setDice(Dice $dice) : void
    {
        $this->dice = $dice;
    }

    public function addPlayer(Player $player) : void
    {
        $this->players[] = $player;
    }

    public function start()
    {
        $text = trim(fgets(STDIN));
        $this->board->write($text);
        foreach ($this->players as $player) {
            $player->show();
        }
    }

}