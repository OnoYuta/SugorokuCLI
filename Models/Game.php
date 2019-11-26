<?php

class Game
{
    private $board;
    private $dice;
    private $players = [];
    private $num_of_turns;
    private $distance_to_goal;
    public const GOAL_POINT = 100;

    public static function getInstance() : self
    {
        echo "Game start!\n";
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
        $this->num_of_turns = 0;
        $this->distance_to_goal = self::GOAL_POINT;

        while ($this->distance_to_goal !== 0) {

            // csv出力用の文字列を格納
            $outputs = [];

            // ターン開始(ここでゲームを続けるか否か確認)
            $input = "";
            while ($input !== "y" && $input !== "n") {
                echo "Do you continue the game?\n[Yes:y][No:n]";
                $input = mb_strtolower(trim(fgets(STDIN)));
            }
            if ($input === "n") {
                echo "see you...\n";
                exit;
            }
            $this->num_of_turns++;

            // プレイヤが順番にサイコロを振り、出た数字だけ進む
            foreach ($this->players as $player) {
                $outputs[] = $player->roll($this->dice);
                // $outputs[] = $player->getTextToOutput();
            }
            $this->distance_to_goal = Player::getDistanceToGoal($this->players);
            $outputs[] = $this->distance_to_goal . " points from the top to the goal.";
            // ターンごとにプレイヤ名と現在地を出力する
            $this->board->outputArrayToCsv($this->num_of_turns, $outputs);
        }

        $winners = [];
        foreach ($this->players as $player) {
            if ($player->getPoint() === self::GOAL_POINT) {
                $winners[] = $player;
            }
        }
        if (count($winners) >= 2) {
            echo "Draw game.\n";
        } else {
            $name_of_winner = $winners[0]->getName();
            echo $name_of_winner . " win!!\n";
        }

    }

}