<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

$sale_class = '';

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$product_id = $product->get_id();
$sale_percent = ferma_get_cached_option_field('priceint');
$check_ac = $product->get_attribute('pa_akcziya');
if ($sale_percent and $check_ac) {
	$sale_class = 'sale_class';
}

$discount = ferma_get_cached_option_field('priceint');
$green_friday_discount = ferma_get_green_friday_discount_for_product($product_id);
$is_green_friday = $green_friday_discount !== null;
if ($is_green_friday) {
	$discount = $green_friday_discount;
}

$end_date = strtotime(ferma_get_cached_option_field('pricedate'));
$price_tovar = $product->get_price();
$real_price = $product->get_regular_price();
if($price_tovar != $real_price) {
	$sale_class = 'sale_class';
}
$is_new = $product->get_attribute('pa_novinka');
$is_cooling = $product->get_attribute('pa_ohlazhdyonka');
$is_freezing = $product->get_attribute('pa_zamorozka');
?>
<!-- // Rating@Mail.ru counter dynamic remarketing appendix -->
<li <?php wc_product_class( 'ferma-product-card'.$sale_class.'', $product ); ?>>
	<?php
	if($price_tovar != $real_price) {
		echo '<div class="date-label">Скидка: ' . $discount . '%, действует до ' . date("d.m", $end_date) . '</div>';

	}
	if(!empty($is_cooling) || !empty($is_new) || !empty($is_freezing) || $is_green_friday) {
		echo '<div class="product-labels">';
		if(!empty($is_green_friday)) {
			echo '<span class="product-label green-friday-label">Зеленая пятница</span><br>';
		}
		if(!empty($is_new)) {
			echo '<span class="product-label new-label">Новинка</span><br>';
		}
		if(!empty($is_cooling)) {
			echo '<span class="product-label cooling-label">Охлажденка</span><br>';
		}
		if(!empty($is_freezing)) {
			echo '<span class="product-label freezing-label">Заморозка</span><br>';
		}
	
		echo '</div>';
	}
	?>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
    ?>
    <div class="ferma-product-card__img">
    <?php
    do_action( 'woocommerce_before_shop_loop_item_title' );
    ?>
    </div>
    <?php
	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );


	?>
	<div class="ferma-product-card__bottom">
	<?php
        do_action( 'woocommerce_after_shop_loop_item_title' );
    ?>

        <div class="ferma-product-card__add">
            <?php
                do_action( 'woocommerce_after_shop_loop_item' );
            ?>
        </div>
	</div>
</li>
