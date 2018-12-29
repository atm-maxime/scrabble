$(document).ready(function() {
	$('#new_turn').bind('click', function() {
		$.ajax({
			url: 'interface.php', 
			data: {
				'action': 'new_turn'
			}
		}).done(function(data) {
			$('div.currentdraw').html(data);
		});
	});
	
	$('#search_wordlist').bind('click', function() {
		$.ajax({
			url: 'interface.php', 
			data: {
				'action': 'list_solutions'
			}
		}).done(function(data) {
			$('div.wordslist').html(data);
		});
	});
});