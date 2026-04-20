jQuery(function($) {
    console.log('cart-validation loaded');

    function fermaUpdateCardPrice($card) {
        var $price = $card.find('.woocommerce-Price-amount[data-price-base]');
        if (!$price.length) return;

        var base  = parseFloat($price.data('price-base'));
        var ratio = parseFloat($price.data('ratio'));

        if (!base || base <= 0) return;
        if (!ratio || ratio <= 0) ratio = 1;

        var $qtyVal = $card.find('.cart__qty-val');
        if (!$qtyVal.length) return;

        var steps = parseFloat($qtyVal.text().replace(',', '.'));
        if (!steps) steps = 1;

        var realQty = steps * ratio; // кг ИЛИ 0.1/0.2/0.3 кг
        var total   = base * realQty;

        // формат цены
        var formatted = total.toLocaleString('ru-RU', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        var unitText;
        if (Math.abs(realQty - Math.round(realQty)) < 1e-4) {
            // почти целое — без десятых
            unitText = 'за ' + realQty.toLocaleString('ru-RU', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }) + ' кг';
        } else {
            // не целое — один знак
            unitText = 'за ' + realQty.toLocaleString('ru-RU', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }) + ' кг';
        }

        // обновляем цену (только текст)
        $price.contents().filter(function() {
            return this.nodeType === 3;
        }).first().replaceWith(formatted + ' ₽ ');

        // обновляем единицу измерения:
        $price.find('.price-unit-text').text(unitText);
    }

    // реакция на клики по количеству
    $(document).on('click', '.cart__qty-btn', function() {
        var $card = $(this).closest('.ferma-product-card');
        if ($card.length) {
            setTimeout(function() {
                fermaUpdateCardPrice($card);
            }, 0);
        }
    });

    // обновление для уже существующих карточек
    $('.ferma-product-card').each(function() {
        fermaUpdateCardPrice($(this));
    });
});
