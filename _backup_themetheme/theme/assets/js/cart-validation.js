jQuery(function($) {

    function applyFragments(fragments) {
        if (!fragments) return;
        $.each(fragments, function(selector, html) {
            var $el = $(selector);
            if ($el.length) {
                $el.replaceWith(html);
            }
        });
    }

    // Нормализуем состояние уже добавленных товаров при загрузке страницы
    $('.product-card__cart .add_to_cart_button.added').each(function() {
        var $btn  = $(this);
        var $card = $btn.closest('.product-card__cart');
        $card.addClass('product-in-cart');
    });

    // Клик по +/-
    $(document).on('click', '.cart__qty-btn', function(e) {
        e.preventDefault();

        var $btn    = $(this);
        var $wrap   = $btn.closest('.cart__qty');
        var $card   = $wrap.closest('.product-card__cart');
        var $addBtn = $card.find('.add_to_cart_button');

        var isWeighted = $wrap.data('is_weighted') == 1;
        var step       = parseFloat($wrap.data('step')) || (isWeighted ? 0.1 : 1);

        // берём либо из data-current_qty, либо из текста
        var displayQty = parseFloat($wrap.data('current_qty'));
        if (isNaN(displayQty) || displayQty <= 0) {
            displayQty = parseFloat($wrap.find('.cart__qty-val').text().replace(',', '.')) || 1;
        }

        // НАЧИНАЕМ ИЗ РЕАЛЬНОГО ОТОБРАЖАЕМОГО ЗНАЧЕНИЯ
        if ($btn.hasClass('cart__qty-btn--plus')) {
            displayQty += 1;
        } else {
            displayQty -= 1;
            if (displayQty < 1) {
                displayQty = 1;
            }
        }

        // Обновляем отображение и data-атрибут
        $wrap.data('current_qty', displayQty);
        $wrap.find('.cart__qty-val').text(displayQty);

        // Управляем минусом
        if (displayQty <= 1) {
            $card.find('.cart__qty-btn--minus').addClass('is-disabled');
        } else {
            $card.find('.cart__qty-btn--minus').removeClass('is-disabled');
        }

        // Реальное количество для корзины (с учётом шага)
        var realQty = isWeighted ? (displayQty * step) : displayQty;

        // КОРРЕКТНОЕ условие "товар уже в корзине"
        var inCart = $card.hasClass('product-in-cart') || $addBtn.hasClass('added') || $addBtn.text().trim() === 'В корзине';

        // Если товар ещё НЕ в корзине — только обновляем кнопку
        if (!inCart) {
            $addBtn.attr('data-quantity', realQty);

            var href = $addBtn.attr('href');
            if (href) {
                href = href.replace(/([?&]quantity=)[^&]*/g, '');
                if (href.indexOf('?') === -1) {
                    href += '?quantity=' + realQty;
                } else {
                    href += '&quantity=' + realQty;
                }
                $addBtn.attr('href', href);
            }

            return;
        }

        // Если товар уже в корзине — отправляем AJAX и применяем фрагменты
        $.post(theme_qty.ajaxurl, {
            action:     'update_cart_qty',
            nonce:      theme_qty.nonce,
            product_id: $wrap.data('product_id'),
            qty:        realQty
        }, function(response) {
            if (response && response.fragments) {
                applyFragments(response.fragments);
            }
        });
    });

    // После добавления товара в корзину
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        var $card = $button.closest('.product-card__cart');
        if (!$card.length) return;

        $card.addClass('product-in-cart');

        $button
            .addClass('added')
            .prop('disabled', true);

        var $wrap      = $card.find('.cart__qty');
        var displayQty = parseFloat($wrap.data('current_qty')) || parseFloat($wrap.find('.cart__qty-val').text()) || 1;
        if (displayQty <= 1) {
            $card.find('.cart__qty-btn--minus').addClass('is-disabled');
        }
    });

});
jQuery(function($) {

    console.log('cart-validation loaded');

    function fermaUpdateCardPrice($card) {
        var $priceWrap = $card.find('.product-card__price');
        if (!$priceWrap.length) return;

        var base  = parseFloat($priceWrap.data('price-base')); // цена за 1 кг/шт
        if (!base || base <= 0) return;

        var ratio = parseFloat($priceWrap.data('ratio'));
        if (!ratio || ratio <= 0) {
            ratio = 1; // для не весовых
        }

        var $qtyVal = $card.find('.cart__qty-val');
        if (!$qtyVal.length) return;

        var txt  = $.trim($qtyVal.text()).replace(',', '.');
        var steps = parseFloat(txt);
        if (!steps || steps <= 0) {
            steps = 1;
        }

        // реальное количество (в кг или штуках)
        var realQty = steps * ratio;

        var total = base * realQty;

        var formatted = total.toLocaleString('ru-RU', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });

        $priceWrap.find('.product-card__price-value').text(formatted + ' ₽');
    }

    // Обновляем цену при клике по +/- на карточке
    $(document).on('click', '.product-card__cart .cart__qty-btn', function() {
        var $card = $(this).closest('.product, .product-card');
        if (!$card.length) return;

        // даём твоему коду обновить .cart__qty-val
        setTimeout(function() {
            fermaUpdateCardPrice($card);
        }, 0);
    });

    // Пересчёт при первой загрузке (если qty не 1)
    $('.product, .product-card').each(function() {
        fermaUpdateCardPrice($(this));
    });
});
