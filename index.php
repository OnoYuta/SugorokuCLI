<?php
require 'Models/Game.php';
require 'Models/Board.php';
require 'Models/Dice.php';
require 'Models/Player.php';

$game = Game::getInstance();
$game->setBoard(new Board('data/board.csv'));
$game->setDice(new Dice());
$game->addPlayer(new Player('Taro'));
$game->addPlayer(new Player('Jiro'));
$game->start();