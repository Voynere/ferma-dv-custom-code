<?php

/* 

    Template Name: home

*/

?>

<?php get_header('home'); ?>


<main class="main">

    <section class="home-slider">
        <div class="container">
            <div class="home-slider__inner">
                <div class="swiper homeSwiper">
                    <div class="swiper-wrapper">
                        <div class="home-slider__item swiper-slide">
                            <a href="https://ferma-dv.ru/dostavka/"><img src="<?php bloginfo('template_url') ?>/assets/img/home_slider/photo_2025-12-18_17-00-04-round-corners.png" alt="Бесплатная доставка"></a>
                        </div>
                        <div class="home-slider__item swiper-slide">
                            <a href="https://ferma-dv.ru/product-category/molochnaya-produkcziya/"><img src="<?php bloginfo('template_url') ?>/assets/img/home_slider/molochyiponedelniknew-round-corners.png" alt="Молочный понедельник -10% скидка"></a>
                        </div>
                        <div class="home-slider__item swiper-slide">
                            <a href="https://ferma-dv.ru/product-category/myaso/"><img src="<?php bloginfo('template_url') ?>/assets/img/home_slider/myasnoevoskresenie-corners.png" alt="Мясное воскресенье -10% скидка"></a>
                        </div>
                        <div class="home-slider__item swiper-slide">
                            <a href="https://ferma-dv.ru/product-category/kolbasy/"><img src="<?php bloginfo('template_url') ?>/assets/img/home_slider/kolbasy10-corners.png" alt="Колбасный вторник -10% скидка"></a>
                        </div>
                        <div class="home-slider__item swiper-slide">
                            <a href="https://ferma-dv.ru/bonusnaya-programma/"><img src="<?php bloginfo('template_url') ?>/assets/img/home_slider/burenka5-round-corners.png" alt="Бонусная программа Буренки"></a>
                        </div>
                        
                    </div>
                </div>
                <div class="swiper-button-next homeSwiper-next"></div>
                <div class="swiper-button-prev homeSwiper-prev"></div>
            </div>
        </div>
    </section>

    <section class="choice">
        <div class="container">
            <div class="choice__inner">
                <h3 class="choice__title page-title">ПОЧЕМУ НАС ВЫБИРАЮТ</h3>
                <div class="choice__content">
                    <div class="choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/choices_1.svg" alt="Проверенные фермерские хозяйства">
                        <p class="choice__item-text">
                            Проверенные фермерские хозяйства
                        </p>
                    </div>
                    <div class="choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/choices_2.svg" alt="Натуральная продукция">
                        <p class="choice__item-text">
                            Натуральная продукция
                        </p>
                    </div>
                    <div class="choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/choices_3.svg" alt="Рейтинг 4,9 на 2GIS 800+ отзывов">
                        <p class="choice__item-text">
                            Рейтинг 4,9 на 2GIS 800+ отзывов
                        </p>
                    </div>
                    <div class="choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/choices_4.svg" alt="Непрерывный контроль качества">
                        <p class="choice__item-text">
                            Непрерывный контроль качества
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="selection">
        <div class="container">
            <h3 class="selection__title page-title">ПОПУЛЯРНЫЕ КАТЕГОРИИ</h3>
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

    <button class="delivery-address open-modal1 open-modal__st">
        <img src="<?php bloginfo('template_url') ?>/assets/img/delivery-address.svg" alt="Адрес доставки">
        
        <? if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $row = get_user_meta( $user_id, 'delivery', true );
            if($row == '') {
                unset($row);
            }
            if (isset($row)) {
                if ($row == 1) {
                    echo '<p>Самовывоз:</p>';
                    $resultArray = get_user_meta( $user_id, 'billing_samoviziv', true );

                } 
                if ($row == 0) {
                    if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                        if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                            echo 'Экспресс-доставка';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                            echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                            echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                        } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                            echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                        }
                    } else {
                        echo 'Доставка:';
                    }
                    $cookieValue = get_user_meta( $user_id, 'billing_delivery', true );
                    $cookieArray = explode(',', $cookieValue);
                    $resultArray = array_slice($cookieArray, 2);
                    $resultArray = implode(',', $resultArray);
                }
            } else {
                echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                // $resultArray = 'Выберите способ получения';
            }
            } else {
                $row = $_COOKIE['delivery'];
                if (isset($row)) {
                    if ($row == 1) {
                        echo '<p>Самовывоз:</p>';
                        $resultArray = $_COOKIE['billing_samoviziv'];
                    } 
                    if ($row == 0) {
                        if(isset($_COOKIE['delivery_time']) && isset($_COOKIE['delivery_day'])) {
                            if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "express") {
                                echo 'Экспресс-доставка';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Доставка с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Доставка с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "today" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Доставка с&nbsp;19&nbsp;до&nbsp;22';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "morning") {
                                echo 'Завтра с&nbsp;10&nbsp;до&nbsp;12';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "day") {
                                echo 'Завтра с&nbsp;15&nbsp;до&nbsp;17';
                            } else if($_COOKIE['delivery_day'] == "tomorrow" && $_COOKIE['delivery_time'] == "evening") {
                                echo 'Завтра с&nbsp;19&nbsp;до&nbsp;22';
                            }
                        } else {
                            echo '<p>Доставка:</p>';
                        }
                        $cookieValue = $_COOKIE['billing_delivery'];
                        $cookieArray = explode(',', $cookieValue);
                        $resultArray = array_slice($cookieArray, 2);
                        $resultArray = implode(',', $resultArray);
                    }
                } else {
                    echo '<p><span>ВЫБРАТЬ АДРЕС </span>для доставки</p>';
                    // $resultArray = 'Выберите способ получения';
                    
                }
            }
            ?>
            <p class="delivery-address__result"><span><?echo $resultArray;?></span></p>
    </button>

    <section class="catalog">
        <div class="container">
            <div class="catalog__inner">
                <h3 class="catalog__title page-title">КАТАЛОГ</h3>
                <div class="catalog__content">
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Молочная продукция.png" alt="Молочная продукция">
                        </div>
                        <p class="catalog__item-title">Молочная продукция</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Домашние полуфабрикаты.png" alt="Домашние полуфабрикаты">
                        </div>
                        <p class="catalog__item-title">Домашние полуфабрикаты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/kopchenosti/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мясные деликатесы.png" alt="Мясные деликатесы">
                        </div>
                        <p class="catalog__item-title">Мясные деликатесы</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/kolbasy/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Колбасные изделия.png" alt="Колбасные изделия">
                        </div>
                        <p class="catalog__item-title">Колбасные изделия</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/ovoshhi/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Овощи.png" alt="Овощи, фрукты, грибы, ягоды">
                        </div>
                        <p class="catalog__item-title">Овощи, фрукты, грибы, ягоды</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Ремесленные сыры.png" alt="Ремесленные сыры">
                        </div>
                        <p class="catalog__item-title">Ремесленные сыры</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/yajczo/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Яйцо домашнее.png" alt="Яйцо домашнее">
                        </div>
                        <p class="catalog__item-title">Яйцо домашнее</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Домашняя консервация.png" alt="Домашняя консервация">
                        </div>
                        <p class="catalog__item-title">Домашняя консервация</p>
                    </a>

                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/myaso/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мясо и рыба.jpg" alt="Мясо и рыба">
                        </div>
                        <p class="catalog__item-title">Мясо и рыба</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Ремесленный хлеб и выпечка.jpg" alt="Ремесленный хлеб и выпечка">
                        </div>
                        <p class="catalog__item-title">Ремесленный хлеб и выпечка</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/bakaleya/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Бакалея.jpg" alt="Бакалея">
                        </div>
                        <p class="catalog__item-title">Бакалея</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/chaj-travy-i-dikorosy/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Чай.jpg" alt="Чай, травы и дикоросы">
                        </div>
                        <p class="catalog__item-title">Чай, травы и дикоросы</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/varene/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Варенье.jpg" alt="Варенье, соки, компоты">
                        </div>
                        <p class="catalog__item-title">Варенье, соки, компоты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/med/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мёд.jpg" alt="Мёд">
                        </div>
                        <p class="catalog__item-title">Мёд</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/tushenka-i-kashi-sobstvennoe-proizvodstvo/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Тушенки.jpg" alt="Тушенки, консервы, каши">
                        </div>
                        <p class="catalog__item-title">Тушенки, консервы, каши</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/gotovaya-eda/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Готовая еда.jpg" alt="Готовая еда">
                        </div>
                        <p class="catalog__item-title">Готовая еда</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/sladosti-i-deserty/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Сладости и десерты.jpg" alt="Сладости и десерты">
                        </div>
                        <p class="catalog__item-title">Сладости и десерты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/bady/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Бады.jpg" alt="Бады">
                        </div>
                        <p class="catalog__item-title">Бады</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/podarochnye-nabory/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Подарки и сертификаты.jpg" alt="Подарки и сертификаты">
                        </div>
                        <p class="catalog__item-title">Подарки и сертификаты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/tovary-dlya-doma/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Товары для дома.jpg" alt="Товары для дома">
                        </div>
                        <p class="catalog__item-title">Товары для дома</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="article">
        <div class="container">
            <div class="article__inner">
                <h3 class="catalog__title page-title">Фермерский блог</h3>
                <?php
                $args = array(
                    'post_type'      => 'post',
                    'cat'            => function_exists( 'ferma_get_farmer_blog_category_id' ) ? ferma_get_farmer_blog_category_id() : 200,
                    'posts_per_page' => 12,
                );
                $blog_query = new WP_Query( $args );
                ?>

                <?php if ( $blog_query->have_posts() ) : ?>
                    <div class="swiper articleSwiper">
                        <div class="swiper-wrapper">
                            <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                                <div class="article__item swiper-slide">
                                    <div class="article__item-img">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail( 'medium' ); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <img src="<?php bloginfo( 'template_url' ); ?>/assets/img/article_placeholder.png" alt="<?php the_title_attribute(); ?>">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="article__item-container">
                                        <div class="article__item-top">
                                            <div class="article__item-info">
                                                <p>Статья</p>
                                                <p>
                                                    <?php echo get_the_date( 'd.m.y' ); ?>
                                                </p>
                                            </div>
                                            <?php
                                                $trimmed_title = wp_trim_words( get_the_title(), 6, '…' );
                                            ?>
                                            <a class="article__item-title" href="<?php the_permalink(); ?>">
                                                <h4><?php echo esc_html( $trimmed_title ); ?></h4>
                                            </a>
                                        </div>
                                        <div class="article__item-bot">
                                            <a class="article__item-read btn-green" href="<?php the_permalink(); ?>">
                                                Читать
                                            </a>
                                            <div class="article__item-comments">
                                                <img src="<?php bloginfo('template_url'); ?>/assets/img/comments.svg" alt="Комментарии">
                                                <p><?php echo get_comments_number(); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Нет доступных записей в разделе "Фермерский блог".</p>
                <?php endif; ?>
                <div class="swiper-button-next articleSwiper-next"></div>
                <div class="swiper-button-prev articleSwiper-prev"></div>
            </div>
        </div>
    </section>

    <section class="recipes">
        <div class="container">
            <div class="recipes__inner">
                <h3 class="recipes__title page-title">РЕЦЕПТЫ</h3>
                <?php
                // 4 последних поста рубрики "recipe"
                $recipes_args = array(
                    'post_type'      => 'post',
                    'category_name'  => 'recipe',
                    'posts_per_page' => 4,
                );
                $recipes_query = new WP_Query( $recipes_args );
                ?>

                <?php if ( $recipes_query->have_posts() ) : ?>
                    <div class="recipes__content">
                        <?php while ( $recipes_query->have_posts() ) : $recipes_query->the_post(); ?>
                            <div class="recipes__item">
                                <div class="recipes__item-img">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'medium' ); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php bloginfo('template_url'); ?>/assets/img/recipes/recipe_placeholder.png" alt="<?php the_title_attribute(); ?>">
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php
                                    $trimmed_title = wp_trim_words( get_the_title(), 6, '…' );
                                ?>
                                <h4 class="recipes__item-title">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html( $trimmed_title ); ?></a>
                                </h4>
                                <a class="recipes__item-watch btn-green" href="<?php the_permalink(); ?>">
                                    Смотреть рецепт
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p>Пока нет доступных рецептов.</p>
                <?php endif; ?>
                <div class="recipes__mobile">
                    <div class="recipes__item">
                        <div class="recipes__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/recipes/recipe.jpg">
                        </div>
                        <a class="recipes__item-watch btn-green" href="<? echo get_home_url(); ?>/category/recipe/">Перейти в рецепты</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="delivery">
        <div class="container">
            <div class="delivery__inner">
                <div class="delivery__left">
                    <h4>ДОСТАВКА ДЛЯ КОМПАНИЙ</h4>
                </div>
                <div class="delivery__right">
                    <p>
                        Заказывайте с доставкой по России вкусные и качественные продукты
                        от Ферма-ДВ, как юридическое лицо или ИП.
                    </p>
                    <a class="delivery__right-more btn-green" href="<? echo get_home_url(); ?>/horeca/">Подробнее</a>
                </div>
            </div>
        </div>
    </section>

    <section class="supplier">
        <div class="container">
            <div class="supplier__inner">
                <h3 class="supplier__title page-title">НАШИ ПОСТАВЩИКИ</h3>
                <div class="supplier__slider">
                    <?php
                    $sup_args = array(
                        'post_type'      => 'post',
                        'category_name'  => 'suppliers',
                        'posts_per_page' => 12,
                    );
                    $sup_query = new WP_Query( $sup_args );
                    ?>

                    <?php if ( $sup_query->have_posts() ) : ?>
                        <div class="swiper supplierSwiper">
                            <div class="swiper-wrapper">
                                <?php while ( $sup_query->have_posts() ) : $sup_query->the_post(); ?>
                                    <div class="supplier__item swiper-slide">
                                        <div class="supplier__item-img">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                                            <?php else : ?>
                                                <a href="<?php the_permalink(); ?>"><img src="<?php bloginfo('template_url'); ?>/assets/img/supplier/supplier_placeholder.png" alt="<?php the_title_attribute(); ?>"></a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="supplier__item-content">
                                            <div class="supplier__item-top">
                                                <p class="supplier__item-name"><?php the_title(); ?></p>
                                                <p><?php the_field('tip_postavki'); ?></p>
                                            </div>
                                            <a class="supplier__item-more btn-green" href="<?php the_permalink(); ?>">Подробнее</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
                    <div class="swiper-button-next supplierSwiper-next"></div>
                    <div class="swiper-button-prev supplierSwiper-prev"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="questions">
        <div class="container">
            <div class="questions__inner">
                <h3 class="questions__title page-title">ОТВЕЧАЕМ НА ВАШИ ВОПРОСЫ</h3>
                <div class="questions__content">
                    <div class="questions__item">
                        <h5 class="questions__item-title">1. Как работает доставка?</h5>
                        <p class="questions__item-text">
                            Доставка осуществляется ежедневно в три временных промежутка:<br>
                            - с 10 до 12<br>
                            - с 15 до 16:30<br>
                            - с 18 до 22 (действует Бесплатная доставка)
                        </p>
                    </div>
                    <div class="questions__item">
                        <h5 class="questions__item-title">2. В каких городах Вы доставляете свою продукцию?</h5>
                        <p class="questions__item-text">
                            Доставка продуктов работает в следующих городах:<br>
                            - г. Владивосток (в том числе остров Русский, пригород Владивостока)<br>
                            - г. Артем и близлежащие населенных пункты<br>
                        </p>
                    </div>
                    <div class="questions__item">
                        <h5 class="questions__item-title">3. Есть ли бесплатная доставка продуктов? </h5>
                        <p class="questions__item-text">
                            Бесплатная доставка действует ежедневно и работает она в следующих условиях:<br>
                            - Бесплатная доставка действует в вечернее время при заказе от 2000 руб в пределах города Владивосток
                            <br>
                        </p>
                    </div>
                    <div class="questions__item">
                        <h5 class="questions__item-title">4. Ассортимент нашего магазина</h5>
                        <p class="questions__item-text">
                            Магазин фермерских продуктов — прямой партнер более 200 хозяйств Приморского края. Мы
                            помогаем найти своих покупателей эко продуктам, произведенным на небольших фермах.
                            Познакомиться с каждым фермером Вы можете в разделе Поставщики.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter">
        <div class="container">
            <div class="newsletter__inner">
                <div class="newsletter__left">
                    <p class="newsletter__left-title">ПОДПИСЫВАЙСЯ <br>НА РАССЫЛКУ</p>
                    <p>Скидки и акции только для подписчиков</p>
                </div>
                <div class="newsletter__right">
                    <form class="newsletter__right-form">
                        <input type="email" placeholder="Ваш e-mail">
                        <button class="btn-green">Подписаться</button>
                    </form>
                    <p class="newsletter__right-text">
                        Нажимая на кнопку «Подписаться», вы соглашаетесь с <a href="#">офертой</a>
                        и <a href="<? echo get_home_url(); ?>/privacy/">политикой конфиденциальности.</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="farm-scene">
        <div class="container">
            <div class="farm-scene__inner">
                <!-- Трактор -->
                <div class="farm-scene__left">
                    <img class="farm-scene__tractor" src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/tractor.svg" alt="Трактор" />
                    <img class="farm-scene__ground" src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground.svg" alt="Дорога">
                    <img class="farm-scene__ground-mob" src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground_mob.svg" alt="Дорога">
                </div>

                <!-- Мельница: база и лопасти -->
                <div class="farm-scene__mid">
                    <img class="farm-scene__grinder" src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/grinder.svg" alt="Лопасти" />
                    <img class="farm-scene__mill-base" src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/mill.svg" alt="Мельница" />
                </div>

                <!-- Хлеб и корзина -->
                <div class="farm-scene__bread">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/bread.svg" alt="Хлеб" />
                </div>
                <div class="farm-scene__basket">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/cart.svg" alt="Корзина" />
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer('home'); ?>