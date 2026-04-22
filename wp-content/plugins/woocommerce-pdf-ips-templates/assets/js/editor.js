jQuery(function($) {
	$( "#documents .field-list" ).sortable({
		items: '.field',
		cursor: 'move',
	});

	$( document ).on( "click", ".delete-field", function() { 
		$(this).parent().remove();
	});

	// hide & disable input fields based on type selection
	$( '.custom-blocks' ).on( 'change', 'select.custom-block-type', function () {
		var option = $( this ).val();
		var $current_block = $( this ).closest('.custom-block');
		var $meta_key = $current_block.find('.meta_key');
		var $custom_text = $current_block.find('.custom_text');
		var $hide_if_empty = $current_block.find('.hide_if_empty');
		if ( option == 'custom_field' || option == 'user_meta' ) {
			$custom_text.find('textarea').val('').prop('disabled', true);
			$custom_text.hide();
			$meta_key.show().find('input').prop('disabled', false);
			$hide_if_empty.show().find('input').prop('disabled', false);
		} else {
			$meta_key.find('input').val('').prop('disabled', true);
			$meta_key.hide();
			$hide_if_empty.find('input').val('').prop('disabled', true);
			$hide_if_empty.hide();			
			$custom_text.show().find('textarea').prop('disabled', false);
		}
	})
	$( 'select.custom-block-type' ).change(); //ensure visible state matches initially

	// Add Custom block

	$( '.document-content' ).on( "click", ".button.add-custom-block", function() { 
		var $current_doc = $( this ).closest('.document-content');
		var document_type = $current_doc.data('document-type');
		var data = {
			security:      wpo_wcpdf_templates.nonce,
			action:        'wcpdf_templates_add_custom_block',
			document_type: document_type,
		};

		xhr = $.ajax({
			type:		'POST',
			url:		wpo_wcpdf_templates.ajaxurl,
			data:		data,
			success:	function( data ) {
				$current_doc.find('.custom-blocks').append( data );
				$current_doc.find('select.custom-block-type').change();
			}
		});
	});

	// Add field to totals or columns

	$( '.dropdown-add-field').change(function () {
		var $section = $(this).closest('.field-list');
		var $current_doc = $( this ).closest('.document-content');
		var document_type = $current_doc.data('document-type');
		var $field_value = $(this).val();
		var data = {
			security:      	wpo_wcpdf_templates.nonce,
			action:        	'wcpdf_templates_add_totals_columns_field',
			section:       	$section.data('section_key'),
			document_type: 	document_type,
			field_value:   	$field_value,
		};

		xhr = $.ajax({
			context:    $section,
			type:		'POST',
			url:		wpo_wcpdf_templates.ajaxurl,
			data:		data,
			success:	function( html ) {
				var $html = $( html ).insertBefore( $(this).find('.document.add-field') );
				$html.accordion({
					header: '.field-title',
					collapsible: true,
					active: 0,
				});
				$( "#documents .field-list" ).sortable({
					items: '.field',
					cursor: 'move'
				});
				$( ".dropdown-add-field" ).val('default');
			}
		});
	});

	// Disable VAT percent and VAT base when single_total is checked
	$( ".field.options [data-key='single_total']" ).change(function () {
		$block = $(this).closest('.field.options');
		if ( $(this).attr( 'checked' ) ) {
			$block.find( "[data-key='percent'], [data-key='base']" ).prop( 'disabled', true );
			$block.find( "[data-key='percent'], [data-key='base']" ).attr( 'checked', false );
		} else {
			$block.find( "[data-key='percent'], [data-key='base']" ).prop( 'disabled', false );
		}
	});
	// trigger change on page load
	$( ".field.options [data-key='single_total']" ).change();

	$( '.field.options' ).accordion({
		header: '.field-title',
		collapsible: true,
		active: false
	});

	$( '.fields.library' ).accordion({
		header: 'h4'
	});


	$( '#documents' ).tabs().show();
});
		