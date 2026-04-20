// Слайдер статей
document.addEventListener('DOMContentLoaded', function () {
    var swiper = new Swiper(".blogSwiper", {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        navigation: {
            nextEl: ".blogSwiper-next",
            prevEl: ".blogSwiper-prev",
        },
    });
});