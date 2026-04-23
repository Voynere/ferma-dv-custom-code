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

document.addEventListener('DOMContentLoaded', function () {
    var swiper = fermaCreateSwiper(".blogSwiper", {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        navigation: {
            nextEl: ".blogSwiper-next",
            prevEl: ".blogSwiper-prev",
        },
    });
    fermaBindSwiperNavAlignment(swiper);
});