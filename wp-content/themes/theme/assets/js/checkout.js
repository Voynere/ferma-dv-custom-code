jQuery(document).on('checkout_error', function() {
    setTimeout(function() {
        jQuery('.checkout-inline-error-message').each(function() {
            const errorText = jQuery(this).text();
            jQuery(this).text(errorText.replace('Платежи ', ''));
        });
    }, 1); 
});