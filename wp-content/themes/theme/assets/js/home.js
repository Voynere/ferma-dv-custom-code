function fermaSafeSwiperOptions(selector, options) {
    var root = document.querySelector(selector);
    if (!root) {
        return options;
    }
    var slides = root.querySelectorAll(".swiper-slide").length;
    var safe = Object.assign({}, options);
    // Loop mode in Swiper requires enough slides; disable to avoid layout glitches.
    if (safe.loop && slides < 2) {
        safe.loop = false;
    }
    return safe;
}

// Слайдер баннера на главной (в разметке: .homeSwiper + .homeSwiper-next/prev)
var homeHeroSwiper = new Swiper(".homeSwiper", fermaSafeSwiperOptions(".homeSwiper", {
    slidesPerView: 1,
    spaceBetween: 16,
    loop: true,
    speed: 350,
    autoplay: {
        delay: 4500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
        stopOnLastSlide: false,
    },
    observer: true,
    observeParents: true,
    watchSlidesProgress: true,
    navigation: {
        nextEl: ".homeSwiper-next",
        prevEl: ".homeSwiper-prev",
    },
    keyboard: {
        enabled: true,
    },
}));

// Слайдер с подборками продукции
var swiper = new Swiper(".selectionSwiper", fermaSafeSwiperOptions(".selectionSwiper", {
    slidesPerView: 2,
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".selectionSwiper-next",
        prevEl: ".selectionSwiper-prev",
    },
    breakpoints: {
        568: {
            slidesPerView: 3,
            spaceBetween: 24,
        },
        1100: {
            slidesPerView: 4,
        },
        1330: {
            slidesPerView: 5,
        },
    },
}));

// Слайдер статей
var swiper = new Swiper(".articleSwiper", fermaSafeSwiperOptions(".articleSwiper", {
    slidesPerView: 2,
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".articleSwiper-next",
        prevEl: ".articleSwiper-prev",
    },
}));

// Слайдер с поставщиками
var swiper = new Swiper(".supplierSwiper", fermaSafeSwiperOptions(".supplierSwiper", {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".supplierSwiper-next",
        prevEl: ".supplierSwiper-prev",
    },
    breakpoints: {
        686: {
            slidesPerView: 2,
            spaceBetween: 24,
        },
    },
}));

// Мобильный баннер в шапке (header-home): .bannerSwiper
if (document.querySelector(".bannerSwiper")) {
    var bannerHeaderSwiper = new Swiper(".bannerSwiper", fermaSafeSwiperOptions(".bannerSwiper", {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        navigation: {
            nextEl: ".bannerSwiper-next",
            prevEl: ".bannerSwiper-prev",
        },
    }));
}