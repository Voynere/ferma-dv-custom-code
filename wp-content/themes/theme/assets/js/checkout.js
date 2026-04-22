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
        var $scope = $('body.woocommerce-checkout');
        if (!$scope.length) {
            $scope = $('body');
        }
        return $scope
            .find(
                '.woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout, .woocommerce-NoticeGroup-checkout, .woocommerce-notices-wrapper'
            )
            .filter(function () {
                var $el = $(this);
                if ($el.closest('.ferma-checkout-inline-notices').length) {
                    return false;
                }
                var html = $el.html();
                if (!html || !$.trim(html)) {
                    return false;
                }
                return (
                    $el.find('.woocommerce-error, .woocommerce-message, .woocommerce-info, .woocommerce-notice').length > 0 ||
                    $el.hasClass('woocommerce-NoticeGroup-checkout')
                );
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
        var looksMinOrder =
            text.indexOf('минимальн') !== -1 ||
            text.indexOf('сумма корзины') !== -1 ||
            (text.indexOf('1000') !== -1 && (text.indexOf('корзин') !== -1 || text.indexOf('доставк') !== -1));
        if (!looksMinOrder) {
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

    function fermaSyncInlineNoticesAfterWcUpdate() {
        fermaApplyCompactPlaceholders();
        fermaBeautifyChangeAddressButton();
        // updated_checkout часто идёт после checkout_error; нельзя всегда очищать — иначе пропадают ошибки и мин. сумма.
        setTimeout(function () {
            if (fermaCheckoutNoticeSources().length) {
                fermaShowInlineFromCheckoutNotices();
            } else {
                fermaClearInlineNotices();
            }
        }, 0);
    }

    $(document.body).on('checkout_error', function () {
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
        fermaSyncInlineNoticesAfterWcUpdate();
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
        // POST после ошибки валидации: WC отрисовал notices в DOM — дублируем у кнопки, как при AJAX.
        setTimeout(function () {
            fermaShowInlineFromCheckoutNotices();
        }, 0);
    });

    // Сохраняем позицию перед сабмитом, но не ломаем штатную цепочку WooCommerce checkout.
    $(document).on('click', '#place_order', function () {
        fermaCheckoutLastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    });
    $(document).on('submit', 'form.checkout', function () {
        fermaCheckoutLastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    });
})(jQuery);
