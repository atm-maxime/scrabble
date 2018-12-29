<?php

require 'class/scrabblegame.class.php';
require 'lib/scrabble.lib.php';

$game = new ScrabbleGame('fr');
file_put_contents('games/scrabble.game', serialize($game));

//$game->newTurn();

include 'tpl/game.tpl.php';


?>