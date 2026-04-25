(function ($) {
    function applyCouponVisibility() {
        var isPickup = !!(window.FDV_CHECKOUT && window.FDV_CHECKOUT.is_pickup);

        if (isPickup) {
            $('.promo_code_block').hide();
            $('.q-promo-message').hide();
        } else {
            $('.promo_code_block').show();
            $('.q-promo-message').show();
        }
    }

    $(document).ready(function () {
        applyCouponVisibility();

        // WooCommerce часто перерисовывает checkout
        $(document.body).on('updated_checkout', applyCouponVisibility);

        // если у тебя выбор доставки/самовывоза через модалку — на всякий
        $(document).on('click', '#but_dev, #but_dev2', function () {
            setTimeout(applyCouponVisibility, 300);
        });
    });
})(jQuery);
