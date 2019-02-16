<?php

set_time_limit(60);
include_once 'class/scrabblegame.class.php';

$game = new ScrabbleGame();

// DICT TEST
$game->dict->createDict();

// TURN TEST
/*
$game->newTurn();

$game->board->setLetter($game->currentDraw[0], "3,3");
$game->board->setLetter($game->currentDraw[1], "3,2");
$game->board->setLetter($game->currentDraw[2], "3,1");

echo '<pre>';
$game->getPossibleWords();
*/

/*echo convert(memory_get_usage()).'<br>';

$tab = array();
for ($i = 0; $i < 100000; $i++) {
    $tab[] = array($i);
}

echo convert(memory_get_usage()).'<br>';

for ($i = 0; $i < 10000; $i++) {
    unset($tab[$i]);
    $tab[$i] = &$tab[50000];
}

echo convert(memory_get_usage()).'<br>';*/



function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
?>