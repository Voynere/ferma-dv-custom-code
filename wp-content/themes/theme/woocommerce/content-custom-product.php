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

$sale_percent = get_field('priceint', 'option');
$check_ac = array_shift( wc_get_product_terms( $product->id, 'pa_akcziya', array( 'fields' => 'names' ) ) );
if ($sale_percent and $check_ac) {
	$sale_class = 'sale_class';
}


// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$discount = get_field('priceint', 'option');
$green_friday_products = get_green_friday_products();

$is_green_friday = false;

foreach($green_friday_products['good_ids_with_discount'] as $percent => $green_friday_product) {
	if(in_array($product->id, $green_friday_product)) {
		$discount = $percent;
		$is_green_friday = true;
	}
}

$end_date = strtotime(get_field('pricedate', 'option'));
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
