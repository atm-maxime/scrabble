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
		<i id="new_game" class="fas fa-gamepad fa-fw action" title="New game"></i>
		<i class="fas fa-user-circle fa-fw"></i>
	</div>
</div>

<div class="scrabble">
	<div class="board">
	<?php
		print $game->getBoardHTML()
	?>
	</div>
	<div class="turn">
		<div id="currentdraw" class="box">
    		<div class="boxtitle">
    			Tirage
    			<div class="toolbar">
            		<i id="new_turn" class="fas fa-chevron-circle-right fa-fw action" title="New turn"></i>
            	</div>
    		</div>
    		<div class="boxcontent">
    		<?php 
        		print $game->getCurrentDrawHTML();
        	?>
    		</div>
		</div>
		<div id="solutions" class="box">
    		<div class="boxtitle">
    			Mots possibles
    			<div class="toolbar">
            		<i id="search_solutions" class="fas fa-search fa-fw action" title="Search"></i>
            	</div>
    		</div>
        	<div class="boxcontent">
        		...
        	</div>
    	</div>
	</div>
	<div class="game">
		<div id="score" class="box">
    		<div class="boxtitle">
    			Partie en cours
    			<div class="toolbar">
            		<i id="score" class="fas fa-trophy fa-fw"></i>
            	</div>
    		</div>
    		<div class="boxcontent">
    			Score : 000
    		</div>
		</div>
		<div id="gameturns" class="box">
    		<div class="boxtitle">
    			Coups joués
    			<div class="toolbar">
            		<i id="game_turns" class="fas fa-history fa-fw"></i>
            	</div>
    		</div>
    		<div class="boxcontent">
        		...
        	</div>
    	</div>
	</div>
	<div class="tools">
		<div id="checkword" class="box">
    		<div class="boxtitle">
    			Vérification d'un mot
    			<div class="toolbar">
            		<i id="check_word" class="fas fa-check-circle fa-fw action"></i>
            	</div>
    		</div>
    		<div class="boxcontent">
    			<input type="text" name="word" />
    		</div>
		</div>
	</div>
</div>

</body>
</html>