// Замена текста на кнопке "В корзину"
document.addEventListener('DOMContentLoaded', function () {
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (
                mutation.type === 'attributes' &&
                mutation.attributeName === 'class' &&
                mutation.target.classList.contains('add_to_cart_button') &&
                mutation.target.classList.contains('added')
            ) {
                mutation.target.textContent = 'В корзине';
            }
        });
    });

    document.querySelectorAll('.add_to_cart_button').forEach(button => {
        observer.observe(button, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
});



// Кнопка поделиться
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: 'Посмотри этот товар!',
            url: window.location.href,
        })
        .then(() => console.log('Успешное выполнение функции share'))
        .catch((error) => console.log('Ошибка при выполнении функции share', error));
    }
}
