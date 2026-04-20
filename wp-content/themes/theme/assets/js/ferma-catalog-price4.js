jQuery(function($) {
    console.log('cart-validation loaded');

    function fermaUpdateCardPrice($card) {
        var $price = $card.find('.woocommerce-Price-amount[data-price-base]');
        if (!$price.length) return;

        var base  = parseFloat($price.data('price-base'));
        var ratio = parseFloat($price.data('ratio'));
        var isWeighted = parseInt($price.data('is-weighted'), 10) === 1;

        if (!base || base <= 0) return;
        if (!ratio || ratio <= 0) ratio = 1;

        var $qtyVal = $card.find('.cart__qty-val');
        if (!$qtyVal.length) return;

        var steps = parseFloat($qtyVal.text().replace(',', '.'));
        if (!steps) steps = 1;

        var realQty = steps * ratio; // для весовых – кг, для штучных – просто кол-во
        var total   = base * realQty;

        // формат цены
        var formatted = total.toLocaleString('ru-RU', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        // обновляем цену (только текст)
        $price.contents().filter(function() {
            return this.nodeType === 3;
        }).first().replaceWith(formatted + ' ₽ ');

        // ---- единица измерения ----
        var $unit = $price.find('.price-unit-text');

        // если товар НЕ весовой — не лезем в "за шт."
        if (!isWeighted) {
            // максимум можешь захотеть показывать "за N шт."
            // $unit.text('за ' + steps + ' шт.');
            return;
        }

        // весовые — тут уже логика с кг
        var unitText;
        if (Math.abs(realQty - Math.round(realQty)) < 1e-4) {
            unitText = 'за ' + realQty.toLocaleString('ru-RU', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }) + ' кг';
        } else {
            unitText = 'за ' + realQty.toLocaleString('ru-RU', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }) + ' кг';
        }

        $unit.text(unitText);
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
