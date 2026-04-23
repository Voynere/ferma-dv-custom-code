// Слайдер статей
function fermaSafeSwiperOptions(selector, options) {
    var root = document.querySelector(selector);
    if (!root) {
        return options;
    }
    var slides = root.querySelectorAll(".swiper-slide").length;
    var safe = Object.assign({}, options);
    if (safe.loop && slides < 2) {
        safe.loop = false;
    }
    return safe;
}

document.addEventListener('DOMContentLoaded', function () {
    var swiper = new Swiper(".blogSwiper", fermaSafeSwiperOptions(".blogSwiper", {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        navigation: {
            nextEl: ".blogSwiper-next",
            prevEl: ".blogSwiper-prev",
        },
    }));
});