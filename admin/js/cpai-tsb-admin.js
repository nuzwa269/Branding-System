(function($) {
	'use strict';

	$(document).on('click', '.cpai-upload-image-btn', function(e) {
		e.preventDefault();
		const button = $(this);
		const input = button.siblings('.cpai-image-url-input');
		if (!input.length || typeof wp === 'undefined' || !wp.media) {
			return;
		}

		const frame = wp.media({
			title: 'Select Question Image',
			button: {
				text: 'Use this image'
			},
			multiple: false,
			library: {
				type: 'image'
			}
		});

		frame.on('select', function() {
			const attachment = frame.state().get('selection').first().toJSON();
			input.val(attachment.url).trigger('change');
		});

		frame.open();
	});
})(jQuery);
