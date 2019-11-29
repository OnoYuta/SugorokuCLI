<?php

class Game
{
    private static $instance;

    private $board;
    private $dice;
    private $players;

    const RESUME = "R";
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
        // 中断データの有無と中断データを再開する意思を確認
        if ($this->board->hasReadableFile($this->players) &&
            $this->getInputToResume() === self::RESUME) {
            echo "Resume this game with using saved data.\n";
            $this->board->loadSavedFile($this->players);
        } else {
            echo "Start a new game.\n";
            $this->board->setNewFile();
        };

        // メッセージを読ませるため一時停止
        sleep(1);

        while (!$this->board->isEndOfGame()) {

            // ダイスをふってプレイヤーを進める処理
            $this->board->continue($this->players, $this->dice);

            // 例：Taro,3,4(name,point,turn)
            // ターンごとにボードの状態をcsv出力
            $this->board->appendRecord($this->players);

            if (!$this->board->isEndOfGame() && 
                $this->getInputToPause() === self::PAUSE) {
                echo "This game has been paused.\n";
                exit;
            }
        }

        usleep(500000);
        echo $this->board->getWinnerInfo($this->players);;

    }

    public function getInputToPause() : string
    {
        echo "If you want to pause this game, enter 'P'.";
        return strtoupper(trim(fgets(STDIN)));
    }

    public function getInputToResume() : string
    {
        echo "If you want to Resume this game, enter 'R'.";
        return strtoupper(trim(fgets(STDIN)));
    }

}