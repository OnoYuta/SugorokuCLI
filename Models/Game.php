<?php

class Game
{
    private static $instance;

    private $board;
    private $dice;
    private $players;

    const PAUSE = "P";

    private function __construct(int $goal_point = 100)
    {
        define("GOAL_POINT", $goal_point);
    }

    public static function getInstance() : self
    {
        if (empty(self::$instance)) {
            self::$instance = new Game;
        }
        return self::$instance;
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
        $this->board->readSavedFile($this->players);

        while (!$this->board->isEndOfGame()) {

            $this->board->continue($this->players, $this->dice);

            // ターンごとに"プレイヤ名,現在地,ターン数"を出力する
            // 例：Taro,3,4
            $this->board->appendRecord($this->players);

            if ($this->board->isEndOfGame()) {
                break;
            } elseif ($this->getInput() === self::PAUSE) {
                echo "This game has been paused.\n";
                exit;
            }
        }

        $this->board->showWinners($this->players);

    }

    public function getInput() : string
    {
        echo "If you want to pause this game, enter 'P'.";
        return strtoupper(trim(fgets(STDIN)));
    }

}