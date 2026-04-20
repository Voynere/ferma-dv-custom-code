jQuery(document).ready(function($) {
	$(document).on('click', '[name="add-to-cart-complect"]', function() {
		let productID = $(this).data('id');
		
		$.ajax({
			url: complect.ajax_url,
			type: 'POST',
			data: {action: 'add_to_cart_complect', productID: productID},
		    success: function(response) {
				window.location.reload();
			}
		});
	});
});