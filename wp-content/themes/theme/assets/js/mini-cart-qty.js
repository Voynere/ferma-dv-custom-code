(function () {
    document.addEventListener('click', function (e) {
        // 1) работаем и в мини-корзине, и в оформлении заказа
        const scope = e.target.closest('.cart__container, .ferma-checkout__form-table');
        if (!scope) {
            return;
        }

        const plusBtn  = e.target.closest('.cart__qty-btn--plus');
        const minusBtn = e.target.closest('.cart__qty-btn--minus');

        if (plusBtn && !plusBtn.classList.contains('is-disabled')) {
            e.preventDefault();
            updateCartQuantity(plusBtn, 'increase');
            return;
        }

        if (minusBtn) {
            e.preventDefault();
            updateCartQuantity(minusBtn, 'decrease');
            return;
        }
    });

    function updateCartQuantity(button, action) {
        const qtyWrap     = button.closest('.cart__qty');
        if (!qtyWrap) return;

        const qtyElement  = qtyWrap.querySelector('.cart__qty-val');
        const cartItemKey =
            qtyWrap.getAttribute('data-cart_item_key') ||
            qtyWrap.getAttribute('data-cart-item-key') ||
            '';
        const productId =
            qtyWrap.getAttribute('data-product_id') ||
            qtyWrap.getAttribute('data-product-id') ||
            '';

        const ratio = parseFloat(qtyWrap.dataset.weight_ratio || '1');

        var rawSteps = parseFloat(qtyWrap.dataset.steps || '0');
        var currentQty = parseFloat((qtyWrap.dataset.current_qty || '0').toString().replace(',', '.'));
        let steps = Math.round(rawSteps);
        if (!Number.isFinite(steps) || steps < 1) {
            if (Number.isFinite(currentQty) && currentQty > 0) {
                steps = ratio < 1 ? Math.max(1, Math.round(currentQty / ratio)) : Math.max(1, Math.round(currentQty));
            } else {
                steps = 1;
            }
        }

        if (action === 'increase') {
            steps += 1;
        } else if (action === 'decrease') {
            steps -= 1;
            if (steps < 1) {
                steps = 1;
            }
        }

        let displayQty;
        if (ratio < 1) {
            displayQty = steps * ratio;
            displayQty = Number(displayQty.toFixed(1));
        } else {
            displayQty = steps;
        }
        qtyWrap.dataset.steps = String(steps);
        qtyWrap.dataset.current_qty = String(displayQty);

        qtyElement.textContent = String(displayQty).replace('.', ',');

        // Для весовых в корзине храним "шаги" (3 => 0.3 кг при ratio=0.1), для остальных — фактическое количество.
        var qtyForCart = ratio < 1 ? steps : displayQty;
        updateCartViaAjax(cartItemKey, qtyForCart, productId);
    }

    function updateCartViaAjax(cartItemKey, quantity, productId) {
        const payload = {
            action: 'update_cart_qty',
            cart_item_key: cartItemKey,
            qty: quantity,
            nonce: CartQtyData.nonce
        };
        if (productId) {
            payload.product_id = productId;
        }

        if (qtyWrapHasPending(cartItemKey)) {
            return;
        }
        setQtyWrapPending(cartItemKey, true);

        jQuery.ajax({
            type: 'POST',
            url: CartQtyData.ajax_url,
            dataType: 'json',
            data: payload
        })
            .done(function (data) {
                if (data && data.fragments) {
                    // 2) применяем ВСЕ фрагменты, которые вернул сервер
                    Object.keys(data.fragments).forEach(function (selector) {
                        var html = data.fragments[selector];
                        var $el  = jQuery(selector);
                        if ($el.length) {
                            $el.replaceWith(html);
                        }
                    });

                    // 3) просим Woo пересчитать checkout; удерживаем скролл (WC иначе тянет к notice сверху).
                    var scrollY =
                        window.pageYOffset ||
                        document.documentElement.scrollTop ||
                        0;
                    jQuery(document.body).one('updated_checkout', function () {
                        window.requestAnimationFrame(function () {
                            window.scrollTo(0, scrollY);
                        });
                    });
                    jQuery(document.body).trigger('update_checkout');
                    if (typeof window.wc_checkout_form !== 'undefined' &&
                        typeof window.wc_checkout_form.update_checkout === 'function') {
                        window.wc_checkout_form.update_checkout();
                    }
                    refreshCheckoutPanels();
                } else {
                    // Даже если фрагменты не пришли, просим Woo пересчитать итоги checkout.
                    jQuery(document.body).trigger('update_checkout');
                    if (typeof window.wc_checkout_form !== 'undefined' &&
                        typeof window.wc_checkout_form.update_checkout === 'function') {
                        window.wc_checkout_form.update_checkout();
                    }
                    refreshCheckoutPanels();
                }
            })
            .fail(function (error) {
                console.error('Mini/checkout cart qty AJAX error:', error);
            })
            .always(function () {
                setQtyWrapPending(cartItemKey, false);
            });
    }

    function refreshCheckoutPanels() {
        var $checkoutForm = jQuery('form.checkout');
        if (!$checkoutForm.length) {
            return;
        }
        if (typeof wc_checkout_params === 'undefined' || !wc_checkout_params.wc_ajax_url) {
            return;
        }
        var endpoint = wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_order_review');
        var payload = {
            security: wc_checkout_params.update_order_review_nonce || '',
            post_data: $checkoutForm.serialize()
        };
        jQuery.ajax({
            type: 'POST',
            url: endpoint,
            data: payload,
            dataType: 'json'
        }).done(function (res) {
            if (!res || !res.fragments) {
                return;
            }
            Object.keys(res.fragments).forEach(function (selector) {
                var html = res.fragments[selector];
                var $el = jQuery(selector);
                if ($el.length) {
                    $el.replaceWith(html);
                }
            });
            jQuery(document.body).trigger('updated_checkout', [res]);
        });
    }

    function qtyWrapHasPending(cartItemKey) {
        if (!cartItemKey) {
            return false;
        }
        var wrap = document.querySelector('.cart__qty[data-cart_item_key="' + cartItemKey + '"]');
        return !!(wrap && wrap.dataset.pending === '1');
    }

    function setQtyWrapPending(cartItemKey, pending) {
        if (!cartItemKey) {
            return;
        }
        var wrap = document.querySelector('.cart__qty[data-cart_item_key="' + cartItemKey + '"]');
        if (!wrap) {
            return;
        }
        wrap.dataset.pending = pending ? '1' : '0';
    }
})();
