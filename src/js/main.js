$(document).ready(function() {
	// you can add more JavaScript/jQuery to your liking
	$(document).on('click touchstart', function(event) {
		if (!$('.navigation, .footer').find(event.target).length && $('.navigation .navigation-checkbox').prop('checked')) {
			$('.navigation .navigation-checkbox').prop('checked', false);
		}
	});
	var toogle = false;
	$('.front-form .info-checkbox').on('change', function(event) {
		toogle = !toogle;
		$('.front-form .info-checkbox').each(function(i, val) {
			if ($(val).prop('id') !== $(event.target).prop('id') && $(val).prop('checked')) {
				$(val).prop('checked', false);
				toogle = !toogle;
			}
		});
	});
	$('.front-form input[type="text"], .front-form input[type="password"]').on('focus', function(event) {
		var dropdown = '#' + $(event.target).prop('id') + 'Dropdown';
		if (toogle && !$(dropdown).prop('checked')) {
			$(dropdown).prop('checked', true).trigger('change');
		}
	});
});
