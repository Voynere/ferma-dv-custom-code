(function() {
    tinymce.PluginManager.add('contents_button', function(editor) {
        editor.addButton('contents_button', {
            text: 'Создать содержание',
            icon: false,
            onclick: function() {
                // Получаем контент редактора
                const content = editor.getContent();
                
                // Создаем временный контейнер для парсинга
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = content;
                
                // Ищем все h2
                const h2Elements = tempDiv.querySelectorAll('h2');
                if (h2Elements.length === 0) {
                    alert('В статье нет заголовков H2!');
                    return;
                }
                
                // Создаем HTML содержания
                let contentsHtml = '<div class="post-contents">';
                contentsHtml += '<h3 class="post-contents__title">СОДЕРЖАНИЕ СТАТЬИ</h3>';
                contentsHtml += '<ul class="post-contents__list">';
                
                h2Elements.forEach((h2, index) => {
                    // Генерируем ID если нужно
                    if (!h2.id) {
                        h2.id = 'section-' + (index + 1) + '-' + h2.textContent
                            .toLowerCase()
                            .replace(/[^\wа-яё]+/gi, '-')
                            .replace(/^-+|-+$/g, '');
                    }
                    
                    // Добавляем пункт меню
                    contentsHtml += `<li><a class="post-contents__link" href="#${h2.id}">${h2.textContent}</a></li>`;
                });
                contentsHtml += '</ul></div>';
                
                // Вставляем содержание после первого h1
                const h1 = tempDiv.querySelector('h1');
                if (h1) {
                    h1.insertAdjacentHTML('afterend', contentsHtml);
                } else {
                    // Если нет h1, вставляем в начало
                    tempDiv.insertAdjacentHTML('afterbegin', contentsHtml);
                }
                
                // Обновляем контент редактора
                editor.setContent(tempDiv.innerHTML);
                
                editor.notificationManager.open({
                    text: 'Содержание успешно добавлено!',
                    type: 'info',
                    timeout: 3000
                });
            }
        });
    });
})();