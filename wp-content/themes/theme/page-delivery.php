<?php
/* 
    Template Name: delivery
*/
?>

<?php get_header('home'); ?>


<main class="main delivery-price">

    <section class="selection">
        <div class="container">
            <div class="selection__inner">
                <div class="swiper selectionSwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <a class="selection__item selection__item--green " href="<? echo get_home_url(); ?>/product-category/green-prices/">
                                <p>ЗЕЛЕНЫЕ <br>ЦЕННИКИ</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_1.png" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">
                                <p>Молочная <br>продукция</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_2.png" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">
                                <p>Домашние <br>полуфабрикаты</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_3.png" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/kopchenosti/">
                                <p>Мясные <br>деликатесы</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_4.png" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/kolbasy/">
                                <p>Колбасные <br>изделия</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/selection_5.png" alt="">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/ovoshhi/">
                                <p>Овощи, фрукты, <br>грибы, ягоды</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/vegetables.svg" alt="Овощи, фрукты, грибы, ягоды">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">
                                <p>Ремесленные <br>сыры</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/cheese.svg" alt="Ремесленные сыры">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/yajczo/">
                                <p>Яйца</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/eggs.svg" alt="Яйца">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">
                                <p>Домашняя консервация</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/canned-food.svg" alt="Домашняя консервация">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/myaso/">
                                <p>Мясо и <br>рыба</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/meat.svg" alt="Мясо и рыба">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">
                                <p>Ремесленный <br>хлеб</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/bread.svg" alt="Ремесленный хлеб">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/bakaleya/">
                                <p>Бакалея</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/grocery.svg" alt="Бакалея">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/varene/">
                                <p>Варенье, <br>соки, компоты</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/jam.svg" alt="Варенье, соки, компоты">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/chaj-travy-i-dikorosy/">
                                <p>Чай и дикоросы</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/tea.svg" alt="Чай и дикоросы">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/med/">
                                <p>Мёд</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/med.svg" alt="Мёд">
                            </a>
                        </div>
                        <div class="swiper-slide">
                            <a class="selection__item" href="<? echo get_home_url(); ?>/product-category/">
                                <p>Кулинария</p>
                                <img src="<?php bloginfo('template_url') ?>/assets/img/selection/cooking.svg" alt="Кулинария">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="swiper-button-next selectionSwiper-next"></div>
                <div class="swiper-button-prev selectionSwiper-prev"></div>
            </div>
        </div>
    </section>

    <section class="delivery__breadcrumb">
        <div class="container">
            <?php
            if (function_exists('custom-breadcrumb')) {
                $args = array(
                    'delimiter'   => '<span class="delimiter">&gt;</span>',
                    'wrap_before' => '<nav class="woocommerce-breadcrumb">',
                    'wrap_after'  => '</nav>',
                    'home'        => _x('Магазин фермерских продуктов Ферма ДВ', 'breadcrumb', 'woocommerce'),
                );
                woocommerce_breadcrumb($args);
            } else {
                echo '<nav class="custom-breadcrumb">';
                echo '<a href="' . esc_url(home_url('/')) . '">Магазин фермерских продуктов Ферма ДВ</a>';
                echo '<span class="delimiter">&gt;</span>';
                echo '<span class="custom-breadcrumb__this">' . get_the_title() . '</span>';
                echo '</nav>';
            }
            ?>
        </div>
    </section>

    <div class="delivery-price__body">
        <div class="container">
            <div class="delivery-price__inner">
                <h1 class="page-title">ДОСТАВКА И ОПЛАТА</h1>
                <h5 class="delivery-price__subtitle page-title">ВЛАДИВОСТОК</h5>
                <div class="delivery-price__content">
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">По городу</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>349 руб.</p>
                                <p>до 1 000 руб.</p>
                                <p>с 18 до 22</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 1000 руб.</p>
                                <p>с 18 до 22</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 3.000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>349 руб.</p>
                                <p>от 1 500 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>399 руб.</p>
                                <p>до 1 500 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">о. Русский</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>500 руб.</p>
                                <p>от 5 000 до 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>800 руб.</p>
                                <p>до 5 000 руб</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">Пригород (от Зари до Весенней)</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 2000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 3 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>349 руб.</p>
                                <p>от 2 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>399 руб.</p>
                                <p>до 2 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">Де-Фриз</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 7 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>700</p>
                                <p>до 7 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>700</p>
                                <p>до 7 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">Трудовая и Артём</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>700</p>
                                <p>до 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>700</p>
                                <p>до 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="delivery-price__item">
                        <div class="delivery-price__item-header">
                            <p class="delivery-price__item-title">Шамора, Щитовая, Емар</p>
                            <button class="delivery-price__item-btn">
                                <img src="<?php bloginfo('template_url') ?>/assets/img/to_open_tab.svg" alt="">
                            </button>
                        </div>
                        <ul class="delivery-price__item-list">
                            <li class="delivery-price__item-row delivery-price__item-row--first">
                                <p>Стоимость</p>
                                <p>Сумма заказа</p>
                                <p>Время доставки</p>
                            </li>
                            <li class="delivery-price__item-row">
                                <p>Бесплатно</p>
                                <p>от 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 18 до 22</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>500</p>
                                <p>до 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 15 до 17</span>
                                </p>
                            </li>
							<li class="delivery-price__item-row">
                                <p>500</p>
                                <p>до 8 000 руб.</p>
                                <p class="delivery-price__item-spans">
                                    <span>с 10 до 12</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
                <br>
				<h5 class="delivery-price__subtitle page-title">ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ ПО РАБОТЕ СЛУЖБЫ ДОСТАВКИ ФЕРМА ДВ</h5>
				<div class="delivery-price__return">
                    <div class="delivery-price__return-item">
                        <p>
                            Доставки осуществляются в день заказа (за исключением случаев, когда Вы оформляете заказ или предзаказ на нужный Вам день)
                        </p>
                        <p>
                            Время оформления заказов и время отгрузки: 
								<p>* Заказы на доставку с 10:00 до 12:00 - принимаются до 8:00</p>
							<p>	* Заказы на доставку с 15:00 до 17:00 - принимаются до 13.00</p>
							<p>	* Заказы на доставку с 18:00 до 22:00 - принимаются до 16:30</p>

                        </p>
                        <p>
                            Заказы оформленные после 16:30 текущего дня будут отгружаться уже на следующий день (в удобный для Вас промежуток времени, стоимость доставки будет рассчитана исходя из условий, указанных на сайте и составлять от 0 до 399 руб)
                        </p>
						 <p>Заказы оформленные после 16:30 текущего дня могут быть доставленные в этот же день, но уже в рамках "Индивидуального заказа". Стоимость доставки таких заказов 399 руб </p>

                    </div>
                    <div class="delivery-price__return-item">
                        <p class="delivery-price__return-title">ПЕРСОНАЛЬНАЯ ДОСТАВКА</p>
                        <p>
                            Заказ на определенное время - осуществляется в рамках доставки "Персонального заказа". Стоимость доставки таких заказов 399 руб
                        </p>
                        
                    </div>
                </div>
                <div class="delivery-price__info">
                    <p>
                        Доставка заказов в пригород, остров Русский, Артем осуществляется только после полной оплаты
                        заказа.
                    </p>
                    <!--p class="delivery-price__info-bold">Доставка по г. Уссурийск осуществляется БЕСПЛАТНО.</p>
                    <p>
                        Доставка заказов по г. Уссурийск осуществляется в день заказа (или согласовывается с клиентом индивидуально).
                    </p-->
                </div>
                <h4 class="delivery-price__subtitle page-title green">ПРИЕМ ЗАКАЗОВ НА САЙТЕ КРУГЛОСУТОЧНО!</h4>
                <h5 class="delivery-price__subtitle page-title">ВОЗВРАТ ПРОДУКЦИИ</h5>
                <div class="delivery-price__return">
                    <div class="delivery-price__return-item">
                        <p>
                            Пожалуйста, проверяйте продукты при курьере. Вы можете вернуть отдельные позиции, не
                            устраивающие по качеству.
                        </p>
                        <p>
                            Если Вы приняли заказ, но недовольны качеством продуктов, просим Вас сразу же сообщить нам
                            об этом по телефону 8 (924) 128-21-38. Ваша заявка будет рассмотрена в порядке очереди в
                            течение двух суток в зависимости от сложности случая.
                        </p>
                        <p>
                            Компенсации подлежат обращения в рамках срока годности приобретенного товара.
                        </p>
                    </div>
                    <div class="delivery-price__return-item">
                        <p class="delivery-price__return-title">ТАКЖЕ МЫ ПРОСИМ ВАС:</p>
                        <p>
                            1.Обязательно сохранить полученный товар до выяснения обстоятельств обращения, даже если Вы
                            его уже приготовили.
                        </p>
                        <p>
                            2. Для сохранения состояния товара заморозить его по просьбе наших специалистов.
                        </p>
                        <p>
                            3. Направить на WhatsApp +7-908-441-11-10 фотографии и видео товара с указанием информации
                            об обращении и номера Вашего заказа.
                        </p>
                        <p>
                            4.При необходимости сохранить товар до приезда курьера для осуществления возврата.
                        </p>
                    </div>
                </div>
                <div class="delivery-price__additionally">
                    <p>Денежные средства за возврат товара будут возвращены на Вашу карту.</p>
                    <p>Пожалуйста, обращайте внимание на условия хранения и сроки годности товаров в Вашем заказе!</p>
                </div>
            </div>
        </div>
    </div>
     <section class="farm-scene">
        <div class="container">
            <div class="farm-scene__inner">
                <!-- Трактор -->
                <div class="farm-scene__left">
                    <img class="farm-scene__tractor"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/tractor.svg"
                        alt="Трактор" />
                    <img class="farm-scene__ground"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground.svg" alt="Дорога">
                    <img class="farm-scene__ground-mob"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground_mob.svg"
                        alt="Дорога">
                </div>

                <!-- Мельница: база и лопасти -->
                <div class="farm-scene__mid">
                    <img class="farm-scene__grinder"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/grinder.svg"
                        alt="Лопасти" />
                    <img class="farm-scene__mill-base"
                        src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/mill.svg"
                        alt="Мельница" />
                </div>

                <!-- Хлеб и корзина -->
                <div class="farm-scene__bread">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/bread.svg" alt="Хлеб" />
                </div>
                <div class="farm-scene__basket">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/cart.svg"
                        alt="Корзина" />
                </div>
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const items = document.querySelectorAll(".delivery-price__item");

        items.forEach((item) => {
            const header = item.querySelector(".delivery-price__item-header");
            const content = item.querySelector(".delivery-price__item-list");

            // Изначально скрыт
            content.style.maxHeight = "0";
            content.style.overflow = "hidden";
            content.style.transition = "max-height 0.4s ease";

            header.addEventListener("click", () => {
            const isOpen = content.style.maxHeight && content.style.maxHeight !== "0px";

            if (isOpen) {
                content.style.maxHeight = "0";
                item.classList.remove("delivery-price__item--open");
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                item.classList.add("delivery-price__item--open");
            }
            });
        });
    });
</script>

<?php get_footer('home'); ?>