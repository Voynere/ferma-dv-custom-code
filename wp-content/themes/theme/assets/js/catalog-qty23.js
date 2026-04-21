jQuery(document).ready(function($) {
    var style = document.createElement('style');
    style.innerHTML = `
    .product-card__cart .add_to_cart_button.product-in-cart,
    .single_add_to_cart_button.product-in-cart {
        background: #ccc !important;
        border-color: #ccc !important;
        color: #fff !important;
        cursor: default !important;
        pointer-events: none !important;
    }
    .ferma-addcart-toast {
        position: fixed;
        z-index: 2147483000;
        min-width: 260px;
        max-width: min(90vw, 380px);
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #dbe8cf;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(0,0,0,.16);
        color: #1f2d18;
        opacity: 0;
        transform: translateY(-4px);
        transition: opacity .18s ease, transform .18s ease;
        pointer-events: none;
    }
    .ferma-addcart-toast.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .ferma-addcart-toast__title {
        margin: 0 0 4px;
        font-size: 14px;
        line-height: 1.3;
        font-weight: 700;
    }
    .ferma-addcart-toast__meta {
        margin: 0;
        font-size: 13px;
        line-height: 1.35;
    }
`;
    document.head.appendChild(style);

    console.log('Ca223232323lфцувцу');
    var singleCartKey = $('#single_cart_item_key').val() || '';

    // если товар уже был в корзине, считаем, что кнопка "в корзине"
    if (singleCartKey) {
        $('.single_add_to_cart_button').addClass('product-in-cart');
        console.log('Single product: found cart_item_key =', singleCartKey);
    } else {
        console.log('Single product: no cart_item_key, товар ещё не в корзине');
    }
    function formatKg(value) {
        var v = Number(value);

        // убиваем плавающие хвосты до 1 знака
        v = Math.round(v * 10) / 10;

        // считаем это целым, если очень близко
        if (Math.abs(v - Math.round(v)) < 1e-4) {
            return Math.round(v) + ' кг';
        }

        return v.toFixed(1).replace('.', ',') + ' кг';
    }
    function formatPriceRub(value) {
        return Math.round(Number(value) || 0).toLocaleString('ru-RU') + ' ₽';
    }
    function formatQty(value) {
        var v = Number(value);
        if (!isFinite(v) || v <= 0) return '1';
        if (Math.abs(v - Math.round(v)) < 1e-4) return String(Math.round(v));
        return v.toFixed(1).replace('.', ',');
    }
    var fermaAddToastTimer = null;
    function fermaGetCartAnchor() {
        // Якоримся к "иконке" (или счетчику на ней), а не к широкому контейнеру корзины.
        var $anchors = $(
            '.xoo-wsc-basket .xoo-wsc-icon-basket:visible,' +
            '.xoo-wsc-basket .xoo-wsc-items-count:visible,' +
            '.xoo-wsc-basket .xoo-wsc-bki:visible,' +
            '.xoo-wsc-basket:visible'
        );
        if (!$anchors.length) {
            return $();
        }
        var bestEl = null;
        var bestScore = Infinity;
        $anchors.each(function () {
            var el = this;
            var rect = el.getBoundingClientRect();
            if (rect.width <= 0 || rect.height <= 0) {
                return;
            }
            // Отсекаем элементы, вышедшие за экран.
            if (rect.right < 0 || rect.left > window.innerWidth || rect.bottom < 0 || rect.top > window.innerHeight) {
                return;
            }
            // Предпочитаем корзину, которая находится в видимой области (в т.ч. sticky-header).
            var inViewport = rect.bottom > 0 && rect.top < window.innerHeight;
            // Приоритет: элемент в верхней части хедера + компактный (иконка/бейдж) + ближе к правому краю.
            var inHeaderBand = rect.top >= 0 && rect.top <= 180;
            var compactPenalty = (rect.width * rect.height) / 10; // большие контейнеры получают худший score
            var rightBias = Math.abs(window.innerWidth - rect.right);
            var score =
                (inViewport ? 0 : 100000) +
                (inHeaderBand ? 0 : 50000) +
                compactPenalty +
                rightBias +
                Math.abs(rect.top);
            if (score < bestScore) {
                bestScore = score;
                bestEl = el;
            }
        });
        return bestEl ? $(bestEl) : $anchors.first();
    }
    function fermaExtractAddedInfo($button) {
        var qty = Number($button.attr('data-quantity') || $button.data('quantity') || 1);
        if (!isFinite(qty) || qty <= 0) qty = 1;
        var $root = $button.closest('li.ferma-product-card, li.product, .ferma-product-card, .product, .shop-ferma__cart, form.cart, .product-card');
        var name = $.trim(
            $root.find('.woocommerce-loop-product__title, h1.product_title, .product_title, .product-name a, .product-name').first().text()
        );
        if (!name) {
            var aria = String($button.attr('aria-label') || '');
            var m = aria.match(/["«](.+?)["»]/);
            if (m && m[1]) name = m[1];
        }
        if (!name) name = 'Товар';
        var $price = $root.find('.discount-offset, .price .amount, .woocommerce-Price-amount').first();
        var priceText = $.trim($price.text().replace(/\s+/g, ' '));
        if (!priceText) priceText = '';
        var isWeighted = String($price.data('is-weighted')) === '1';
        var ratio = Number($price.data('ratio') || 1);
        if (!isFinite(ratio) || ratio <= 0) ratio = 1;
        var qtyText = isWeighted ? ('весом ' + formatKg(qty * ratio)) : ('в количестве ' + formatQty(qty) + ' шт.');
        return {
            name: name,
            qtyText: qtyText,
            priceText: priceText
        };
    }
    function fermaShowAddToast($button) {
        if (!$button || !$button.length) return;
        var info = fermaExtractAddedInfo($button);
        var $toast = $('.ferma-addcart-toast');
        if (!$toast.length) {
            $toast = $('<div class="ferma-addcart-toast" role="status" aria-live="polite">' +
                '<p class="ferma-addcart-toast__title"></p>' +
                '<p class="ferma-addcart-toast__meta"></p>' +
                '</div>');
            $('body').append($toast);
        }
        var meta = 'Вы добавили ' + info.qtyText + (info.priceText ? (', ' + info.priceText) : '');
        $toast.find('.ferma-addcart-toast__title').text(info.name);
        $toast.find('.ferma-addcart-toast__meta').text(meta);
        var $anchor = fermaGetCartAnchor();
        var top = 20;
        var left = window.innerWidth - $toast.outerWidth() - 20;
        if ($anchor.length) {
            var rect = $anchor[0].getBoundingClientRect();
            top = Math.max(8, rect.bottom + 8);
            // Ставим pop-up строго под иконкой корзины.
            left = rect.left + (rect.width / 2) - ($toast.outerWidth() / 2);
            left = Math.max(8, Math.min(left, window.innerWidth - $toast.outerWidth() - 8));
        }
        $toast.css({ top: top + 'px', left: left + 'px' }).addClass('is-visible');
        if (fermaAddToastTimer) {
            clearTimeout(fermaAddToastTimer);
        }
        fermaAddToastTimer = setTimeout(function () {
            $toast.removeClass('is-visible');
        }, 3000);
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
    function updateQuantity($qtyBlock, change, updateCart = false) {
        var $productCard   = $qtyBlock.closest('.product-card__cart');
        var $addToCartBtn  = $productCard.find('.add_to_cart_button');

        var $qtyVal           = $qtyBlock.find('.cart__qty-val');
        var currentDisplayQty = parseInt($qtyVal.text(), 10) || 1;

        // читаем максимум из data-max_qty
        var maxQty = parseFloat($qtyBlock.data('max_qty'));
        if (!maxQty || maxQty < 1) {
            maxQty = Infinity; // если не передали — не ограничиваем
        }

        var newDisplayQty = currentDisplayQty + change;

        // нижняя граница
        if (newDisplayQty < 1) {
            newDisplayQty = 1;
        }

        // верхняя граница по остатку
        if (newDisplayQty > maxQty) {
            newDisplayQty = maxQty;
        }

        // если уже на максимуме и жмут "+", просто выходим
        if (newDisplayQty === currentDisplayQty && change > 0 && maxQty !== Infinity) {
            return;
        }

        // Обновляем отображение
        $qtyVal.text(newDisplayQty);
        $qtyBlock.data('current_qty', newDisplayQty);

        var realQuantity = newDisplayQty;

        // Обновляем кнопку "В корзину"
        if ($addToCartBtn.length) {
            $addToCartBtn.data('quantity', realQuantity);
            $addToCartBtn.attr('data-quantity', realQuantity);

            var productId = $qtyBlock.data('product_id');
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

        // Плюс-баттон — отключаем, если упёрлись в максимум
        var $plusBtn = $qtyBlock.find('.cart__qty-btn--plus');
        if (maxQty !== Infinity && newDisplayQty >= maxQty) {
            $plusBtn.addClass('is-disabled');
        } else {
            $plusBtn.removeClass('is-disabled');
        }

        // Пересчитать цену/вес в этой карточке
        updatePriceForCard($productCard);

        if (updateCart && $addToCartBtn.hasClass('product-in-cart')) {
            var productId = $qtyBlock.data('product_id');
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

    $(document).on('click', '.cart__qty-btn--plus', function(e) {
        // если это кнопка внутри .shop-ferma__cart — выходим, ею занимается другой код
        if ($(this).closest('.shop-ferma__cart').length) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        var $qtyBlock     = $(this).closest('.cart__qty');
        var $productCard  = $qtyBlock.closest('.product-card__cart');
        var $addToCartBtn = $productCard.find('.add_to_cart_button');
        var updateCart    = $addToCartBtn.hasClass('product-in-cart');

        updateQuantity($qtyBlock, 1, updateCart);
    });

    $(document).on('click', '.cart__qty-btn--minus', function(e) {
        // если это кнопка внутри .shop-ferma__cart — выходим
        if ($(this).closest('.shop-ferma__cart').length) {
            return;
        }

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
        fermaShowAddToast($button);

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
    $(document.body).on('added_to_cart', function() {
        $('.single_add_to_cart_button').addClass('product-in-cart');
    });

    applyGrayStyle();

    // ==== КАРТОЧКА ОДИНОЧНОГО ТОВАРА (.shop-ferma__cart) ====

    function updateSingleProductPrice($form) {
        // .shop-ferma__cart — твой form
        var $priceEl = $form.find('.discount-offset').first();
        if (!$priceEl.length) return;

        var base       = Number($priceEl.data('price-base') || 0); // цена за 1 кг / 1 шт
        var ratio      = Number($priceEl.data('ratio') || 1);      // шаг веса (0.1, 0.5, 1)
        var isWeighted = String($priceEl.data('is-weighted')) === '1';

        var $qtyInput = $form.find('input.qty');
        var qty = parseFloat(String($qtyInput.val()).replace(',', '.')) || 1;

        var totalPrice, unitText;

        if (!isWeighted) {
            // штучный
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
    function updateSingleCart(productId, qty) {
        if (!singleCartKey) {
            console.warn('updateSingleCart: нет cart_item_key, не считаем товар лежащим в корзине');
            return;
        }

        $.ajax({
            type: 'POST',
            url: CartQtyData.ajax_url,
            dataType: 'json',
            data: {
                action:        'update_cart_qty',
                product_id:    productId,
                cart_item_key: singleCartKey, // ← ключ берём из hidden-поля
                qty:           qty,
                nonce:         CartQtyData.nonce
            },
            success: function(response) {
                console.log('updateSingleCart OK', response);
                var fragments = response.fragments || (response.data && response.data.fragments);
                if (!fragments) return;
                $.each(fragments, function(selector, html) {
                    $(selector).replaceWith(html);
                });
                syncSingleProductCartState();
            }
        });
    }



    function initSingleProductQty() {
        $('.shop-ferma__cart').each(function () {
            var $form      = $(this);
            var $qtyInput  = $form.find('input.qty');
            var $minusBtn  = $form.find('.cart__qty-btn--minus');
            var $plusBtn   = $form.find('.cart__qty-btn--plus');
            var $addBtn    = $form.find('.single_add_to_cart_button');

            if (!$qtyInput.length) return;

            var min = parseFloat($qtyInput.attr('min')) || 1;
            var max = parseFloat($qtyInput.attr('max')) || Infinity;

            function clamp(val) {
                if (val < min) val = min;
                if (val > max) val = max;
                return val;
            }

            function syncMinusState() {
                var val = parseFloat($qtyInput.val()) || min;
                if (val <= min) {
                    $minusBtn.addClass('is-disabled');
                } else {
                    $minusBtn.removeClass('is-disabled');
                }
            }

            function syncAddButtonQty() {
                if (!$addBtn.length) return;
                var productId = $addBtn.data('product_id') || $form.data('product_id');
                var qty       = parseFloat($qtyInput.val()) || min;

                $addBtn.data('quantity', qty);
                $addBtn.attr('data-quantity', qty);

                var href = $addBtn.attr('href');
                if (href && href.indexOf('add-to-cart=') !== -1 && productId) {
                    var baseUrl = href.split('?')[0];
                    var newUrl  = baseUrl + '?add-to-cart=' + productId + '&quantity=' + qty;
                    $addBtn.attr('href', newUrl);
                }
            }

            $plusBtn.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var val = parseFloat($qtyInput.val()) || min;
                val = clamp(val + 1);
                $qtyInput.val(val);

                syncMinusState();
                syncAddButtonQty();
                updateSingleProductPrice($form);

                var productId = $addBtn.data('product_id') || $form.data('product_id');

                // если есть cart_item_key → товар уже в корзине, шлём update
                if (singleCartKey) {
                    updateSingleCart(productId, val);
                }
            });

            $minusBtn.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if ($(this).hasClass('is-disabled')) return;

                var val = parseFloat($qtyInput.val()) || min;
                val = clamp(val - 1);
                $qtyInput.val(val);

                syncMinusState();
                syncAddButtonQty();
                updateSingleProductPrice($form);

                var productId = $addBtn.data('product_id') || $form.data('product_id');

                if (singleCartKey) {
                    updateSingleCart(productId, val);
                }
            });
            $qtyInput.on('change keyup', function () {
                var val = parseFloat($qtyInput.val()) || min;
                val = clamp(val);
                $qtyInput.val(val);

                syncMinusState();
                syncAddButtonQty();
                updateSingleProductPrice($form);
            });

            // Инициализация при загрузке
            var startVal = parseFloat($qtyInput.val()) || min;
            $qtyInput.val(clamp(startVal));
            syncMinusState();
            syncAddButtonQty();
            updateSingleProductPrice($form);
        }); // ← закрываем .each
    }

    // запускаем инициализацию одиночной карточки
    initSingleProductQty();
    function syncSingleProductCartState() {
        var $btn = $('.single_add_to_cart_button');
        if (!$btn.length) return;

        var productId = $btn.data('product_id');

        // пробегаем мини-корзину
        $('.woocommerce-mini-cart-item, .mini_cart_item').each(function () {
            var pid = $(this).data('product_id') ||
                $(this).find('[data-product_id]').data('product_id');

            if (pid == productId) {
                $btn.addClass('product-in-cart');
            }
        });
    }

    $(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function() {
        syncSingleProductCartState();
    });
    function syncSingleProductCartState() {
        var $btn = $('.single_add_to_cart_button');
        if (!$btn.length) return;

        var productId = $btn.data('product_id');
        if (!productId) return;

        var inCart = false;

        $('.woocommerce-mini-cart-item, .mini_cart_item').each(function () {
            var $item = $(this);
            var pid = $item.data('product_id') ||
                $item.find('[data-product_id]').data('product_id');

            if (pid == productId) {
                inCart = true;
                return false; // break
            }
        });

        if (inCart) {
            $btn
                .addClass('product-in-cart')
                .text('В корзине');
        } else {
            $btn
                .removeClass('product-in-cart')
                .text('В корзину');
        }
    }

    syncSingleProductCartState();

    $(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function () {
        syncSingleProductCartState();
    });

});
