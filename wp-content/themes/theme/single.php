<?php 
if ( is_singular('product') ) {
    get_header('product');
} else {
    get_header();
}
?>
<div class="container info_page">
    <div class="row">
        <div class="col-12">
            <?php the_post(); ?>
            <?php the_content(); ?>
        </div>
    </div>
</div>
<?php 
if ( is_singular('product') ) {
    ?>
        <div class="container banner-product">
            <div class="banner banner-desk">
                <div class="banner__item">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/banner_sale.svg" alt="Молочная продукция">
                    <p>Молочный понедельник - скидка 10%</p>
                </div>
                <div class="banner__item">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/myaso-akcii.svg" alt="Ферма ДВ">
                    <p>Мясное воскресенье - скидка 10%</p>
                </div>
            </div>
        </div>
        <section class="farm-scene">
            <div class="container">
                <div class="farm-scene__inner">
                    <!-- Трактор -->
                    <div class="farm-scene__left">
                        <img class="farm-scene__tractor"
                            src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/tractor.svg" alt="Трактор" />
                        <img class="farm-scene__ground"
                            src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground.svg" alt="Дорога">
                        <img class="farm-scene__ground-mob"
                            src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/ground_mob.svg" alt="Дорога">
                    </div>

                    <!-- Мельница: база и лопасти -->
                    <div class="farm-scene__mid">
                        <img class="farm-scene__grinder"
                            src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/grinder.svg" alt="Лопасти" />
                        <img class="farm-scene__mill-base"
                            src="<?php bloginfo('template_url') ?>/assets/img/animation_footer/mill.svg" alt="Мельница" />
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
<?php
} else {
}
?>

<?php 
if ( is_singular('product') ) {
    get_footer('home');
} else {
    get_footer();
}
?>