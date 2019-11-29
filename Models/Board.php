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
    }

    public function hasReadableFile(array $players) : bool
    {
        // セーブデータが存在するか確認
        if ($this->hasFile()) {
            echo "Saved data exists.\n";
            $this->file = new SplFileObject($this->path, 'c+');
            $this->file->setFlags(
                SplFileObject::READ_CSV | 
                SplFileObject::SKIP_EMPTY | 
                SplFileObject::READ_AHEAD
            );
        } else {
            $this->setNewFile();
            return false;
        };

        // セーブデータが形式に則っているか確認
        if (!$this->validateFile(($players))) {
            echo "But inappropriate as data to continue .\n";
            $this->setNewFile();
            return false;
        }

        // セーブデータの中身を表示
        foreach ($this->getFileContents($players) as $content) {
            echo $content;
        }

        return true;
    }

    // セーブデータのボード情報を取得
    public function getFileContents(array $players) : array
    {

        $rows = [];
        foreach ($this->file as $row) {
            $rows[] = $row;
        }

        $num_of_players = count($players);
        $num_of_turn = 0;
        $contents = [];
        for ($i = 0; $i < $num_of_players; $i++) {
            list($name, $point, $num_of_turn) = $rows[count($rows) - $num_of_players + $i];
            $contents[] = $name . " is in point " . $point . ".\n";
        }
        array_unshift($contents, $this->getDelimiter($num_of_turn));
        $contents[] = str_repeat("*", 34) . "\n";
        return $contents;
        
    }

    // セーブデータ内のプレイヤーやボードの情報を現在のゲームにセットする
    public function loadSavedFile(array $players) : void
    {

        $rows = [];
        foreach ($this->file as $row) {
            $rows[] = $row;
        }

        $num_of_players = count($players);
        for ($i = 0; $i < $num_of_players; $i++) {
            list($name, $point, $num_of_turn) = $rows[count($rows) - $num_of_players + $i];
            $players[$i]->setName($name);
            $players[$i]->setPoint($point);
            $this->num_of_turn = $num_of_turn;
        }
                
    }

    // セーブデータが形式に則っているか確認する
    private function validateFile(array $players) : bool
    {
        $num_of_players = count($players);
        $saved_num_of_turns = [];
        $saved_player_names = [];

        $rows = [];
        foreach ($this->file as $row) {
            $rows[] = $row;
        }

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
        echo $this->getDelimiter();

        $all_points_left = [];
        foreach ($players as $player) {
            $player->roll($dice);
            $all_points_left[] = $player->getPointsLeft();
        }
        $this->points_left = min($all_points_left);

        echo str_repeat("*", 33) . "\n";
    }

    public function appendRecord(array $players) : void
    {
        foreach ($players as $player) {
            $record = $player->getRecord();
            $record[] = $this->num_of_turn;
            $this->file->fputcsv($record);
        }
    }

    public function getWinnerInfo(array $players) : string
    {
        $winners = [];
        foreach ($players as $player) {
            if ($player->getPoint() === GOAL_POINT) {
                $winners[] = $player;
            }
        }
        if (count($winners) >= 2) {
            return "Draw game.\n";
        } else {
            $name_of_winner = $winners[0]->getName();
            return $name_of_winner . " wins!!\n";
        }
    }

    public function hasFile() : bool
    {
        $info = new SplFileInfo($this->path);
        if ($info->isFile()) {
            return true;
        } else {
            return false;
        }
    }

    // セーブデータを読み書きするための準備
    public function setFile() : void
    {
            $this->file = new SplFileObject($this->path, 'c+');
            $this->file->setFlags(
                SplFileObject::READ_CSV | 
                SplFileObject::SKIP_EMPTY | 
                SplFileObject::READ_AHEAD
            );
    }

    // セーブデータを新たに作成する準備
    public function setNewFile() : void
    {
        $this->file = new SplFileObject($this->path, 'w+');
    }

    // ターン数と区切り記号を出力する際に文字数を揃えるための処理
    public function getDelimiter(int $num_of_turn = null) : string
    {
        $num_of_turn = $num_of_turn ? $num_of_turn : $this->num_of_turn;
        $num_of_digits = strlen($this->num_of_turn);
        $result = str_repeat("*", 9);
        $result .= " On the ". $num_of_turn . " turn ";
        $result .= str_repeat("*", 10 - $num_of_digits) . "\n";
        return $result;
    }

}