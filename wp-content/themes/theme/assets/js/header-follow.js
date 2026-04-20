// header follow
document.addEventListener('DOMContentLoaded', function () {

    const follow = document.querySelector('.header__follow');
    let lastScroll = 0;
    const screenHeight = 350;

    function toggleFollowHeader() {
        const scrollTop = window.scrollY || window.pageYOffset;

        // Если прокрутка больше 1 высоты экрана → показываем
        if (scrollTop > screenHeight) {
            if (!follow.classList.contains('is-visible')) {
                follow.classList.add('is-visible');
            }
        }
        // Меньше — скрываем
        else {
            if (follow.classList.contains('is-visible')) {
                follow.classList.remove('is-visible');
            }
        }

        lastScroll = scrollTop;
    }

    // вызываем 1 раз
    toggleFollowHeader();
    window.addEventListener('scroll', toggleFollowHeader);
});

// Кнопка для строки поиска - переключение
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.querySelector('.header__search-to-open');
    const searchBox = document.querySelector('.header__search-content');
    const iconSearch = document.querySelector('.header-follow-search-icon');
    const iconClose = document.querySelector('.header-follow-close-icon');

    function openSearch() {
        searchBox.classList.add('active');
        iconSearch.classList.toggle('active');
        iconClose.classList.toggle('active');
    }

    function closeSearch() {
        searchBox.classList.remove('active');
        iconSearch.classList.toggle('active');
        iconClose.classList.toggle('active');
    }

    function isSearchOpen() {
        return searchBox.classList.contains('active');
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();

        if (!isSearchOpen()) {
            openSearch();
        } else {
            closeSearch();
        }
    });
});

// Каталог для follow header (открытие/закрытие и перемещение навигации)
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('catalog-follow-btn');
    const menu = document.getElementById('catalog-menu-desc');
    const overlay = document.querySelector('.overlay');
    const targetContainer = document.querySelector('.header__follow-inner');

    if (!btn || !menu || !targetContainer) return;

    // Запоминаем исходный родитель и следующий элемент
    const originalParent = menu.parentNode;
    const originalNextSibling = menu.nextElementSibling;

    let isMoved = false;

    function openMenu() {
        if (isMoved) return;

        // Перемещаем меню
        targetContainer.appendChild(menu);

        // Добавляем класс
        menu.classList.add('catalog-follow');

        isMoved = true;
    }

    function closeMenu() {
        if (!isMoved) return;

        // Возвращаем в исходное место
        if (originalNextSibling) {
            originalParent.insertBefore(menu, originalNextSibling);
        } else {
            originalParent.appendChild(menu);
        }

        // Удаляем служебный класс
        menu.classList.remove('catalog-follow');

        isMoved = false;
    }

    // Клик по кнопке
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!isMoved) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    // Клик по overlay закрывает меню
    if (overlay) {
        overlay.addEventListener('click', function () {
            closeMenu();
        });
    }

});

// Перемещение логотипа
document.addEventListener('DOMContentLoaded', function () {
    function changeTemplate() {
        const w = window.innerWidth;
        const logo = document.querySelector('.header__follow .header__logo');
        let target = null;

        if (w <= 950) {
            target = document.querySelector('.header__follow .header__follow-top');
        } else if (w <= 1200) {
            target = document.querySelector('.header__follow .header__desktop-menu');
        } else {
            target = document.querySelector('.header__follow .header__follow-inner');
        }

        // если целевой контейнер найден и логотип там ещё не находится — переместить
        if (target && logo.parentElement !== target) {
            target.prepend(logo);
        }
    }

    // дебаунс resize
    let resizeTimer;

    changeTemplate();

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(changeTemplate, 100);
    });

});