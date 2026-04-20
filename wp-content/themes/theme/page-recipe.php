<?php
/* 
    Template Name: recipe
*/
?>

<?php get_header('home'); ?>

<main class="recipe">

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

    <section class="recipe__breadcrumb">
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

    <div class="recipe__body">
        <div class="container">
            <div class="recipe__inner">
                <h1 class="recipe__title page-title">РЕЦЕПТЫ</h1>
                <div class="recipe__content">
                    <?php
                    $args = array(
                        'post_type'      => 'post',
                        'category_name'  => 'recipe', 
                        'posts_per_page' => 8,    
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    );

                    $recipe_query = new WP_Query($args);

                    if ($recipe_query->have_posts()) :
                        while ($recipe_query->have_posts()) : $recipe_query->the_post();
                            $post_id = get_the_ID();
                    ?>
                            <div class="recipe__item">
                                <div class="recipe__item-img">
                                    <?php
                                    // Вывод изображения поста
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('medium', array('class' => 'recipe__img'));
                                    } else {
                                        echo '<img src="' . get_template_directory_uri() . '/images/placeholder.jpg" alt="Изображение рецепта" class="recipe__img">';
                                    }
                                    ?>
                                </div>
                                <div class="recipe__item-info">
                                    <h3 class="recipe__item-title page-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <p class="recipe__item-required page-title">Что потребуется</p>
                                    <div class="recipe__item-ingredients">
                                        <?php
                                        // Вывод ингредиентов
                                        $ingredients_shown = false;
                                        
                                        // выводим поля ингредиентов от 1 до 5
                                        for ($i = 1; $i <= 5; $i++) {
                                            $ingredient = get_field('recipe_ingredient_' . $i, $post_id);
                                            
                                            if (!empty($ingredient)) {
                                                echo '<p class="recipe__ingredient">' . esc_html($ingredient) . '</p>';
                                                $ingredients_shown = true;
                                            }
                                        }
                                        
                                        // Выводим многоточие только если есть хотя бы один ингредиент
                                        if ($ingredients_shown) {
                                            echo '<p class="recipe__item-ellipsis">. . .</p>';
                                        }
                                        ?>
                                    </div>
                                    <a class="recipe__item-read btn-green" href="<?php the_permalink(); ?>">Читать рецепт</a>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>Рецепты не найдены.</p>';
                    endif;
                    ?>
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

<?php get_footer('home'); ?>