jQuery(document).ready(function($) {
    console.log('Fixed catalog quantity script loaded');

    // Функция для обновления количества
    function updateQuantity($qtyBlock, change) {
        var $qtyVal = $qtyBlock.find('.cart__qty-val');
        var $addToCartBtn = $qtyBlock.siblings('.add_to_cart_button');
        var currentDisplayQty = parseInt($qtyVal.text());
        var weightRatio = parseFloat($qtyBlock.data('weight_ratio')) || 1;
        var isWeighted = $qtyBlock.data('is_weighted') === '1';
        var productId = $qtyBlock.data('product_id');

        console.log('Display Qty:', currentDisplayQty, 'Weight ratio:', weightRatio, 'Is weighted:', isWeighted);

        // Меняем отображаемое количество (всегда целое число)
        var newDisplayQty = currentDisplayQty + change;
        newDisplayQty = Math.max(1, newDisplayQty); // Минимум 1

        console.log('New display qty:', newDisplayQty);

        // Обновляем отображение
        $qtyVal.text(newDisplayQty);
        $qtyBlock.data('current_qty', newDisplayQty);

        // Рассчитываем реальное количество для корзины
        var realQuantity = isWeighted ? (newDisplayQty * 0.1) : newDisplayQty;
        console.log('Real quantity for cart:', realQuantity);

        // Обновляем кнопку добавления в корзину
        if ($addToCartBtn.length) {
            // Обновляем data-атрибут
            $addToCartBtn.attr('data-quantity', realQuantity);

            // Обновляем URL
            var newHref = '?add-to-cart=' + productId + '&quantity=' + realQuantity;
            $addToCartBtn.attr('href', newHref);

            // Для AJAX кнопок обновляем data-attributes
            $addToCartBtn.attr('data-product_id', productId);
            $addToCartBtn.attr('data-product_sku', $addToCartBtn.data('product_sku') || '');
        }

        // Обновляем состояние кнопки "минус"
        var $minusBtn = $qtyBlock.find('.cart__qty-btn--minus');
        if (newDisplayQty <= 1) {
            $minusBtn.addClass('is-disabled');
        } else {
            $minusBtn.removeClass('is-disabled');
        }
    }

    // Обработчики событий
    $(document).on('click', '.cart__qty-btn--plus', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Plus clicked');
        var $qtyBlock = $(this).closest('.cart__qty');
        updateQuantity($qtyBlock, 1);
    });

    $(document).on('click', '.cart__qty-btn--minus', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Minus clicked');
        var $qtyBlock = $(this).closest('.cart__qty');
        if (!$qtyBlock.find('.cart__qty-btn--minus').hasClass('is-disabled')) {
            updateQuantity($qtyBlock, -1);
        }
    });

    // Инициализация при загрузке
    $('.cart__qty').each(function() {
        var $qtyBlock = $(this);
        var currentQty = parseInt($qtyBlock.find('.cart__qty-val').text()) || 1;
        $qtyBlock.data('current_qty', currentQty);

        // Блокируем минус если нужно
        if (currentQty <= 1) {
            $qtyBlock.find('.cart__qty-btn--minus').addClass('is-disabled');
        }
    });
});