<?php

class Board
{
    private $csv_path;

    public function __construct(string $csv_path)
    {
        $this->csv_path = $csv_path;
        $fp = fopen($this->csv_path, "c");
        fwrite($fp, "Game start!\n");
        fclose($fp);
    }

    public function show() {
        
    }

    public function outputArrayToCsv(int $num_of_turn,array $outputs) {
        
        $fp = fopen($this->csv_path, "a");

        // 区切り記号を出力
        fwrite($fp, str_repeat("*", 20) . "\n");

        // ターン数を出力
        fwrite($fp, "Start of turn " . $num_of_turn . "\n");
        
        // ダイスロールの結果を出力
        foreach ($outputs as $output) {
            echo $output . "\n";
            fwrite($fp, $output . "\n");
        }

        // 区切り記号を出力
        fwrite($fp, str_repeat("*", 20) . "\n");

        fclose($fp);
    }
}