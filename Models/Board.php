<?php

class Board
{
    private $csv_path;

    public function __construct(string $csv_path)
    {
        $this->csv_path = $csv_path;
        $fp = fopen($this->csv_path, "c");
        fwrite($fp, "ゲームスタート!!\n");
        fclose($fp);
    }

    public function write(string $text) {
        $fp = fopen($this->csv_path, "a");
        fwrite($fp, "$text\n");
        fclose($fp);
    }
}