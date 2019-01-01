$(document).ready(function() {
	$('#new_game').bind('click', function() {
		$.ajax({
			url: 'interface.php',
			dataType: 'json',
			data: {
				'action': 'new_game'
			}
		}).done(function(data) {
			$('div.board').html(data.board);
			$('div.currentdraw').html(data.currentdraw);
			$('div.solutions').html('');
		});
	});
	
	$('#new_turn').bind('click', function() {
		$.ajax({
			url: 'interface.php',
			dataType: 'json',
			data: {
				'action': 'new_turn'
			}
		}).done(function(data) {
			$('div.currentdraw').html(data.currentdraw);
			$('div.solutions').html('');
		});
	});
	
	$('#search_solutions').bind('click', function() {
		$.ajax({
			url: 'interface.php',
			//dataType: 'json',
			data: {
				'action': 'list_solutions'
			}
		}).done(function(data) {
			alert(data);
			$('div.solutions').html(data);
			
			/*$('i.word').bind('click', function() {
				$.ajax({
					url: 'interface.php',
					dataType: 'json',
					data: {
						'action': 'select_word',
						'iword': $(this).attr('iword')
					}
				}).done(function(data) {
					$('div.board').html(data.board);
					$('div.currentdraw').html(data.currentdraw);
				});
			});*/
		});
	});
});