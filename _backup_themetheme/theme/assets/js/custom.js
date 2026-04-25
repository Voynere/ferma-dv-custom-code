document.addEventListener('DOMContentLoaded', function() {
    // Проверка и блокировка добавления в корзину
    function initCartValidation() {
        const addToCartButtons = document.querySelectorAll('.single_add_to_cart_button, .ajax_add_to_cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const productId = this.getAttribute('data-product_id');
                const quantityInput = document.querySelector('.quantity input.qty');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

                // Если кнопка уже заблокирована, отменяем действие
                if (this.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                checkStockBeforeAdd(productId, quantity, e);
            });
        });
    }

    // Проверка наличия перед добавлением
    function checkStockBeforeAdd(productId, quantity, event) {
        // Используем данные которые уже есть на странице
        const stockElement = document.querySelector('.stock');
        const maxQuantity = document.querySelector('input.qty')?.max;

        if (stockElement && stockElement.textContent.includes('Нет в наличии')) {
            event.preventDefault();
            event.stopPropagation();
            showStockError('Товар отсутствует на складе');
            return false;
        }

        if (maxQuantity && quantity > parseInt(maxQuantity)) {
            event.preventDefault();
            event.stopPropagation();
            showStockError(`Максимально доступное количество: ${maxQuantity}`);
            return false;
        }

        // Если данных нет, делаем AJAX запрос
        fetch('/wp-json/wc/v3/products/' + productId)
            .then(response => response.json())
            .then(product => {
                const stockQuantity = product.stock_quantity;
                const isInStock = product.stock_status === 'instock';

                if (!isInStock || (stockQuantity && stockQuantity < quantity)) {
                    event.preventDefault();
                    event.stopPropagation();
                    showStockError(product.name, stockQuantity);
                }
            })
            .catch(error => {
                console.log('Не удалось проверить наличие');
                // Продолжаем стандартное поведение
            });
    }

    function showStockError(productName, availableStock) {
        const message = availableStock > 0
            ? `Доступно только ${availableStock} шт. товара "${productName}"`
            : `Товар "${productName}" отсутствует на складе`;

        // Показываем сообщение WooCommerce
        if (typeof wc_add_notice === 'function') {
            wc_add_notice(message, 'error');
        } else {
            alert(message);
        }
    }

    // Инициализация
    initCartValidation();
});