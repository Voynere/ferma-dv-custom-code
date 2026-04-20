<?php
/* 
    Template Name: about
*/
?>

<?php get_header('home'); ?>


<main class="main about">

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

    <section class="about__breadcrumb">
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

    <section class="about__content">
        <div class="container">
            <div class="about__content-inner">
                <div class="about__title">
                    <h1 class="page-title">О НАС</h1>
                    <h2 class="page-title about__title-subtitle">ФЕРМА ДВ — ЭТО СЕТЬ МАГАЗИНОВ ФЕРМЕРСКИХ ПРОДУКТОВ.</h2>
                </div>
                <div class="about__content-item about__content-top">
                    <div class="about__textBlock">
                        <p class="about__textBlock-regular">
                            Первый магазин быт открыт ровно 5 лет назад. <br>
                            Наши магазины создавались с двумя простыми идеями.
                        </p>
                        <div class="about__textBlock-container">
                            <p class="about__textBlock-regular">Первая идея:</p>
                            <p class="about__textBlock-bold">
                                Мы хотим дать людям возможность купить качественные продукты с натуральными составами. Без
                                консервантов и ненужной химии. Для себя и своих близких.
                            </p>
                        </div>
                        <p class="about__textBlock-regular">
                            С этой идеей мы выбираем, с кем будем работать, и какие товары будут стоять на наших полках и в ваших холодильниках.
                        </p>
                        <div class="about__textBlock-container">
                            <p class="about__textBlock-regular">Наша вторая идея и ценность:</p>
                            <p class="about__textBlock-bold">
                                Помощь в реализации своей продукции местным фермерам и производителям.
                            </p>
                        </div>
                        <p class="about__textBlock-regular">
                            Фермеры, с которыми мы начинали работать более 4-х лет назад, развивались вместе с нами. У
                            многих за это время вырос ассортимент, расширились производство, появились новые сотрудники.
                            Ведь теперь каждый делает то, что может лучше всего: фермеры делают вкусные и качественные
                            продукты, а мы рассказываем о них Вам, продаём и доставляем на дом. География наших
                            поставщиков, даже в рамках Приморья очень обширная: есть, кто нам возит продукцию из
                            Находки, а есть и те, кто и из Лучегорска. В общем охватываем весь край с севера на юг. С
                            фермерами мы знакомим наших гостей у нас на сайте, в социальных сетях, нередко сами фермеры
                            приезжают к нам в магазины на дегустации и общаются с гостями, рассказывая о своей
                            продукции.
                        </p>
                    </div>
                    <div class="about__textBlock">
                        <p class="about__textBlock-bold">
                            И конечно же двери нашей сети открыты для новых поставщиков и фермеров!
                        </p>
                        <p class="about__textBlock-regular">
                            Форматы наших магазинов разные: есть и небольшие островки по 10-15 квадратов внутри сетевых
                            магазинов, и отдельностоящие магазины полного формата, в которых представленность и
                            ассортимент конечно выше, чем на островках.
                        </p>
                        <p class="about__textBlock-regular">Ферма ДВ сейчас — это:</p>
                        <ul class="about__textBlock-list">
                            <li class="about__textBlock-regular">
                                4 магазина во Владивостоке. В самых ближайших планах
                                запуск еще 3х новых локаций.
                            </li>
                            <li class="about__textBlock-regular">
                                Более 16 тыс гостей в наших магазинах ежемесячно.
                            </li>
                            <li class="about__textBlock-regular">
                                Более 2х тыс наименований в нашем ассортименте.
                            </li>
                            <li class="about__textBlock-regular">
                                Более 60 поставщиков и фермеров.
                            </li>
                            <li class="about__textBlock-regular">
                                <span>Бесплатная доставка продуктов домой в день заказа.</span> Доставка с каждым днем становиться
                                все более и более популярной, а самое главное, удобной! Нужно всего лишь перейти на сайт
								<span><a href="/">ferma-dv.ru</a></span> и оформить заказ, дальше уже наша задача все это Вам привезти.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="about__content-item">
                    <p class="about__textBlock-regular">Сейчас нас можно найти по следующим адресам:</p>
                    <div class="about__cities">
                        <div class="about__city">
                            <p class="about__textBlock-bold">Владивосток</p>
                            <ul class="about__textBlock-list">
                                <li class="about__textBlock-regular">ул. Верхнепортовая, 41в</li>
								<li class="about__textBlock-regular">ул. Народный проспект, 20 (ТЦ «РемиСити», островок внутри торгового зала)</li>
                                <li class="about__textBlock-regular">ул. Тимирязева, 31, стр. 1 (р-н Спутника, супермаркет Космос)</li>
                            </ul>
                        </div>
                        <!--div class="about__city">
                            <p class="about__textBlock-bold">Находка</p>
                            <ul class="about__textBlock-list">
                                <li class="about__textBlock-regular">ул. пр-т Мира, 65/10 (Сити-Центр, фермерский мини-рынок)</li>
                            </ul>
                        </div>
                        <div class="about__city">
                            <p class="about__textBlock-bold">Уссурийск</p>
                            <ul class="about__textBlock-list">
                                <li class="about__textBlock-regular">ул. Суханова, 52 (ТЦ «Москва», островок внутри супермаркета)</li>
                            </ul>
                        </div-->
                    </div>
                    <div class="about__map" style="margin-top: 32px; width: 100%; overflow: hidden;">
                        <a class="dg-widget-link" href="http://2gis.ru/vladivostok/profiles/70000001046565331,70000001063451663,70000001032443811/center/131.97724917903545,43.178743669386414/zoom/11?utm_medium=widget-source&utm_campaign=firmsonmap&utm_source=bigMap">Посмотреть на карте Владивостока</a>
                        <script charset="utf-8" src="https://widgets.2gis.com/js/DGWidgetLoader.js"></script>
                        <script charset="utf-8">
                            (function () {
                                var mapWidth = Math.max(320, Math.floor(document.currentScript.parentElement.clientWidth));

                                new DGWidgetLoader({
                                    "width": mapWidth,
                                    "height": 600,
                                    "borderColor": "#a3a3a3",
                                    "pos": {"lat": 43.178743669386414, "lon": 131.97724917903545, "zoom": 11},
                                    "opt": {"city": "vladivostok"},
                                    "org": [{"id": "70000001046565331"}, {"id": "70000001063451663"}, {"id": "70000001032443811"}]
                                });
                            })();
                        </script>
                        <noscript style="color:#c00;font-size:16px;font-weight:bold;">Виджет карты использует JavaScript. Включите его в настройках вашего браузера.</noscript>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="about__rule">
        <div class="container">
            <div class="about__rule-inner">
                <p class="about__rule-text">У нас есть одно золотое правило в компании:</p>
                <h5 class="about__rule-text about__rule-text--green">«Сначала пробуй, потом покупай!».</h5>
                <p class="about__rule-text">То есть Вы можете попробовать абсолютно любую продукцию перед покупкой!</p>
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


<?php get_footer('home'); ?>