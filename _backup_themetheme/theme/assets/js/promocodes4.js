jQuery(document).ready(function($) {
    'use strict';

    var $input      = $('#promo_code');
    var $btnApply   = $('#promo_code_add');
    var $btnRemove  = $('#promo_code_remove');
    var $msgBox     = $('.q-promo-message'); // блок под инпутом

    // Применение промокода
    $(document).on('click', '#promo_code_add', function(e) {
        e.preventDefault();
        applyQPromoCode();
    });

    // Удаление промокода
    $(document).on('click', '#promo_code_remove', function(e) {
        e.preventDefault();
        removeQPromoCode();
    });

    // Enter в поле промокода
    $input.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            applyQPromoCode();
        }
    });

    function applyQPromoCode() {
        var promoCode = $input.val().trim();

        if (!promoCode) {
            showMessage('Введите промокод', 'error');
            return;
        }

        var originalText = $btnApply.text();
        $btnApply.prop('disabled', true).text('Применяем...');

        $.ajax({
            url: q_promo_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'apply_q_promocode',
                promo_code: promoCode,
                nonce: q_promo_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    // обновляем фрагменты
                    if (response.data.fragments) {
                        $.each(response.data.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    $(document.body).trigger('wc_fragments_refreshed');
                    $(document.body).trigger('update_checkout');

                    if (response.data.cart_contents_count !== undefined) {
                        $('.cart-count, .cart-contents-count')
                            .text(response.data.cart_contents_count);
                    }

                    if (response.data.cart_total) {
                        $('.cart-total, .amount').filter(function() {
                            return $(this).text().indexOf('₽') !== -1;
                        }).text(response.data.cart_total);
                    }

                    showMessage(response.data.message, 'success');

                    $input.val(promoCode).prop('disabled', true);
                    $btnApply.hide();
                    $btnRemove.show();

                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showMessage(response.data.message, 'error');
                }
            },
            error: function() {
                showMessage('Ошибка сервера. Попробуйте позже.', 'error');
            },
            complete: function() {
                $btnApply.prop('disabled', false).text(originalText);
            }
        });
    }

    function removeQPromoCode() {
        var originalText = $btnRemove.text();
        $btnRemove.prop('disabled', true).text('Удаляем...');

        $.ajax({
            url: q_promo_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'remove_q_promocode',
                nonce: q_promo_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.fragments) {
                        $.each(response.data.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    $(document.body).trigger('wc_fragments_refreshed');
                    $(document.body).trigger('update_checkout');

                    showMessage(response.data.message, 'success');

                    $input.val('').prop('disabled', false);
                    $btnApply.show();
                    $btnRemove.hide();
                } else {
                    showMessage(response.data.message, 'error');
                }
            },
            error: function() {
                showMessage('Ошибка удаления. Попробуйте позже.', 'error');
            },
            complete: function() {
                $btnRemove.prop('disabled', false).text(originalText);
            }
        });
    }

    function showMessage(message, type) {
        var level = (type === 'success') ? 'success' : 'error';

        if (window.qPromoShowToast) {
            // наша всплывашка
            qPromoShowToast(message, level);
        } else {
            // запасной вариант — сообщение под инпутом
            var messageClass = (level === 'success') ? 'promo-success' : 'promo-error';
            var icon = (level === 'success') ? '✓' : '✗';

            $msgBox.html(
                '<div class="' + messageClass + '">' + icon + ' ' + message + '</div>'
            );

            setTimeout(function() {
                $msgBox.empty();
            }, 5000);
        }
    }

    // Проверка активного промокода при загрузке
    function checkActivePromo() {
        $.ajax({
            url: q_promo_vars.ajaxurl,
            type: 'POST',
            data: {
                action: 'check_active_promo',
                nonce: q_promo_vars.nonce
            },
            success: function(response) {
                if (response.success && response.data.active_promo) {
                    var promoCode = response.data.promo_code;

                    $input.val(promoCode).prop('disabled', true);
                    $btnApply.hide();
                    $btnRemove.show();

                    showMessage('Применен промокод: ' + promoCode, 'success');
                }
            }
        });
    }

    // Ловим все ошибки checkout (WP_Error -> wc_add_notice -> checkout_error)
    $(document.body).on('checkout_error', function () {
        if (typeof qPromoShowToast !== 'function') {
            return;
        }

        // стандартный контейнер ошибок WooCommerce
        var $group = $('.woocommerce-NoticeGroup-checkout, .woocommerce-error');

        if (!$group.length) {
            return;
        }

        var messages = [];

        // собираем <li> если это список ошибок
        $group.find('li').each(function () {
            var txt = $(this).text().trim();
            if (txt) {
                messages.push(txt);
            }
        });

        // если <li> нет, берём текст целиком
        if (!messages.length) {
            var txt = $group.text().trim();
            if (txt) {
                messages.push(txt);
            }
        }

        // показываем каждую ошибку отдельным тостом
        messages.forEach(function (msg) {
            qPromoShowToast(msg, 'error');
        });
    });

    checkActivePromo();
});
