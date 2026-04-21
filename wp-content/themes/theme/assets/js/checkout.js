(function ($) {
    'use strict';
    var fermaCheckoutLastScrollY = 0;
    var fermaOriginalScrollToNotices = null;

    function fermaInlineNotices() {
        return $('.ferma-checkout-inline-notices');
    }

    function fermaInlineNoticesBody() {
        var $w = fermaInlineNotices();
        var $b = $w.find('.ferma-checkout-inline-notices__body');
        return $b.length ? $b : $w;
    }

    function fermaCheckoutNoticeSources() {
        return $('#order_review, form.checkout')
            .find('.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout, .woocommerce-NoticeGroup-checkout')
            .filter(function () {
                return !$(this).closest('.ferma-checkout-inline-notices').length;
            });
    }

    function fermaRenderMinOrderNotice() {
        var shopUrl =
            typeof fermaCheckout !== 'undefined' && fermaCheckout.shopUrl
                ? fermaCheckout.shopUrl
                : '/shop/';
        return (
            '<div class="ferma-checkout-min-order">' +
            '<p>Минимальный заказ на доставку от 1000 руб, добавьте в корзине количество или перейдите в каталог и добавьте еще что-то.</p>' +
            '<div class="ferma-checkout-min-order__actions">' +
            '<a class="ferma-checkout-min-order__link" href="' +
            shopUrl +
            '">Продолжить покупки</a>' +
            '<button type="button" class="ferma-checkout-min-order__stay">Остаться в корзине</button>' +
            '</div>' +
            '</div>'
        );
    }

    function fermaMaybeReplaceMinOrderNotice() {
        var $body = fermaInlineNoticesBody();
        if (!$body.length) {
            return;
        }
        var text = $body.text().toLowerCase();
        if (text.indexOf('минимальная сумма корзины') === -1 && text.indexOf('минимальный заказ') === -1) {
            return;
        }
        $body.html(fermaRenderMinOrderNotice());
    }

    function fermaApplyCompactPlaceholders() {
        // Возвращаем классические подписи полей снаружи:
        // no inline placeholders as labels.
        $('form.checkout .form-row.ferma-inline-label').removeClass('ferma-inline-label');
        $('form.checkout .form-row.ferma-label-gap').removeClass('ferma-label-gap');
    }

    function fermaBeautifyChangeAddressButton() {
        var $field = $('#billing_delivery_field');
        if (!$field.length) {
            return;
        }
        var $link = $field.find('a').filter(function () {
            return $(this).text().toLowerCase().indexOf('изменить адрес') !== -1;
        }).first();
        if (!$link.length) {
            return;
        }
        $field.addClass('ferma-delivery-address-field');
        $link.addClass('ferma-delivery-address-edit');
    }

    function fermaShowInlineFromCheckoutNotices() {
        var $target = fermaInlineNotices();
        if (!$target.length) {
            return;
        }
        var $groups = fermaCheckoutNoticeSources();
        if (!$groups.length) {
            return;
        }
        var chunks = [];
        $groups.each(function () {
            var h = $(this).html();
            if (h && h.trim()) {
                chunks.push(h);
            }
        });
        var html = chunks.join('');
        if (!html || !html.trim()) {
            return;
        }
        fermaInlineNoticesBody().html(html);
        fermaMaybeReplaceMinOrderNotice();
        $target.addClass('is-visible');
        $groups.hide();
    }

    function fermaPatchWooNoticeScroll() {
        if (typeof $.scroll_to_notices !== 'function') {
            return false;
        }
        if (fermaOriginalScrollToNotices) {
            return true;
        }
        fermaOriginalScrollToNotices = $.scroll_to_notices;
        $.scroll_to_notices = function (scrollElement) {
            // Отключаем авто-скролл WooCommerce: ошибки показываем в нашем попапе у кнопки.
            return scrollElement;
        };
        return true;
    }

    function fermaClearInlineNotices() {
        var $target = fermaInlineNotices();
        if ($target.length) {
            $target.find('.ferma-checkout-inline-notices__body').empty();
            $target.removeClass('is-visible');
        }
        fermaCheckoutNoticeSources().show();
    }

    $(document).on('click', '.ferma-checkout-inline-notices__close', function (e) {
        e.preventDefault();
        fermaClearInlineNotices();
    });

    $(document).on('click', '.ferma-checkout-min-order__stay', function (e) {
        e.preventDefault();
        fermaClearInlineNotices();
    });

    $(document).on('checkout_error', function () {
        // После рабочего патча scroll_to_notices позиция ещё не сброшена; иначе берём запас с place_order.
        var keepY =
            window.pageYOffset ||
            document.documentElement.scrollTop ||
            fermaCheckoutLastScrollY ||
            0;
        setTimeout(function () {
            fermaShowInlineFromCheckoutNotices();
            // WooCommerce по умолчанию скроллит к notice-блоку; возвращаем позицию,
            // чтобы пользователь оставался у кнопки оформления и видел наш попап.
            window.scrollTo(0, keepY);
            setTimeout(function () {
                window.scrollTo(0, keepY);
            }, 80);

            $('.checkout-inline-error-message').each(function () {
                var errorText = $(this).text();
                $(this).text(errorText.replace('Платежи ', ''));
            });
        }, 1);
    });

    $(document.body).on('updated_checkout', function () {
        fermaClearInlineNotices();
        fermaApplyCompactPlaceholders();
        fermaBeautifyChangeAddressButton();
    });

    $(function () {
        var patchTries = 0;
        var patchTimer = setInterval(function () {
            if (fermaPatchWooNoticeScroll() || ++patchTries > 40) {
                clearInterval(patchTimer);
            }
        }, 50);
        $(document.body).on('init_checkout', function () {
            fermaPatchWooNoticeScroll();
        });
        fermaApplyCompactPlaceholders();
        fermaBeautifyChangeAddressButton();
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
            fermaCheckoutLastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
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
