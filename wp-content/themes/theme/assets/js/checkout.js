(function ($) {
    'use strict';

    function fermaInlineNotices() {
        return $('.ferma-checkout-inline-notices');
    }

    function fermaShowInlineFromCheckoutNotices() {
        var $target = fermaInlineNotices();
        if (!$target.length) {
            return;
        }
        var $group = $('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout');
        if (!$group.length) {
            return;
        }
        var html = $group.html();
        if (!html || !html.trim()) {
            return;
        }
        $target.html(html).addClass('is-visible');
        $group.hide();
    }

    function fermaClearInlineNotices() {
        var $target = fermaInlineNotices();
        if ($target.length) {
            $target.empty().removeClass('is-visible');
        }
        $('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout').show();
    }

    $(document).on('checkout_error', function () {
        setTimeout(function () {
            fermaShowInlineFromCheckoutNotices();

            $('.checkout-inline-error-message').each(function () {
                var errorText = $(this).text();
                $(this).text(errorText.replace('Платежи ', ''));
            });
        }, 1);
    });

    $(document.body).on('updated_checkout', function () {
        fermaClearInlineNotices();
    });

    function fermaEnsureStockModal() {
        if (document.getElementById('ferma-stock-modal-overlay')) {
            return;
        }
        var el = document.createElement('div');
        el.id = 'ferma-stock-modal-overlay';
        el.className = 'ferma-stock-modal-overlay';
        el.style.display = 'none';
        el.innerHTML =
            '<div class="ferma-stock-modal" role="dialog" aria-modal="true">' +
            '<h3 id="ferma-stock-modal-title">Недостаточно товара на складе</h3>' +
            '<p id="ferma-stock-modal-text"></p>' +
            '<div class="ferma-stock-modal-actions">' +
            '<button type="button" class="ferma-stock-remove">Удалить из корзины</button>' +
            '<button type="button" class="ferma-stock-confirm">Пересчитать и продолжить</button>' +
            '</div></div>';
        document.body.appendChild(el);
    }

    function fermaOpenStockModal(text, onApply, onRemove) {
        fermaEnsureStockModal();
        var overlay = document.getElementById('ferma-stock-modal-overlay');
        if (!overlay) {
            return;
        }
        var p = overlay.querySelector('#ferma-stock-modal-text');
        if (p) {
            p.textContent = text;
        }
        overlay.style.display = 'flex';
        var btnApply = overlay.querySelector('.ferma-stock-confirm');
        var btnRemove = overlay.querySelector('.ferma-stock-remove');
        function close() {
            overlay.style.display = 'none';
            if (btnApply) {
                btnApply.onclick = null;
            }
            if (btnRemove) {
                btnRemove.onclick = null;
            }
        }
        if (btnApply) {
            btnApply.onclick = function () {
                close();
                onApply();
            };
        }
        if (btnRemove) {
            btnRemove.onclick = function () {
                close();
                onRemove();
            };
        }
        overlay.onclick = function (e) {
            if (e.target === overlay) {
                close();
            }
        };
    }

    function fermaStockAjax(action, done) {
        if (typeof fermaCheckout === 'undefined') {
            done(null);
            return;
        }
        $.post(
            fermaCheckout.ajaxUrl,
            {
                action: action,
                nonce: fermaCheckout.nonce,
            },
            function (res) {
                done(res);
            }
        ).fail(function () {
            done(null);
        });
    }

    document.addEventListener(
        'click',
        function (ev) {
            var t = ev.target;
            if (!t || t.id !== 'place_order') {
                return;
            }
            var form = t.closest('form.checkout');
            if (!form) {
                return;
            }
            if (window.__fermaStockGateOk) {
                window.__fermaStockGateOk = false;
                return;
            }
            ev.preventDefault();
            ev.stopPropagation();
            if (typeof ev.stopImmediatePropagation === 'function') {
                ev.stopImmediatePropagation();
            }

            fermaStockAjax('ferma_checkout_stock_check', function (res) {
                if (!res || !res.success || !res.data) {
                    window.__fermaStockGateOk = true;
                    t.click();
                    return;
                }
                var issues = res.data.issues || [];
                if (!issues.length) {
                    window.__fermaStockGateOk = true;
                    t.click();
                    return;
                }
                var first = issues[0];
                var availText =
                    first.ratio && first.ratio > 0 && first.ratio < 1
                        ? String(first.available).replace('.', ',') + ' кг'
                        : String(Math.floor(first.available));
                var msg =
                    '«' +
                    first.name +
                    '» в наличии ' +
                    availText +
                    ', в корзине больше. Пересчитать количества и продолжить оформление?';

                fermaOpenStockModal(
                    msg,
                    function () {
                        fermaStockAjax('ferma_checkout_stock_apply', function (applyRes) {
                            if (applyRes && applyRes.success) {
                                $(document.body).trigger('update_checkout');
                                setTimeout(function () {
                                    window.__fermaStockGateOk = true;
                                    var btn = document.getElementById('place_order');
                                    if (btn) {
                                        btn.click();
                                    }
                                }, 400);
                            } else {
                                $(document.body).trigger('update_checkout');
                            }
                        });
                    },
                    function () {
                        var key = first.cart_item_key;
                        if (key && typeof wc_checkout_params !== 'undefined') {
                            $.ajax({
                                type: 'POST',
                                url: wc_checkout_params.wc_ajax_url
                                    .toString()
                                    .replace('%%endpoint%%', 'remove_from_cart'),
                                data: { cart_item_key: key },
                                complete: function () {
                                    $(document.body).trigger('update_checkout');
                                },
                            });
                        } else {
                            $(document.body).trigger('update_checkout');
                        }
                    }
                );
            });
        },
        true
    );
})(jQuery);
