<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Theme
 */

get_header('home');
?>

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

	<div class="container">
        <div class="row">
            <div class="col-12">
                <section id="primary" class="content-area">
                    <main id="main" class="site-main">
            
                    <?php if ( have_posts() ) : ?>
            
                        <div class="shop-ferma__title-search">
                            <h1 class="page-title">
                            <?php do_shortcode('[woocommerce_product_search]'); ?>
                                <?php
                                /* translators: %s: search query. */
                                printf( esc_html__( 'Результаты поиска: %s', 'theme' ), '<span>' . get_search_query() . '</span>' );
                                ?>
                            </h1>
                        </div><!-- .page-header -->
                        <div class="col-12 shop-ferma__archive shop-ferma__related">
                            <div class="woocommerce columns-4 ">

                                <ul class="products columns-4">
                                    <?php
                                    /* Start the Loop */
                                    while ( have_posts() ) :
                                        the_post();
                    
                                        get_template_part( 'template-parts/content', 'search' );
                        
                                    endwhile;
                        
                                    the_posts_navigation();
                            
                                    else :
                            
                                        get_template_part( 'template-parts/content', 'none' );
                            
                                    endif;
                                    ?>
                                </ul>

                            </div>					
                        </div>
                    </main><!-- #main -->
                </section><!-- #primary -->
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
<?php

get_footer('home');
