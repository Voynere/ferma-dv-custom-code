<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Theme
 */

?>


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
