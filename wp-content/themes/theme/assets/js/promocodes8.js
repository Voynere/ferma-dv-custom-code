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

                    requestMultiGifts(promoCode);

                } else {
                    // 1) Если бэкенд отдал готовый HTML WooCommerce
                    if (response.data && response.data.wc_html) {
                        $('.q-promo-message').html(response.data.wc_html);
                    } else if (response.data && response.data.message) {
                        // 2) Иначе – текстом в нашу всплывашку/блок
                        showMessage(response.data.message, 'error');
                    } else {
                        showMessage('Ошибка применения промокода', 'error');
                    }
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
// =============== MULTI-GIFT MODAL ===============

    function openGiftModal(data) {
        var gifts    = data.gifts || [];
        var gift_max = data.gift_max || 3;

        if (!gifts.length) return;

        var $overlay = $('<div class="q-gift-modal-overlay"></div>');
        var $modal   = $('<div class="q-gift-modal"></div>');
        var $title   = $('<h3 class="q-gift-title">Выберите подарки (до ' + gift_max + ')</h3>');
        var $list    = $('<div class="q-gift-list"></div>');
        var $close   = $('<button type="button" class="q-gift-close">Закрыть</button>');

        gifts.forEach(function(gift) {
            var $card = $('<div class="q-gift-card"></div>');
            if (gift.image) {
                $card.append('<div class="q-gift-img"><img src="' + gift.image + '" alt=""></div>');
            }
            $card.append('<div class="q-gift-name">' + gift.name + '</div>');
            if (gift.price) {
                $card.append('<div class="q-gift-price">' + gift.price + '</div>');
            }

            var $btn = $('<button type="button" class="q-gift-choose">Выбрать</button>');
            $btn.on('click', function() {
                $.post(
                    q_promo_vars.ajaxurl,
                    {
                        action:     'q_promo_add_gift',
                        nonce:      q_promo_vars.nonce,
                        product_id: gift.id
                    },
                    function(resp) {
                        if (!resp || !resp.success) {
                            showMessage(
                                resp?.data?.message || 'Ошибка при добавлении подарка',
                                'error'
                            );
                            return;
                        }

                        // фрагменты
                        if (resp.data && resp.data.fragments) {
                            try {
                                var fragments = JSON.parse(resp.data.fragments);
                                $.each(fragments, function(key, value) {
                                    $(key).replaceWith(value);
                                });
                            } catch(e) {
                                if (typeof resp.data.fragments === 'object') {
                                    $.each(resp.data.fragments, function(key, value) {
                                        $(key).replaceWith(value);
                                    });
                                }
                            }
                        }

                        showMessage('Подарок добавлен', 'success');
                    },
                    'json'
                );
            });

            $card.append($btn);
            $list.append($card);
        });

        $close.on('click', function() {
            $overlay.remove();
        });

        $modal.append($title).append($list).append($close);
        $overlay.append($modal);
        $('body').append($overlay);
    }

    function requestMultiGifts(promoCode) {
        $.post(
            q_promo_vars.ajaxurl,
            {
                action: 'q_promo_multi_list',
                nonce:  q_promo_vars.nonce,
                code:   promoCode
            },
            function(resp) {

                // Старый промокод → просто перезагружаем как раньше
                if (!resp || !resp.success) {
                    if (resp?.data?.code === 'not_multi') {
                        setTimeout(() => window.location.reload(), 1000);
                        return;
                    }

                    if (resp?.data?.message) {
                        showMessage(resp.data.message, 'error');
                    }

                    setTimeout(() => window.location.reload(), 1000);
                    return;
                }

                // Новый multi-подарочный промокод → показываем окно
                openGiftModal(resp.data);
            },
            'json'
        );
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
    $(document.body).on('checkout_error', function (event, data) {
        console.log('checkout_error fired', data);

        var html = data && data.messages ? data.messages : '';
        if (!html) return;

        var $tmp = $('<div>').html(html);
        var messages = [];

        // Берём текст из стандартных Woo-контейнеров
        $tmp.find('.woocommerce-error li, .woocommerce-error, .woocommerce-message, .woocommerce-info')
            .each(function () {
                var txt = $(this).text().trim();
                if (txt) {
                    messages.push(txt);
                }
            });

        // Если по каким-то причинам специфичные селекторы не сработали — берём общий текст
        if (!messages.length) {
            var fallback = $tmp.text().trim();
            if (fallback) {
                messages.push(fallback);
            }
        }

        if (!messages.length) return;

        // Каждую ошибку показываем через наш общий showMessage
        messages.forEach(function (msg) {
            showMessage(msg, 'error'); // внутри сам решит: тост или .q-promo-message
        });
    });

    checkActivePromo();
});
