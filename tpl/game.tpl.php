<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/board.css">
	<link rel="stylesheet" type="text/css" href="lib/fontawesome/css/all.css">
</head>
<body>

<div class="top">
	DUPLICATE.FR - Play Scrabble at home !  
	<div class="toolbar">
		<i class="fas fa-gamepad fa-fw"></i>
		<i class="fas fa-user-circle fa-fw"></i>
	</div>
</div>

<div class="scrabble">
	<div class="board">board
	<?php
		$game->printBoard()
	?>
	</div>
	<div class="turn">turn
		<div class="currentdraw">
		<?php 
    		$game->printDraw();
    	?>
		</div>
	
		<div style="clear: both;">&nbsp;</div>
    	<div class="wordslist">wordslist
    	<?php 
        	$game->printWords();
    	?>
    	</div>
	</div>
	<div class="game">game
		<div class="score">score
			599
		</div>
		<div class="gameturns">gameturns
    	<?php 
    	   //printGameTurns($game);
    	?>
    	</div>
	</div>
</div>

</body>
</html>