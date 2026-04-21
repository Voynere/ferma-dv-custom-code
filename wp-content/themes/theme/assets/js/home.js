// Слайдер баннера на главной (в разметке: .homeSwiper + .homeSwiper-next/prev)
var homeHeroSwiper = new Swiper(".homeSwiper", {
    slidesPerView: 1,
    spaceBetween: 16,
    loop: true,
    speed: 450,
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
});

// Слайдер с подборками продукции
var swiper = new Swiper(".selectionSwiper", {
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
});

// Слайдер статей
var swiper = new Swiper(".articleSwiper", {
    slidesPerView: 2,
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".articleSwiper-next",
        prevEl: ".articleSwiper-prev",
    },
});

// Слайдер с поставщиками
var swiper = new Swiper(".supplierSwiper", {
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
});

// Мобильный баннер в шапке (header-home): .bannerSwiper
if (document.querySelector(".bannerSwiper")) {
    var bannerHeaderSwiper = new Swiper(".bannerSwiper", {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        navigation: {
            nextEl: ".bannerSwiper-next",
            prevEl: ".bannerSwiper-prev",
        },
    });
}