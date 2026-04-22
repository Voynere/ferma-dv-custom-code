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
        const cartItemKey = qtyWrap.dataset.cart_item_key;

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

        // в корзину шлём КОЛ-ВО ШАГОВ (целое)
        updateCartViaAjax(cartItemKey, steps);
    }

    function updateCartViaAjax(cartItemKey, quantity) {
        const data = new FormData();
        data.append('action', 'update_cart_qty');
        data.append('cart_item_key', cartItemKey);
        data.append('qty', quantity);
        data.append('nonce', CartQtyData.nonce);

        if (qtyWrapHasPending(cartItemKey)) {
            return;
        }
        setQtyWrapPending(cartItemKey, true);

        fetch(CartQtyData.ajax_url, {
            method: 'POST',
            body: data
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
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
                } else {
                    // Даже если фрагменты не пришли, просим Woo пересчитать итоги checkout.
                    jQuery(document.body).trigger('update_checkout');
                }
            })
            .catch(function (error) {
                console.error('Mini/checkout cart qty AJAX error:', error);
            })
            .finally(function () {
                setQtyWrapPending(cartItemKey, false);
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
