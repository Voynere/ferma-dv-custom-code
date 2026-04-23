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

function fermaAlignSwiperNav(swiper) {
    if (!swiper || !swiper.el || !swiper.params || !swiper.params.navigation) {
        return;
    }

    var parent = swiper.el.parentElement;
    if (!parent) {
        return;
    }

    var parentRect = parent.getBoundingClientRect();
    var swiperRect = swiper.el.getBoundingClientRect();
    var centeredTop = (swiperRect.top - parentRect.top) + (swiperRect.height / 2);

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
    }
}

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
fermaBindSwiperNavAlignment(swiper);