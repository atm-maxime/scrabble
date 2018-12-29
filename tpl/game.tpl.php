<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/board.css">
	<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/all.css">
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/scrabble.js"></script>
</head>
<body>

<div class="top">
	DUPLICATE.FR - Le scrabble duplicate à la maison !
	<div class="toolbar">
		<i class="fas fa-gamepad fa-fw"></i>
		<i class="fas fa-user-circle fa-fw"></i>
	</div>
</div>

<div class="scrabble">
	<div class="board">
	<?php
		$game->printBoard()
	?>
	</div>
	<div class="turn">
		<div class="boxtitle">
			Tirage
			<div class="toolbar">
        		<i id="new_turn" class="fas fa-chevron-circle-right fa-fw"></i>
        	</div>
		</div>
		<div class="currentdraw">
		<?php 
    		$game->printDraw();
    	?>
		</div>
		<div class="boxtitle">
			Mots possibles
			<div class="toolbar">
        		<i id="search_wordlist" class="fas fa-search fa-fw"></i>
        	</div>
		</div>
    	<div class="wordslist">
    	<?php 
        	$game->printWords();
    	?>
    	</div>
	</div>
	<div class="game">
		<div class="boxtitle">
			Partie en cours
			<div class="toolbar">
        		<i id="score" class="fas fa-trophy fa-fw"></i>
        	</div>
		</div>
		<div class="score">
			Score : 000
		</div>
		<div class="boxtitle">
			Coups joués
			<div class="toolbar">
        		<i id="game_turns" class="fas fa-history fa-fw"></i>
        	</div>
		</div>
		<div class="gameturns">
    	<?php 
    	   $game->printGameTurns();
    	?>
    	</div>
	</div>
</div>

</body>
</html>