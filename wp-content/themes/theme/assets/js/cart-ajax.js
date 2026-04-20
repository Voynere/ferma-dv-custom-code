(function(){
    // Получаем URL для POST: заменяем %%endpoint%% на наше действие
    function getAjaxUrl() {
        return themeMiniCartAjax.ajax_url
               .replace('%%endpoint%%', themeMiniCartAjax.remove_endpoint);
    }

    // Функция удаления по ключу
    function removeCartItem(cartKey) {
        var url = getAjaxUrl();
        var formData = new FormData();
        formData.append('cart_item_key', cartKey);

        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response){
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        });
    }

    // Обработчик клика по кнопке .cart__delete
    document.addEventListener('click', function(e){
        var btn = e.target.closest('.cart__delete');
        if (!btn) return;

        e.preventDefault();
        var cartKey = btn.getAttribute('data-cart_item_key');
        if (!cartKey) return;

        // Можно показать какое-то состояние загрузки
        btn.classList.add('is-loading');

        removeCartItem(cartKey)
        .then(function(data){
            if (data.fragments) {
                // data.fragments — объект { селектор: html }
                Object.keys(data.fragments).forEach(function(selector){
                    var html = data.fragments[selector];
                    document.querySelectorAll(selector).forEach(function(node){
                        // Заменяем каждый фрагмент
                        var temp = document.createElement('div');
                        temp.innerHTML = html;
                        node.parentNode.replaceChild(
                            temp.firstElementChild, node
                        );
                    });
                });
            }
        })
        .catch(function(err){
            console.error('Remove cart item failed:', err);
        })
        .finally(function(){
            btn.classList.remove('is-loading');
        });
    });
})();
