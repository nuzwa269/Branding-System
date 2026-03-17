(function($) {
	'use strict';

	// Helper: find the preview img and remove button inside the same .cpai-img-field wrapper
	function getPreviewEl(input) {
		return input.closest('.cpai-img-field').find('.cpai-image-preview');
	}
	function getRemoveBtn(input) {
		return input.closest('.cpai-img-field').find('.cpai-remove-image-btn');
	}

	// Show preview when URL input already has a value (page load)
	$(document).ready(function() {
		$('.cpai-image-url-input').each(function() {
			const input = $(this);
			const url = input.val().trim();
			const preview = getPreviewEl(input);
			const removeBtn = getRemoveBtn(input);
			if (url && preview.length) {
				preview.attr('src', url).show();
				removeBtn.show();
			}
		});
	});

	// Open WP Media frame on upload button click
	$(document).on('click', '.cpai-upload-image-btn', function(e) {
		e.preventDefault();
		const button = $(this);
		const input = button.closest('.cpai-img-field').find('.cpai-image-url-input');
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
			// Prefer medium or thumbnail size for preview, but save full URL
			const previewSrc = (attachment.sizes && attachment.sizes.medium)
				? attachment.sizes.medium.url
				: ((attachment.sizes && attachment.sizes.thumbnail)
					? attachment.sizes.thumbnail.url
					: attachment.url);

			input.val(attachment.url).trigger('change');

			const preview = getPreviewEl(input);
			const removeBtn = getRemoveBtn(input);
			if (preview.length) {
				preview.attr('src', previewSrc).show();
			}
			if (removeBtn.length) {
				removeBtn.show();
			}
		});

		frame.open();
	});

	// Remove image
	$(document).on('click', '.cpai-remove-image-btn', function(e) {
		e.preventDefault();
		const btn = $(this);
		const wrapper = btn.closest('.cpai-img-field');
		wrapper.find('.cpai-image-url-input').val('').trigger('change');
		wrapper.find('.cpai-image-preview').attr('src', '').hide();
		btn.hide();
	});

})(jQuery);