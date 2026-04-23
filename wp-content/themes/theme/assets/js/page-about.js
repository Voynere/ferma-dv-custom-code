function fermaSafeSwiperOptions(selector, options) {
    var root = document.querySelector(selector);
    if (!root) {
        return options;
    }
    var slides = root.querySelectorAll(".swiper-slide").length;
    var safe = Object.assign({}, options);
    if (safe.loop) {
        var maxSlidesPerView = 1;
        var maxSlidesPerGroup = 1;

        var parseNum = function (value, fallback) {
            var num = Number(value);
            return Number.isFinite(num) && num > 0 ? num : fallback;
        };

        maxSlidesPerView = parseNum(safe.slidesPerView, maxSlidesPerView);
        maxSlidesPerGroup = parseNum(safe.slidesPerGroup, maxSlidesPerGroup);

        if (safe.breakpoints && typeof safe.breakpoints === "object") {
            Object.keys(safe.breakpoints).forEach(function (bp) {
                var cfg = safe.breakpoints[bp] || {};
                maxSlidesPerView = Math.max(maxSlidesPerView, parseNum(cfg.slidesPerView, 1));
                maxSlidesPerGroup = Math.max(maxSlidesPerGroup, parseNum(cfg.slidesPerGroup, 1));
            });
        }

        var requiredSlides = Math.max(2, maxSlidesPerView, maxSlidesPerGroup) + 1;
        if (slides < requiredSlides) {
            safe.loop = false;
        }
    }
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