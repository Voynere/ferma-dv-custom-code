jQuery(document).on('checkout_error', function() {
    setTimeout(function() {
        var $noticeGroup = jQuery('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout');
        if ($noticeGroup.length) {
            $noticeGroup.css('display', 'block');
            jQuery('html, body').animate({
                scrollTop: Math.max($noticeGroup.first().offset().top - 120, 0)
            }, 250);
        }

        jQuery('.checkout-inline-error-message').each(function() {
            const errorText = jQuery(this).text();
            jQuery(this).text(errorText.replace('Платежи ', ''));
        });
    }, 1); 
});