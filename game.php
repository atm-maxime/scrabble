<?php

require 'class/scrabblegame.class.php';
require 'lib/scrabble.lib.php';

$game = new ScrabbleGame('fr');
// For now game is saved in a file, later I'll use a database
file_put_contents('games/scrabble.game', serialize($game)); 

include 'tpl/game.tpl.php';


?>