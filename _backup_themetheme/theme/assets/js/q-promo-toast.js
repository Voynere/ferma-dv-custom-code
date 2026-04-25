(function ($) {

    // Добавляем стили один раз
    function injectPromoToastStyles() {
        if (document.getElementById('q-promo-toast-styles')) return;

        var style = document.createElement('style');
        style.id = 'q-promo-toast-styles';
        style.type = 'text/css';
        style.innerHTML = `
            .q-promo-toast-container {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .q-promo-toast {
                min-width: 260px;
                max-width: 340px;
                padding: 12px 16px;
                border-radius: 6px;
                font-size: 14px;
                line-height: 1.4;
                color: #fff;
                box-shadow: 0 6px 18px rgba(0,0,0,0.25);
                opacity: 0;
                transform: translateY(10px);
                transition: opacity .2s ease-out, transform .2s ease-out;
            }

            .q-promo-toast--visible {
                opacity: 1;
                transform: translateY(0);
            }

            .q-promo-toast--error {
                background: #d93025; /* насыщенный красный */
            }

            .q-promo-toast--success {
                background: #34a853; /* зелёный на случай успеха */
            }
        `;
        document.head.appendChild(style);
    }

    function getContainer() {
        var container = document.querySelector('.q-promo-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'q-promo-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Показываем всплывашку
     * @param {string} message - текст сообщения
     * @param {string} type - 'error' или 'success'
     * @param {number} timeoutMs - таймаут в мс
     */
    function showPromoToast(message, type, timeoutMs) {
        injectPromoToastStyles();

        type = type || 'error';
        timeoutMs = timeoutMs || 4000;

        var container = getContainer();

        var toast = document.createElement('div');
        toast.className = 'q-promo-toast q-promo-toast--' + type;
        toast.textContent = message || 'Ошибка';

        container.appendChild(toast);

        // небольшая задержка, чтобы сработала анимация
        requestAnimationFrame(function () {
            toast.classList.add('q-promo-toast--visible');
        });

        setTimeout(function () {
            toast.classList.remove('q-promo-toast--visible');
            setTimeout(function () {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 200);
        }, timeoutMs);
    }

    // Делаем функцию глобальной
    window.qPromoShowToast = showPromoToast;

})(jQuery);
