фjQuery(function ($) {
    'use strict';

    // создаём один общий див для всплывашек
    var $toast = $('<div class="q-promo-toast"></div>').appendTo('body').hide();
    var hideTimer = null;

    // глобальная функция, которую вызывает твой основной скрипт
    window.qPromoShowToast = function (message, level) {
        level = level || 'error'; // 'success' или 'error'

        // классы для цветов
        $toast
            .removeClass('q-promo-toast--success q-promo-toast--error')
            .addClass(level === 'success' ? 'q-promo-toast--success' : 'q-promo-toast--error')
            .text(message);

        // показываем
        $toast.stop(true, true).fadeIn(200);

        // авто-скрытие через 4 сек
        if (hideTimer) {
            clearTimeout(hideTimer);
        }
        hideTimer = setTimeout(function () {
            $toast.fadeOut(200);
        }, 4000);
    };
});
