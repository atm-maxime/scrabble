<?php

include_once 'class/scrabblegame.class.php';

$action = $_REQUEST['action'];
$game = unserialize(file_get_contents('games/scrabble.game'));

if($action == 'new_turn') {
    $game->draw();
    $game->printDraw();
} else if($action == 'list_solutions') {
    $game->getAllCorrectWords();
    $game->printWords();
} else if($action == 'select_word') {
    $iWord = $_REQUEST['iword'];
    $game->selectWord($iWord);
}

file_put_contents('games/scrabble.game', serialize($game));