(function($) {
	'use strict';

	function showImagePreview(input, url) {
		const p = input.closest('p');
		let preview = p.find('.cpai-image-preview');
		let removeBtn = p.find('.cpai-remove-image-btn');
		if (url) {
			preview.attr('src', url).show();
			removeBtn.show();
		} else {
			preview.attr('src', '').hide();
			removeBtn.hide();
		}
	}

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
			const sizes = attachment.sizes || {};
			const previewUrl = (sizes.medium && sizes.medium.url) ||
				(sizes.thumbnail && sizes.thumbnail.url) ||
				attachment.url;
			input.val(attachment.url).trigger('change');
			showImagePreview(input, previewUrl);
		});

		frame.open();
	});

	$(document).on('click', '.cpai-remove-image-btn', function() {
		const button = $(this);
		const p = button.closest('p');
		p.find('.cpai-image-url-input').val('').trigger('change');
		showImagePreview(p.find('.cpai-image-url-input'), '');
	});
})(jQuery);
