<?php
/* 
    Template Name: horeca
*/
?>

<?php get_header('home'); ?>


<main class="main horeca">

    <div class="horeca-banner">
        <div class="container">
            <h3 class="horeca-banner__title page-title">HoReCa</h3>
            <div class="horeca-banner__inner">
                <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/Грузовик.webp" alt="Грузовик">
                <p class="horeca-banner__inner-text">Поставка фермерских продуктов для компаний от Ферма ДВ</p>
                <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/Магазин.webp" alt="Магазин">
            </div>
        </div>
    </div>

    <section class="horeca-choice choice">
        <div class="container">
            <div class="choice__inner">
                <h3 class="choice__title page-title">ПОЧЕМУ НАС ВЫБИРАЮТ</h3>
                <div class="choice__content horeca-choice__content">
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_1.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Быстрая бесплатная доставка в удобное время
                        </p>
                    </div>
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_2.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Предоплата и отсрочка платежа до 30 дней
                        </p>
                    </div>
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_3.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Выгодные условия для оптовых заказов
                        </p>
                    </div>
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_4.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Качественные, свежие и вкусные продукты
                        </p>
                    </div>
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_5.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Удобный заказ через сайт или личного менеджера
                        </p>
                    </div>
                    <div class="horeca-choice__item">
                        <img src="<?php bloginfo('template_url') ?>/assets/img/horeca/horeca_choice_6.svg"
                            alt="Проверенные фермерские хозяйства">
                        <p class="horeca-choice__item-text">
                            Кешбэк на сумму заказов в конце месяца
                        </p>
                    </div>
                </div>
                <div class="horeca-choice__item horeca-choice__bottom">
                    <p class="horeca-choice__item-text">
                        Мы работаем только с местными производителями Приморья. Это преимущество, которое сможете
                        использовать и вы.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="reg-login">
            <a href="<? echo get_home_url(); ?>/my-account/" class="reg-login__btn">Зарегистрироваться / Войти</a>
        </div>
    </div>

    <section class="horeca-info">
        <div class="container">
            <div class="horeca-info__inner">
                <h5>Для малого и крупного бизнеса</h5>
                <p>Мы предлагаем высококачественные продукты для сегмента HORECA: отелей, кафе и ресторанов. Поставляем
                    как готовые и замороженные блюда, напитки, десерты, так и продукцию для дальнейшей переработки.
                    Мы обеспечиваем свежие и натуральные продукты, которые удовлетворяют самым высоким стандартам
                    индустрии гостеприимства.
                </p>
            </div>
        </div>
    </section>

    <section class="catalog">
        <div class="container">
            <div class="catalog__inner">
                <h3 class="catalog__title page-title">АССОРТИМЕНТ ПРОДУКЦИИ</h3>
                <div class="catalog__content">
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/molochnaya-produkcziya/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Молочная продукция.png"
                                alt="Молочная продукция">
                        </div>
                        <p class="catalog__item-title">Молочная продукция</p>
                    </a>
                    <a class="catalog__item"
                        href="<? echo get_home_url(); ?>/product-category/polufabrikaty-domashnie/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Домашние полуфабрикаты.png"
                                alt="Домашние полуфабрикаты">
                        </div>
                        <p class="catalog__item-title">Домашние полуфабрикаты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/kopchenosti/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мясные деликатесы.png"
                                alt="Мясные деликатесы">
                        </div>
                        <p class="catalog__item-title">Мясные деликатесы</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/kolbasy/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Колбасные изделия.png"
                                alt="Колбасные изделия">
                        </div>
                        <p class="catalog__item-title">Колбасные изделия</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/ovoshhi/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Овощи.png"
                                alt="Овощи, фрукты, грибы, ягоды">
                        </div>
                        <p class="catalog__item-title">Овощи, фрукты, грибы, ягоды</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/domashnie-syry/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Ремесленные сыры.png"
                                alt="Ремесленные сыры">
                        </div>
                        <p class="catalog__item-title">Ремесленные сыры</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/yajczo/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Яйцо домашнее.png"
                                alt="Яйцо домашнее">
                        </div>
                        <p class="catalog__item-title">Яйцо домашнее</p>
                    </a>
                    <a class="catalog__item"
                        href="<? echo get_home_url(); ?>/product-category/domashnyaya-konservacziya/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Домашняя консервация.png"
                                alt="Домашняя консервация">
                        </div>
                        <p class="catalog__item-title">Домашняя консервация</p>
                    </a>

                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/myaso/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мясо и рыба.jpg"
                                alt="Мясо и рыба">
                        </div>
                        <p class="catalog__item-title">Мясо и рыба</p>
                    </a>
                    <a class="catalog__item"
                        href="<? echo get_home_url(); ?>/product-category/remeslennyj-hleb-i-vypechka/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Ремесленный хлеб и выпечка.jpg"
                                alt="Ремесленный хлеб и выпечка">
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
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Чай.jpg"
                                alt="Чай, травы и дикоросы">
                        </div>
                        <p class="catalog__item-title">Чай, травы и дикоросы</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/varene/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Варенье.jpg"
                                alt="Варенье, соки, компоты">
                        </div>
                        <p class="catalog__item-title">Варенье, соки, компоты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/med/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Мёд.jpg" alt="Мёд">
                        </div>
                        <p class="catalog__item-title">Мёд</p>
                    </a>
                    <a class="catalog__item"
                        href="<? echo get_home_url(); ?>/product-category/tushenka-i-kashi-sobstvennoe-proizvodstvo/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Тушенки.jpg"
                                alt="Тушенки, консервы, каши">
                        </div>
                        <p class="catalog__item-title">Тушенки, консервы, каши</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/gotovaya-eda/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Готовая еда.jpg"
                                alt="Готовая еда">
                        </div>
                        <p class="catalog__item-title">Готовая еда</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/sladosti-i-deserty/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Сладости и десерты.jpg"
                                alt="Сладости и десерты">
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
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Подарки и сертификаты.jpg"
                                alt="Подарки и сертификаты">
                        </div>
                        <p class="catalog__item-title">Подарки и сертификаты</p>
                    </a>
                    <a class="catalog__item" href="<? echo get_home_url(); ?>/product-category/tovary-dlya-doma/">
                        <div class="catalog__item-img">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/catalog/Товары для дома.jpg"
                                alt="Товары для дома">
                        </div>
                        <p class="catalog__item-title">Товары для дома</p>
                    </a>
                </div>
            </div>
        </div>
    </section>

<section class="reviews">
    <div class="container">
        <div class="reviews__inner">
            <h3 class="reviews__title page-title">ОТЗЫВЫ НАШИХ КЛИЕНТОВ</h3>
            <div class="reviews__content">
                <div class="reviews__slider">
                    <div class="swiper reviewsSwiper">
                        <div class="swiper-wrapper">
                            <?php
                            // Параметры выборки: все посты из рубрики "reviews" (slug = reviews)
                            $args = [
                                'post_type'      => 'post',
                                'posts_per_page' => -1,           // все посты рубрики
                                'category_name'  => 'reviews',    // именно slug рубрики
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                            ];
                            $reviews_query = new WP_Query($args);

                            if ( $reviews_query->have_posts() ) :
                                while ( $reviews_query->have_posts() ) : $reviews_query->the_post();
                                    // Получаем ID текущего поста
                                    $post_id = get_the_ID();
                                    // Получаем миниатюру (featured image). Если её нет – можно вывести заглушку или пропустить запись.
                                    if ( has_post_thumbnail( $post_id ) ) {
                                        $thumb_url = get_the_post_thumbnail_url( $post_id, 'full' );
                                        $thumb_alt = get_the_title( $post_id );
                                    } else {
                                        // Вариант: если нужно, укажите путь до изображения-заглушки
                                        $thumb_url = get_template_directory_uri() . '/assets/img/placeholder.png';
                                        $thumb_alt = 'Без изображения';
                                    }
                                    ?>
                                    <div class="reviews__slider-item swiper-slide">
                                        <div class="reviews__slider-head">
                                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $thumb_alt ); ?>">
                                            <h5 class="reviews__slider-title"><?php the_title(); ?></h5>
                                        </div>
                                        <p class="reviews__slider-text">
                                            <?php the_field('reviews_text'); ?>
                                        </p>
                                    </div>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            else :
                                ?>
                                <div class="reviews__slider-item swiper-slide">
                                    <div class="reviews__slider-head">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.png" alt="Нет отзывов">
                                        <h5 class="reviews__slider-title">Пока нет отзывов</h5>
                                    </div>
                                    <p class="reviews__slider-text">
                                        Просим прощения, на данный момент у нас ещё нет отзывов от клиентов.
                                    </p>
                                </div>
                                <?php
                            endif;
                            ?>
                        </div> <!-- swiper-wrapper -->
                    </div> <!-- swiper .reviewsSwiper -->
                    <div class="reviews__slider-arrow swiper-button-next reviewsSwiper-next"></div>
                    <div class="reviews__slider-arrow swiper-button-prev reviewsSwiper-prev"></div>
                </div> <!-- reviews__slider -->
            </div> <!-- reviews__content -->
        </div> <!-- reviews__inner -->
    </div> <!-- container -->
</section>


    <section class="contact-us">
        <div class="container">
            <div class="contact-us__inner">
                <div class="contact-us__inner-container">
                    <div class="contact-us__text">
                        <h5 class="contact-us__text-title">Свяжитесь с нами, если остались вопросы</h5>
                        <p class="contact-us__text-descr">
                            Мы всегда готовы ответить на ваши вопросы и предложить лучшие
                            решения для вашего бизнеса. Звоните или пишите нам по указанным контактам.
                        </p>
                    </div>
                    <div class="contact-us__info">
                        <a href="tel:+79084411110" class="contact-us__info-item">+7 908 441-11-10</a>
                        <a href="mailto:zakaz@ferma-dv.ru" class="contact-us__info-item">zakaz@ferma-dv.ru</a>
                        <p class="contact-us__info-item">Ежедневно с 09:00 до 20:00</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <div class="faq__inner">
                <h3 class="faq__title page-title">FAQ</h3>
                <div class="faq__content">
                    <div class="faq__item">
                        <button class="faq__question" aria-expanded="false">
                            Какие условия доставки?
                            <div>
                                <span class="faq__icon" aria-hidden="true">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/arrow_down.svg" alt="">
                                </span>
                            </div>
                        </button>
                        <div class="faq__answer">
                            Доставка из магазинов бесплатная, но иногда мы устанавливаем минимальную сумму самого
                            заказа. Это происходит, когда большинство заказов — маленькие: раз в день по одному продукту
                            или несколько раз в неделю по 3-4 продукта. Лимиты позволяют нам сохранить доставку
                            бесплатной для всех покупателей
                        </div>
                    </div>

                    <div class="faq__item">
                        <button class="faq__question" aria-expanded="false">
                            Как осуществляется оплата?
                            <div>
                                <span class="faq__icon" aria-hidden="true">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/arrow_down.svg" alt="">
                                </span>
                            </div>
                        </button>
                        <div class="faq__answer">
                            Доставка из магазинов бесплатная, но иногда мы устанавливаем минимальную сумму самого
                            заказа. Это происходит, когда большинство заказов — маленькие: раз в день по одному продукту
                            или несколько раз в неделю по 3-4 продукта. Лимиты позволяют нам сохранить доставку
                            бесплатной для всех покупателей
                        </div>
                    </div>

                    <div class="faq__item">
                        <button class="faq__question" aria-expanded="false">
                            Как можно получить закрывающие документы?
                            <div>
                                <span class="faq__icon" aria-hidden="true">
                                    <img src="<?php bloginfo('template_url') ?>/assets/img/arrow_down.svg" alt="">
                                </span>
                            </div>
                        </button>
                        <div class="faq__answer">
                            Все документы: счета на оплату, договоры и УПД –
                            мы отправляем контрагентам в системе ЭДО (электронный документооборот)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer('home'); ?>