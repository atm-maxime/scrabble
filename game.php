<?php

require 'class/scrabblegame.class.php';
require 'lib/scrabble.lib.php';

$game = new ScrabbleGame('fr');

$game->newTurn();

include 'tpl/game.tpl.php';

/*echo '<div class="left">';
echo '<div class="board">';

echo '</div>';
echo '</div>';

echo '<div class="right">';
echo '<div class="game">';
echo '<div class="currentdraw">';

echo '</div>';

echo '';


echo '</div>';
echo '</div>';*/

?>