jQuery(document).ready(function($) {
    var style = document.createElement('style');
    style.innerHTML = `
    .product-card__cart .add_to_cart_button.product-in-cart {
        background: #ccc !important;
        border-color: #ccc !important;
        color: #fff !important;
        cursor: default !important;
        pointer-events: none !important;
    }
`;
  document.head.appendChild(style);

  console.log('Cataуцуцlфцувцу');

    // ==== форматирование веса и цены ====
    function formatKg(value) {
        var v = Number(value);
        if (Math.abs(v - Math.round(v)) < 1e-6) {
            return Math.round(v) + ' кг';
        }
        return v.toFixed(1).replace('.', ',') + ' кг';
    }

    function formatPriceRub(value) {
        return Math.round(Number(value) || 0).toLocaleString('ru-RU') + ' ₽';
    }

    // Пересчёт цены/веса для одной карточки товара
    function updatePriceForCard($productCard) {
        // общий контейнер товара (li.ferma-product-card.product)
        var $root = $productCard.closest('li.ferma-product-card, li.product, .ferma-product-card, .product');
        if (!$root.length) {
            $root = $productCard; // запасной вариант
        }

        // Цена
        var $priceEl = $root.find('.discount-offset').first();
        if (!$priceEl.length) return;

        var base       = Number($priceEl.data('price-base') || 0); // цена за 1 кг / 1 шт
        var ratio      = Number($priceEl.data('ratio') || 1);      // шаг (0.1 или 1)
        var isWeighted = String($priceEl.data('is-weighted')) === '1';

        // Количество
        var $qtyBlock = $root.find('.cart__qty');
        var qty = 1;
        if ($qtyBlock.length) {
            var txt = $qtyBlock.find('.cart__qty-val').text();
            qty = parseFloat(String(txt).replace(',', '.')) || 1;
        }

        var totalPrice, unitText;

        if (!isWeighted) {
            // штучный товар
            totalPrice = base * qty;
            unitText   = 'за шт.';
        } else {
            // весовой
            var totalWeight = qty * ratio;
            totalPrice = base * totalWeight;
            unitText   = 'за ' + formatKg(totalWeight);
        }

        var $unitSpan = $priceEl.find('.price-unit-text');
        if ($unitSpan.length) {
            $unitSpan.text(unitText);
            var html = formatPriceRub(totalPrice) + ' <span class="price-unit-text">' +
                $unitSpan.text() + '</span>';
            $priceEl.html(html);
        } else {
            $priceEl.text(formatPriceRub(totalPrice) + ' ' + unitText);
        }
    }



    if (typeof CartQtyData === 'undefined') {
        console.error('CartQtyData NOT LOADED');
    }
    function syncCatalogWithMiniCart() {
        var cartMap = {};

        // 1) Собираем данные из мини-корзины
        $('.woocommerce-mini-cart-item, .mini_cart_item').each(function () {
            var $item = $(this);

            var pid = $item.data('product_id') ||
                $item.find('[data-product_id]').data('product_id');

            if (!pid) return;

            var cartKey = $item.data('cart_item_key') || '';

            // Обычно "3 × 73 ₽"
            var qtyText = $item.find('.quantity').text() || '1';
            var match   = qtyText.match(/(\d+([.,]\d+)?)/);
            var qty     = match ? parseFloat(match[1].replace(',', '.')) : 1;

            cartMap[pid] = {
                qty: qty,
                key: cartKey
            };
        });

        var hasItems = !$.isEmptyObject(cartMap);

        // 2) Проставляем количество в карточки каталога
        $('.product-card__cart .cart__qty').each(function () {
            var $qtyBlock = $(this);
            var pid       = $qtyBlock.data('product_id');
            if (!pid) return;

            var meta    = (hasItems && cartMap[pid]) ? cartMap[pid] : null;
            var inCart  = !!meta;
            var qty     = inCart ? meta.qty : 1;
            var cartKey = inCart ? (meta.key || '') : '';

            var $productCard  = $qtyBlock.closest('.product-card__cart');
            var $addToCartBtn = $productCard.find('.add_to_cart_button');
            var $qtyVal       = $qtyBlock.find('.cart__qty-val');
            var $minusBtn     = $qtyBlock.find('.cart__qty-btn--minus');

            // ВАЖНО: сохраняем cart_item_key в data
            if (cartKey) {
                $qtyBlock.attr('data-cart_item_key', cartKey);
            } else {
                $qtyBlock.removeAttr('data-cart_item_key');
            }

            // Обновляем отображение количества
            $qtyVal.text(qty);
            $qtyBlock.data('current_qty', qty);

            // Минус-кнопка
            if (qty <= 1) {
                $minusBtn.addClass('is-disabled');
            } else {
                $minusBtn.removeClass('is-disabled');
            }

            // Пересчитываем цену/вес
            updatePriceForCard($productCard);

            // Кнопка "В корзину" + href
            if ($addToCartBtn.length) {
                $addToCartBtn
                    .data('quantity', qty)
                    .attr('data-quantity', qty);

                var href = $addToCartBtn.attr('href');
                if (href && href.indexOf('add-to-cart=') !== -1) {
                    var baseUrl = href.split('?')[0];
                    var newUrl  = baseUrl + '?add-to-cart=' + pid + '&quantity=' + qty;
                    $addToCartBtn.attr('href', newUrl);
                }

                if (inCart && qty > 0) {
                    $addToCartBtn.addClass('product-in-cart').text('В корзине');
                } else {
                    $addToCartBtn.removeClass('product-in-cart').text('В корзину');
                }
            }
        });
    }
    // Функция обновления количества в карточке
    function updateQuantity($qtyBlock, change, updateCart = false) {
        var $productCard   = $qtyBlock.closest('.product-card__cart');
        var $addToCartBtn  = $productCard.find('.add_to_cart_button');

        var $qtyVal           = $qtyBlock.find('.cart__qty-val');
        var currentDisplayQty = parseInt($qtyVal.text(), 10) || 1;

        var isWeighted        = $qtyBlock.data('is_weighted') === 1 || $qtyBlock.data('is_weighted') === '1';
        var productId         = $qtyBlock.data('product_id');

        // Меняем отображаемое количество (целое)
        var newDisplayQty = currentDisplayQty + change;
        if (newDisplayQty < 1) newDisplayQty = 1;

        // Обновляем отображение
        $qtyVal.text(newDisplayQty);
        $qtyBlock.data('current_qty', newDisplayQty);

        // Реальное количество для корзины
        var realQuantity = newDisplayQty;

        // Обновляем кнопку "В корзину"
        if ($addToCartBtn.length) {
            $addToCartBtn.data('quantity', realQuantity);
            $addToCartBtn.attr('data-quantity', realQuantity);

            var href = $addToCartBtn.attr('href');
            if (href) {
                var baseUrl = href.split('?')[0];
                var newUrl  = baseUrl + '?add-to-cart=' + productId + '&quantity=' + realQuantity;
                $addToCartBtn.attr('href', newUrl);
            }
        }

        // Минус-баттон
        var $minusBtn = $qtyBlock.find('.cart__qty-btn--minus');
        if (newDisplayQty <= 1) {
            $minusBtn.addClass('is-disabled');
        } else {
            $minusBtn.removeClass('is-disabled');
        }

        // Пересчитать цену/вес в этой карточке
        updatePriceForCard($productCard);

        if (updateCart && $addToCartBtn.hasClass('product-in-cart')) {
            updateCartQuantity(productId, realQuantity, $qtyBlock);
        }
    }

    function updateCartQuantity(productId, quantity, $qtyBlock) {
        var cartItemKey = '';
        if ($qtyBlock && $qtyBlock.length) {
            cartItemKey = $qtyBlock.attr('data-cart_item_key') || '';
        }

        $.ajax({
            type: 'POST',
            url:  CartQtyData.ajax_url,
            dataType: 'json',
            data: {
                action:        'update_cart_qty',
                product_id:    productId,
                cart_item_key: cartItemKey,
                qty:           quantity,
                nonce:         CartQtyData.nonce
            },
            success: function(response) {
                console.log('update_cart_qty response (catalog):', response);

                var fragments = response.fragments || (response.data && response.data.fragments);
                if (!fragments) return;

                $.each(fragments, function(selector, html) {
                    var $el = $(selector);
                    if ($el.length) {
                        $el.replaceWith(html);
                    }
                });

                // Заново синхронизируем каталог с обновлённой мини-корзиной
                syncCatalogWithMiniCart();
            }
        });
    }

    // Плюс
    $(document).on('click', '.cart__qty-btn--plus', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $qtyBlock     = $(this).closest('.cart__qty');
        var $productCard  = $qtyBlock.closest('.product-card__cart');
        var $addToCartBtn = $productCard.find('.add_to_cart_button');
        var updateCart    = $addToCartBtn.hasClass('product-in-cart');

        updateQuantity($qtyBlock, 1, updateCart);
    });

    // Минус
    $(document).on('click', '.cart__qty-btn--minus', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if ($(this).hasClass('is-disabled')) return;

        var $qtyBlock     = $(this).closest('.cart__qty');
        var $productCard  = $qtyBlock.closest('.product-card__cart');
        var $addToCartBtn = $productCard.find('.add_to_cart_button');
        var updateCart    = $addToCartBtn.hasClass('product-in-cart');

        updateQuantity($qtyBlock, -1, updateCart);
    });

    // Перед кликом "В корзину" подсовываем правильное количество
    $(document).on('mousedown', '.add_to_cart_button', function() {
        var $button      = $(this);
        var $productCard = $button.closest('.product-card__cart');
        var $qtyBlock    = $productCard.find('.cart__qty');

        if (!$qtyBlock.length) return;

        var productId = $qtyBlock.data('product_id');
        if (!productId) return;

        var currentDisplayQty = parseInt($qtyBlock.find('.cart__qty-val').text(), 10);
        if (isNaN(currentDisplayQty) || currentDisplayQty < 1) currentDisplayQty = 1;

        var realQuantity = currentDisplayQty;

        console.log('Prepared quantity for add_to_cart (catalog):', realQuantity, 'product:', productId);

        $button.data('quantity', realQuantity);
        $button.attr('data-quantity', realQuantity);

        var href = $button.attr('href');
        if (href && href.indexOf('add-to-cart=') !== -1) {
            var baseUrl = href.split('?')[0];
            var newUrl  = baseUrl + '?add-to-cart=' + productId + '&quantity=' + realQuantity;
            $button.attr('href', newUrl);
        }
    });

    $(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button) {
        if (!$button || !$button.length) return;

        $button.addClass('product-in-cart').text('В корзине');

    });


    // Синхронизируем каталог с мини-корзиной при загрузке
    syncCatalogWithMiniCart();

    // И каждый раз, когда WooCommerce обновляет фрагменты
    $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
        syncCatalogWithMiniCart();
    });
    function applyGrayStyle() {
        $('.product-card__cart .add_to_cart_button.product-in-cart').css({
            background: '#ccc',
            borderColor: '#ccc',
            color: '#fff',
            cursor: 'default',
            pointerEvents: 'none'
        });
    }
    $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
        syncCatalogWithMiniCart();
        applyGrayStyle();
    });

    $(document.body).on('added_to_cart', function () {
        applyGrayStyle();
    });

    applyGrayStyle();
});
