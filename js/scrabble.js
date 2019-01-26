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
			$('#currentdraw div.boxcontent').html(data.currentdraw);
			$('#solutions div.boxcontent').html('...');
			$('#score div.boxcontent').html(data.anchors);
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
			$('#currentdraw div.boxcontent').html(data.currentdraw);
			$('#solutions div.boxcontent').html('...');
		});
	});
	
	$('#search_solutions').bind('click', function() {
		$.ajax({
			url: 'interface.php',
			dataType: 'json',
			data: {
				'action': 'list_solutions'
			}
		}).done(function(data) {
			//alert(data);
			$('#solutions div.boxcontent').html(data.solutions);
			
			$('i.word').bind('click', function() {
				$.ajax({
					url: 'interface.php',
					dataType: 'json',
					data: {
						'action': 'select_word',
						'iword': $(this).attr('iword')
					}
				}).done(function(data) {
					$('div.board').html(data.board);
					$('#currentdraw div.boxcontent').html(data.currentdraw);
					$('#score div.boxcontent').html(data.anchors);
				});
			});
		});
	});
	
	$('#check_word').bind('click', function() {
		$(this).css('border-color', '#000000');
		$.ajax({
			url: 'interface.php',
			dataType: 'json',
			data: {
				'action': 'check_word',
				'word': $('input[name="word"]').val()
			}
		}).done(function(data) {
			var color = '#FF0000';
			if(data.checkword) color = '#00FF00';
			$('input[name="word"]').css('border-color', color);
		});
	});
});