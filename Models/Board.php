<?php

class Board
{
    private $path;
    private $file;
    private $num_of_turn;
    private $points_left;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->file = new SplFileObject($this->path, 'c+');
        $this->file->setFlags(
            SplFileObject::READ_CSV | 
            SplFileObject::SKIP_EMPTY | 
            SplFileObject::READ_AHEAD
        );
    }

    /**
     * ゲーム開始時に前回の対戦記録があれば読み込む
     *
     * @return void
     */
    public function readSavedFile(array $players) : void
    {

        $rows = [];
        foreach ($this->file as $row) {
            $rows[] = $row;
        }

        if ($this->validateFile($players, $rows)) {

            echo "Read saved data successfully.\n";

            $num_of_players = count($players);
            for ($i = 0; $i < $num_of_players; $i++) {
                list($name, $point, $num_of_turn) = $rows[count($rows) - $num_of_players + $i];
                $players[$i]->setName($name);
                $players[$i]->setPoint($point);
                $this->num_of_turn = $num_of_turn;
                echo $name . " is in point " . $point . ".\n";
            }

            echo "On the " . $this->num_of_turn . " turn.\n";

        } else {
            // ファイルを削除してリスタート
            echo "Saved data is corrupted.\nStart a new game.\n";
            $this->file = new SplFileObject($this->path, 'w+');
        }
        
    }

    private function validateFile(array $players, array $rows) : bool
    {
        $saved_num_of_turns = [];
        $saved_player_names = [];

        $num_of_players = count($players);

        for ($i = 0; $i < $num_of_players; $i++) {
            list($name, $point, $num_of_turn) = $rows[count($rows) - $num_of_players + $i];

            // nameは、1文字以上の文字列
            if (strlen($name) === 0) {
                return false;
            }

            // pointは、1桁以上の半角数字で、GOAL_POINTより小さい
            if (!preg_match("/[0-9]+/", $point) || $point >= GOAL_POINT) {
                return false;
            }

            // turnは、1桁以上の半角数字
            if (!preg_match("/[0-9]+/", $num_of_turn)) {
                return false;
            } 

            $saved_num_of_turns[] = $num_of_turn;
            $saved_player_names[] = $name;
        }

        // プレイヤーの名前と数が一致
        foreach ($players as $player) {
            if (!in_array($player->getName(), $saved_player_names)) {
                return false;
            }
        }

        // プレイヤー数が一致
        if ($num_of_players !== count($saved_player_names)) {
            return false;
        }

        // 全プレイヤーのターン数が同一
        if (count(array_unique($saved_num_of_turns)) !== 1) {
            return false;
        }

        return true;
    }

    public function isEndOfGame() : bool
    {
        return $this->points_left === 0;
    }

    public function continue(array $players, Dice $dice) : void
    {
        $this->num_of_turn++;

        $all_points_left = [];
        foreach ($players as $player) {
            $player->roll($dice);
            $all_points_left[] = $player->getPointsLeft();
        }
        echo "Turn " . $this->num_of_turn . " is over.\n";
        $this->points_left = min($all_points_left);
    }

    public function appendRecord(array $players) : void
    {
        foreach ($players as $player) {
            $record = $player->getRecord();
            $record[] = $this->num_of_turn;
            $this->file->fputcsv($record);
        }
    }

    public function showWinners(array $players) : void
    {
        $winners = [];
        foreach ($players as $player) {
            if ($player->getPoint() === GOAL_POINT) {
                $winners[] = $player;
            }
        }

        if (count($winners) >= 2) {
            echo "Draw game.\n";
        } else {
            $name_of_winner = $winners[0]->getName();
            echo $name_of_winner . " wins!!\n";
        }
    }
}