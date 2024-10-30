/* global jQuery */
(function($) {
	$(function() {
		let modal = $( '#ephd-deactivate-modal' );
		let deactivateLink = $( '#the-list' ).find( '[data-slug="help-dialog"] span.deactivate a' );

		// Open modal
		deactivateLink.on( 'click', function( e ) {
			e.preventDefault();

			modal.addClass( 'modal-active' );
			deactivateLink = $( this ).attr( 'href' );
		});

		// Close modal; Cancel
		modal.on( 'click', 'button.ephd-deactivate-cancel-modal', function( e ) {
			e.preventDefault();
			modal.removeClass( 'modal-active' );
		});

		// Click submit button
		modal.on( 'click', '.ephd-deactivate-submit-modal', function( e ) {
			e.preventDefault();

			// submit form
			modal.find( 'form#ephd-deactivate-feedback-dialog-form' ).trigger( 'submit' );
		});

		// Submit form
		modal.on( 'submit', 'form#ephd-deactivate-feedback-dialog-form', function( e ) {
			e.preventDefault();

			if ( ! this.reportValidity() ) {
				return;
			}

			let button = $( this ).find( '.ephd-deactivate-submit-modal' );

			if ( button.hasClass( 'disabled' ) ) {
				return;
			}

			let formData = $( '#ephd-deactivate-feedback-dialog-form', modal ).serialize();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: formData,
				beforeSend: function() {
					button.addClass( 'disabled' );
					button.text( 'Processing...' );
				},
				complete: function() {
					window.location.href = deactivateLink;
				}
			});
		});

	});
}(jQuery));