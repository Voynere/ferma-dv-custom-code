<?php
/**
 * Архив товаров (магазин и таксономии).
 * Категории: прежняя вёрстка темы (info_page, shop-ferma__archive, header-archive / footer-home), как в page.php.
 * Остальное: стандартная оболочка WooCommerce.
 *
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/** Только рубрики товаров — как в старом page.php; витрина магазина и др. таксономии без смены шапки/подвала. */
$ferma_cat_archive_skin = is_product_category();

if ( $ferma_cat_archive_skin ) {
	get_header( 'archive' );
} else {
	get_header( 'shop' );
}

if ( $ferma_cat_archive_skin ) {
	?>
	<div class="container info_page">
		<div class="row">
			<div class="col-12" style="text-align:center">
				<h1 class="page-title" style="text-align: start; margin: 0px !important;"><?php woocommerce_page_title(); ?></h1>
			</div>
			<div class="col-12 shop-ferma__related shop-ferma__archive">
	<?php
	do_action( 'woocommerce_archive_description' );
	$ferma_wc_cols = absint( wc_get_loop_prop( 'columns', 4 ) );
	if ( $ferma_wc_cols < 1 ) {
		$ferma_wc_cols = 4;
	}
	echo '<div class="woocommerce columns-' . esc_attr( (string) $ferma_wc_cols ) . '">';
} else {
	do_action( 'woocommerce_before_main_content' );
	?>
	<header class="woocommerce-products-header">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>
		<?php do_action( 'woocommerce_archive_description' ); ?>
	</header>
	<?php
}

if ( woocommerce_product_loop() ) {

	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	do_action( 'woocommerce_after_shop_loop' );
} else {
	do_action( 'woocommerce_no_products_found' );
}

if ( $ferma_cat_archive_skin ) {
	echo '</div>';
	?>
			</div>
		</div>
	</div>
	<?php
	get_footer( 'home' );
} else {
	do_action( 'woocommerce_after_main_content' );
	do_action( 'woocommerce_sidebar' );
	get_footer( 'shop' );
}
