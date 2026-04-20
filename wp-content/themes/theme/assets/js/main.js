document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.querySelector('.overlay');

    const menus = {
        catalog: {
            button: document.getElementById('catalog-mob'),
            menu: document.querySelector('.catalog-menu-mob'),
        },
        catalogDesc: {
            button: document.getElementById('catalog-desc-btn'),
            menu: document.getElementById('catalog-menu-desc'),
        },
        catalogFollow: {
            button: document.getElementById('catalog-follow-btn'),
            menu: document.getElementById('catalog-menu-desc'),
        },
        mob: {
            button: document.getElementById('mob-menu'),
            menu: document.getElementById('mob-menu-container'),
        },
        cart: {
            buttons: document.querySelectorAll('.cart-btn'),
            menu: document.querySelector('.cart'),
        },
        contacts: {
            button: document.getElementById('to-open-contacts'),
            menu: document.querySelector('.mob-contacts'),
        },
    };

    function closeAllMenus() {
        Object.values(menus).forEach(item => {
            if (item.menu) item.menu.classList.remove('active');
        });
        overlay && overlay.classList.remove('active');
    }

    function toggleMenu(item) {
        if (!item.menu) return;
        const isActive = item.menu.classList.contains('active');
        closeAllMenus();
        if (!isActive) {
            item.menu.classList.add('active');
            overlay && overlay.classList.add('active');
        }
    }

    Object.values(menus).forEach(item => {
        if (!item.menu) return;

        if (item.buttons && item.buttons.length) {
            item.buttons.forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    toggleMenu(item);
                });
            });
        }
        if (item.button) {
            item.button.addEventListener('click', e => {
                e.stopPropagation();
                toggleMenu(item);
            });
        }
    });

    // Закрытие по клику на оверлей
    overlay && overlay.addEventListener('click', e => {
        e.stopPropagation();
        closeAllMenus();
    });

    // Динамическое закрытие крестиком корзины
    document.body.addEventListener('click', e => {
        if (e.target.closest('.cart__close')) {
            e.stopPropagation();
            closeAllMenus();
        }
    });

    // Закрытие по Esc
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllMenus();
    });
});



// Строка поиска для моб устройств header-product: 
// при клике перемещается вверх и открывается overlay
document.addEventListener('DOMContentLoaded', function () {
    const searchBody = document.getElementById('hidden-mobile-search');
    const searchContainer = document.getElementById('search-mob');
    const searchButton = document.getElementById('open-search');
    const searchInput = searchContainer.querySelector('input');
    const body = document.body;
    let overlay = document.querySelector('.overlay');

    function openSearch() {
        searchBody.classList.add('active');
        searchContainer.classList.add('active');
        overlay.classList.add('active');
        body.classList.add('no-scroll');
        if (searchInput) searchInput.focus();
    }

    function closeSearch() {
        searchBody.classList.remove('active');
        searchContainer.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('no-scroll');
        if (searchInput) searchInput.blur();
    }

    searchButton.addEventListener('click', function (e) {
        e.stopPropagation();
        openSearch();
    });

    overlay.addEventListener('click', closeSearch);

    if (searchInput) {
        searchInput.addEventListener('blur', function () {
            setTimeout(closeSearch, 100);
        });
    }

    window.addEventListener('popstate', function () {
        if (searchContainer.classList.contains('active')) {
            closeSearch();
        }
    });
});

// Строка поиска для моб устройств header-home: 
document.addEventListener('DOMContentLoaded', function () {
    const searchContainer = document.getElementById('search-mob');
    const searchInput     = searchContainer.querySelector('input');
    const body            = document.body;
    let overlay           = document.querySelector('.overlay');

    function openSearch() {
        searchContainer.classList.add('active');
        overlay.classList.add('active');
        body.classList.add('no-scroll');       
        if (searchInput) searchInput.focus();
    }

    function closeSearch() {
        searchContainer.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('no-scroll');    
        if (searchInput) searchInput.blur();
    }

    searchContainer.addEventListener('click', function (e) {
        if (!searchContainer.classList.contains('active')) {
            e.stopPropagation();
            openSearch();
        }
    });

    overlay.addEventListener('click', closeSearch);

    if (searchInput) {
        searchInput.addEventListener('blur', function () {
            setTimeout(closeSearch, 100);
        });
    }
    window.addEventListener('popstate', function () {
        if (searchContainer.classList.contains('active')) {
            closeSearch();
        }
    });
});

// Кнопки количества товара
document.addEventListener('DOMContentLoaded', function () {
    const plusButtons = document.querySelectorAll('button.plus');
    const minusButtons = document.querySelectorAll('button.minus');

    plusButtons.forEach(btn => {
        btn.innerHTML = `
							<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<rect x="8.5" width="3" height="20" fill="currentColor"/>
								<rect y="8.5" width="20" height="3" fill="currentColor"/>
							</svg>
							`;
    });

    minusButtons.forEach(btn => {
        btn.innerHTML = `
							<svg width="20" height="3" viewBox="0 0 20 3" xmlns="http://www.w3.org/2000/svg">
								<rect width="20" height="3" fill="currentColor"/>
							</svg>
							`;
    });
});