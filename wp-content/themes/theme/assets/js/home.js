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

function fermaCreateSwiper(selector, options) {
    if (typeof Swiper === "undefined") {
        return null;
    }
    if (!document.querySelector(selector)) {
        return null;
    }
    return new Swiper(selector, fermaSafeSwiperOptions(selector, options));
}

function fermaAlignSwiperNav(swiper) {
    if (!swiper || !swiper.el || !swiper.params || !swiper.params.navigation) {
        return;
    }

    var parent = swiper.el.parentElement;
    if (!parent) {
        return;
    }

    var parentRect = parent.getBoundingClientRect();
    var target = swiper.el.querySelector(".swiper-slide-active img, .swiper-slide-active .selection__item, .swiper-slide-active .supplier__item, .swiper-slide-active .article__item, .swiper-slide-active");
    var targetRect = target ? target.getBoundingClientRect() : swiper.el.getBoundingClientRect();
    var centeredTop = (targetRect.top - parentRect.top) + (targetRect.height / 2);

    var selectors = [swiper.params.navigation.nextEl, swiper.params.navigation.prevEl];
    selectors.forEach(function (selector) {
        if (!selector) {
            return;
        }

        var button = document.querySelector(selector);
        if (!button) {
            return;
        }

        button.style.top = centeredTop + "px";
        button.style.bottom = "auto";
        button.style.transform = "translateY(-50%)";
        button.style.marginTop = "0";
    });
}

function fermaBindSwiperNavAlignment(swiper) {
    if (!swiper) {
        return;
    }

    var update = function () {
        fermaAlignSwiperNav(swiper);
    };

    requestAnimationFrame(update);
    window.addEventListener("load", update);
    window.addEventListener("resize", update);

    if (swiper.on) {
        swiper.on("resize", update);
        swiper.on("breakpoint", update);
        swiper.on("observerUpdate", update);
        swiper.on("imagesReady", update);
        swiper.on("slideChange", update);
        swiper.on("transitionEnd", update);
    }
}

// Слайдер баннера на главной (в разметке: .homeSwiper + .homeSwiper-next/prev)
var homeHeroSwiper = fermaCreateSwiper(".homeSwiper", {
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
});
fermaBindSwiperNavAlignment(homeHeroSwiper);

// Слайдер с подборками продукции
var swiper = fermaCreateSwiper(".selectionSwiper", {
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
fermaBindSwiperNavAlignment(swiper);

// Слайдер статей
var swiper = fermaCreateSwiper(".articleSwiper", {
    slidesPerView: 2,
    spaceBetween: 24,
    loop: true,
    navigation: {
        nextEl: ".articleSwiper-next",
        prevEl: ".articleSwiper-prev",
    },
});
fermaBindSwiperNavAlignment(swiper);

// Слайдер с поставщиками
var swiper = fermaCreateSwiper(".supplierSwiper", {
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
fermaBindSwiperNavAlignment(swiper);

// Мобильный баннер в шапке (header-home): .bannerSwiper
if (document.querySelector(".bannerSwiper")) {
    var bannerHeaderSwiper = fermaCreateSwiper(".bannerSwiper", {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        navigation: {
            nextEl: ".bannerSwiper-next",
            prevEl: ".bannerSwiper-prev",
        },
    });
}