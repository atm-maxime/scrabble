<?php

include_once 'class/scrabblegame.class.php';
include_once 'lib/scrabble.lib.php';

$action = $_REQUEST['action'];
$game = unserialize(file_get_contents('games/scrabble.game'));

$res = array();

if($action == 'new_game') {
    $game = new ScrabbleGame();
    $res['board'] = $game->getBoardHTML();
    $res['currentdraw'] = $game->getCurrentDrawHTML();
} else if($action == 'new_turn') {
    $game->newTurn();
    $res['currentdraw'] = $game->getCurrentDrawHTML();
} else if($action == 'list_solutions') {
    //$game->getAllCorrectWords();
    echo '<pre>';
    $game->getPossibleWords();
    $res['solutions'] = $game->getWordsHTML();
} else if($action == 'select_word') {
    $iWord = $_REQUEST['iword'];
    $game->selectWord($iWord);
    $res['board'] = $game->getBoardHTML();
    $res['currentdraw'] = $game->getCurrentDrawHTML();
}

file_put_contents('games/scrabble.game', serialize($game));

print json_encode($res);