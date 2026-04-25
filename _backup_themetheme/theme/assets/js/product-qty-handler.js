(function ($) {
    'use strict';

    $(function () {

        // ====== ФУНКЦИОНАЛ ДЛЯ КАТАЛОГА ======

        // Нормализация стартовых значений в каталоге
        $('.product-card__cart .cart__qty').each(function () {
            var $wrap = $(this);

            // Только для каталога, не для мини-корзины
            if ($wrap.closest('.widget_shopping_cart').length ||
                $wrap.closest('.woocommerce-cart-form').length) {
                return;
            }

            var $val = $wrap.find('.cart__qty-val');
            var ratio = parseFloat(
                String($wrap.data('weight_ratio') || '1').replace(',', '.')
            ) || 1;

            var rawText = ($val.text() || '').replace(',', '.');
            var qty = parseFloat(rawText);

            if (isNaN(qty) || qty <= 0) {
                qty = ratio;
            }

            var display;
            if (ratio < 1) {
                qty = parseFloat(qty.toFixed(1));
                display = String(qty).replace('.', ',');
            } else {
                qty = Math.round(qty);
                display = String(qty);
            }

            $val.text(display);

            // Пробрасываем в data-quantity кнопки "В корзину"
            var $btnAdd = $wrap.closest('.product-card__cart').find('.add_to_cart_button');
            if ($btnAdd.length) {
                $btnAdd
                    .attr('data-quantity', qty)
                    .data('quantity', qty);
            }
        });

        // Обработчики для КАТАЛОГА
        $(document).on('click', '.product-card__cart .cart__qty-btn--plus', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Останавливаем всплытие

            var $wrap = $(this).closest('.cart__qty');
            changeCatalogQty($wrap, +1);
        });

        $(document).on('click', '.product-card__cart .cart__qty-btn--minus', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Останавливаем всплытие

            var $wrap = $(this).closest('.cart__qty');
            changeCatalogQty($wrap, -1);
        });

        function changeCatalogQty($wrap, direction) {
            var $val = $wrap.find('.cart__qty-val');
            var ratio = parseFloat(
                String($wrap.data('weight_ratio') || '1').replace(',', '.')
            ) || 1;

            var raw = ($val.text() || '').replace(',', '.');
            var qty = parseFloat(raw);

            if (isNaN(qty) || qty <= 0) {
                qty = ratio;
            }

            qty += direction * ratio;

            if (qty < ratio) {
                qty = ratio;
            }

            var display;
            if (ratio < 1) {
                qty = parseFloat(qty.toFixed(1));
                display = String(qty).replace('.', ',');
            } else {
                qty = Math.round(qty);
                display = String(qty);
            }

            $val.text(display);

            // Пробрасываем количество в кнопку "В корзину"
            var $card = $wrap.closest('.product-card__cart');
            var $addToCart = $card.find('.add_to_cart_button');

            if ($addToCart.length) {
                var qtyForWoo = qty;
                $addToCart
                    .attr('data-quantity', qtyForWoo)
                    .data('quantity', qtyForWoo);
            }
        }

        // ====== ФУНКЦИОНАЛ ДЛЯ КОРЗИНЫ ======

        // Обработчики для КОРЗИНЫ (мини-корзина и страница корзины)
        $(document).on('click', '.widget_shopping_cart .cart__qty-btn--plus, .woocommerce-cart-form .cart__qty-btn--plus', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $wrap = $(this).closest('.cart__qty');
            changeCartQty($wrap, +1);
        });

        $(document).on('click', '.widget_shopping_cart .cart__qty-btn--minus, .woocommerce-cart-form .cart__qty-btn--minus', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $wrap = $(this).closest('.cart__qty');
            changeCartQty($wrap, -1);
        });

        function changeCartQty($wrap, direction) {
            var $val = $wrap.find('.cart__qty-val');
            var cartItemKey = $wrap.data('cart_item_key');
            var productId = $wrap.data('product_id');

            var currentQty = parseInt($val.text()) || 1;
            var newQty = currentQty + direction;

            if (newQty < 1) newQty = 1;

            $val.text(newQty);

            // AJAX обновление корзины
            $.ajax({
                url: CartQtyData.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_cart_qty',
                    nonce: CartQtyData.nonce,
                    cart_item_key: cartItemKey,
                    product_id: productId,
                    qty: newQty
                },
                success: function(response) {
                    if (response.success) {
                        $(document.body).trigger('wc_fragment_refresh');
                    }
                }
            });
        }

        // ====== ОБЩИЕ СОБЫТИЯ ======

        $(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
            if (!$button || !$button.length) return;

            var $card = $button.closest('.product-card__cart');
            if (!$card.length) return;

            var $qtyWrap = $card.find('.cart__qty');
            if ($qtyWrap.length) {
                $qtyWrap.attr('data-in-cart', '1');
            }

            $button
                .addClass('is-added')
                .prop('disabled', true)
                .text('В корзине');
        });

    });

})(jQuery);